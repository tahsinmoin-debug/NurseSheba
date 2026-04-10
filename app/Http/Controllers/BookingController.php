<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BookingController extends Controller
{
    public function create($nurseId)
    {
        $nurse = User::where('role', 'nurse')
            ->whereHas('nurseProfile', function ($query) {
                $query->where('is_approved', true)
                    ->where('availability', true);
            })
            ->with('nurseProfile')
            ->findOrFail($nurseId);

        $rates = config('service_rates.base_rates', []);

        return view('patient.book', compact('nurse', 'rates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nurse_id'        => 'required|exists:users,id',
            'date'            => 'required|date|after_or_equal:today',
            'time'            => 'required',
            'service_type'    => 'required|string|max:255',
            'service_address' => 'required|string|max:500',
            'duration_hours'  => 'required|numeric|min:1|max:24',
        ]);

        $nurse = User::whereKey($request->nurse_id)
            ->where('role', 'nurse')
            ->whereHas('nurseProfile', function ($query) {
                $query->where('is_approved', true)
                    ->where('availability', true);
            })
            ->first();

        if (!$nurse) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['nurse_id' => 'Selected nurse is not available for booking right now.']);
        }

        Booking::create([
            'patient_id'      => auth()->id(),
            'nurse_id'        => $request->nurse_id,
            'date'            => $request->date,
            'time'            => $request->time,
            'service_type'    => $request->service_type,
            'service_address' => $request->service_address,
            'duration_hours'  => $request->duration_hours,
            'status'          => 'pending',
        ]);

        return redirect()->route('patient.dashboard')->with('success', 'Booking request sent successfully!');
    }

    public function cancel(Booking $booking)
    {
        if ($booking->patient_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if (!in_array($booking->status, ['pending', 'accepted'], true)) {
            return redirect()->back()->with('error', 'Only active bookings can be cancelled.');
        }

        $booking->update(['status' => 'cancelled']);
        return redirect()->back()->with('success', 'Booking cancelled.');
    }

    public function accept(Booking $booking)
    {
        if ($booking->nurse_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending bookings can be accepted.');
        }

        $booking->update(['status' => 'accepted']);

        // Auto-create pending payment record when nurse accepts
        if (!$booking->payment) {
            $nurse      = $booking->nurse()->with('nurseProfile')->first();
            $experience = $nurse?->nurseProfile?->experience_years ?? 0;
            $duration   = (float) ($booking->duration_hours ?? 1);
            $cost       = Payment::calculateCost($booking->service_type, $experience, $duration);

            Payment::create([
                'booking_id'        => $booking->id,
                'invoice_number'    => Payment::generateInvoiceNumber(),
                'duration_hours'    => $duration,
                'nurse_hourly_rate' => $cost['hourly_rate'],
                'amount'            => $cost['total_amount'],
                'payment_status'    => 'unpaid',
            ]);
        }

        return redirect()->back()->with('success', 'Booking accepted. Patient will be notified to pay.');
    }

    public function reject(Booking $booking)
    {
        if ($booking->nurse_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($booking->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending bookings can be rejected.');
        }

        $booking->update(['status' => 'cancelled']);
        return redirect()->back()->with('success', 'Booking rejected.');
    }

    public function complete(Booking $booking)
    {
        if ($booking->nurse_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        if ($booking->status !== 'accepted') {
            return redirect()->back()->with('error', 'Only accepted bookings can be completed.');
        }

        // Ensure payment is made before marking complete
        if ($booking->payment && $booking->payment->payment_status !== 'paid') {
            return redirect()->back()->with('error', 'Cannot complete booking until patient has paid.');
        }

        $booking->update(['status' => 'completed']);
        return redirect()->back()->with('success', 'Booking marked as completed.');
    }

    public function adminUpdateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'accepted', 'completed', 'cancelled'])],
        ]);

        $booking->update([
            'status' => $validated['status'],
        ]);

        return redirect()->back()->with('success', 'Booking status updated successfully.');
    }
}
