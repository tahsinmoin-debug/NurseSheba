<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    // ─── Patient: Checkout Page ───────────────────────────────────────────────

    public function show(Booking $booking)
    {
        $this->authorizePatient($booking);

        // Must be accepted but unpaid
        abort_if($booking->status !== 'accepted', 403, 'Payment is only allowed for accepted bookings.');

        $payment = $booking->payment ?? $this->createPendingPayment($booking);

        if ($payment->payment_status === 'paid') {
            return redirect()->route('patient.invoice', $payment)->with('info', 'This booking has already been paid.');
        }

        $stripeKey = config('services.stripe.key');
        $rates     = config('service_rates.base_rates');

        return view('payment.checkout', compact('booking', 'payment', 'stripeKey', 'rates'));
    }

    // ─── Patient: Create Stripe PaymentIntent ─────────────────────────────────

    public function createStripeIntent(Request $request, Booking $booking)
    {
        $this->authorizePatient($booking);

        $payment = $booking->payment ?? $this->createPendingPayment($booking);

        if ($payment->payment_status === 'paid') {
            return response()->json(['error' => 'Already paid.'], 400);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        // Amount in paisa (BDT cents) — Stripe uses smallest currency unit
        $amountInPaisa = (int) round($payment->amount * 100);

        $intent = PaymentIntent::create([
            'amount'                    => $amountInPaisa,
            'currency'                  => 'bdt',
            'automatic_payment_methods' => ['enabled' => true],
            'metadata'                  => [
                'booking_id'     => $booking->id,
                'invoice_number' => $payment->invoice_number,
                'patient_email'  => $booking->patient->email,
            ],
        ]);

        // Store intent ID
        $payment->update(['stripe_payment_intent_id' => $intent->id]);

        return response()->json(['clientSecret' => $intent->client_secret]);
    }

    // ─── Patient: Stripe Success Callback ────────────────────────────────────

    public function stripeSuccess(Request $request, Booking $booking)
    {
        $this->authorizePatient($booking);

        $payment = $booking->payment;

        if (!$payment || $payment->payment_status === 'paid') {
            return redirect()->route('patient.invoice', $payment ?? abort(404));
        }

        // Verify with Stripe
        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $intent = PaymentIntent::retrieve($payment->stripe_payment_intent_id);
            if ($intent->status === 'succeeded') {
                $payment->update([
                    'payment_status' => 'paid',
                    'payment_method' => 'stripe',
                    'transaction_id' => $intent->id,
                    'paid_at'        => now(),
                ]);
                return redirect()->route('patient.invoice', $payment)->with('success', 'Payment successful! Your invoice is ready.');
            }
        } catch (\Exception $e) {
            return redirect()->route('patient.payment.show', $booking)->with('error', 'Payment verification failed: ' . $e->getMessage());
        }

        return redirect()->route('patient.payment.show', $booking)->with('error', 'Payment not completed. Please try again.');
    }

    // ─── Patient: Simulate Mobile Payment (bKash / Nagad) ────────────────────

    public function simulateMobile(Request $request, Booking $booking)
    {
        $this->authorizePatient($booking);

        $request->validate([
            'mobile_provider' => 'required|in:bkash,nagad',
            'mobile_number'   => 'required|regex:/^01[3-9]\d{8}$/',
        ]);

        $payment = $booking->payment ?? $this->createPendingPayment($booking);

        if ($payment->payment_status === 'paid') {
            return redirect()->route('patient.invoice', $payment)->with('info', 'Already paid.');
        }

        // Generate fake transaction ID
        $prefix = strtoupper($request->mobile_provider) === 'BKASH' ? 'BK' : 'NG';
        $fakeId  = $prefix . strtoupper(bin2hex(random_bytes(6)));

        $payment->update([
            'payment_status' => 'paid',
            'payment_method' => $request->mobile_provider,
            'transaction_id' => $fakeId,
            'paid_at'        => now(),
        ]);

        return redirect()->route('patient.invoice', $payment)->with('success', 'Payment successful via ' . ucfirst($request->mobile_provider) . '! Transaction ID: ' . $fakeId);
    }

    // ─── Patient: View Invoice ────────────────────────────────────────────────

    public function invoice(Payment $payment)
    {
        $booking = $payment->booking()->with(['patient', 'nurse', 'nurse.nurseProfile'])->firstOrFail();

        // Allow patient, nurse of this booking, or admin
        $user = auth()->user();
        $allowed = $user->role === 'admin'
            || $user->id === $booking->patient_id
            || $user->id === $booking->nurse_id;

        abort_if(!$allowed, 403);

        return view('payment.invoice', compact('payment', 'booking'));
    }

    // ─── Patient: Payment History ─────────────────────────────────────────────

    public function patientHistory()
    {
        $payments = Payment::whereHas('booking', function ($q) {
            $q->where('patient_id', auth()->id());
        })
        ->with(['booking', 'booking.nurse', 'booking.nurse.nurseProfile'])
        ->latest()
        ->paginate(15);

        return view('payment.history', compact('payments'));
    }

    // ─── Nurse: Earnings Summary ──────────────────────────────────────────────

    public function nurseEarnings()
    {
        $nurseId = auth()->id();

        $payments = Payment::whereHas('booking', function ($q) use ($nurseId) {
            $q->where('nurse_id', $nurseId);
        })
        ->with(['booking', 'booking.patient'])
        ->latest()
        ->get();

        $totalEarned   = $payments->where('payment_status', 'paid')->sum('amount');
        $pendingAmount = $payments->where('payment_status', 'unpaid')->sum('amount');
        $totalBookings = $payments->count();

        // Monthly summary (last 6 months)
        $monthlySummary = $payments->where('payment_status', 'paid')
            ->groupBy(fn($p) => $p->paid_at?->format('Y-m'))
            ->map(fn($group) => [
                'month'  => $group->first()->paid_at->format('M Y'),
                'total'  => $group->sum('amount'),
                'count'  => $group->count(),
            ])
            ->take(6);

        return view('payment.earnings', compact(
            'payments', 'totalEarned', 'pendingAmount', 'totalBookings', 'monthlySummary'
        ));
    }

    // ─── Admin: Refund ────────────────────────────────────────────────────────

    public function refund(Payment $payment)
    {
        abort_if(auth()->user()->role !== 'admin', 403);
        abort_if($payment->payment_status !== 'paid', 400, 'Only paid payments can be refunded.');

        // Attempt real Stripe refund if paid via Stripe
        if ($payment->payment_method === 'stripe' && $payment->stripe_payment_intent_id) {
            try {
                Stripe::setApiKey(config('services.stripe.secret'));
                \Stripe\Refund::create(['payment_intent' => $payment->stripe_payment_intent_id]);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Stripe refund failed: ' . $e->getMessage());
            }
        }

        $payment->update(['payment_status' => 'refunded']);
        return redirect()->back()->with('success', 'Payment #' . $payment->invoice_number . ' has been refunded.');
    }

    // ─── Private Helpers ──────────────────────────────────────────────────────

    private function authorizePatient(Booking $booking): void
    {
        abort_if($booking->patient_id !== auth()->id(), 403, 'Unauthorized.');
    }

    private function createPendingPayment(Booking $booking): Payment
    {
        $nurse       = $booking->nurse()->with('nurseProfile')->first();
        $experience  = $nurse?->nurseProfile?->experience_years ?? 0;
        $duration    = (float) ($booking->duration_hours ?? 1);
        $cost        = Payment::calculateCost($booking->service_type, $experience, $duration);

        return Payment::create([
            'booking_id'       => $booking->id,
            'invoice_number'   => Payment::generateInvoiceNumber(),
            'duration_hours'   => $duration,
            'nurse_hourly_rate' => $cost['hourly_rate'],
            'amount'           => $cost['total_amount'],
            'payment_status'   => 'unpaid',
        ]);
    }
}
