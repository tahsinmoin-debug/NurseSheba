<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    /**
     * Show the complaint form for a specific booking.
     */
    public function create($bookingId)
    {
        $user = auth()->user();
        $booking = Booking::with(['patient', 'nurse', 'nurse.nurseProfile'])->findOrFail($bookingId);

        // Ensure the user is part of this booking
        if ($booking->patient_id !== $user->id && $booking->nurse_id !== $user->id) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        $complaintTypes = Complaint::COMPLAINT_TYPES;

        return view('complaints.create', compact('booking', 'complaintTypes'));
    }

    /**
     * Store a new complaint.
     */
    public function store(Request $request)
    {
        $request->validate([
            'booking_id'     => 'required|exists:bookings,id',
            'complaint_type' => 'required|string|in:' . implode(',', Complaint::COMPLAINT_TYPES),
            'message'        => 'required|string|max:2000',
        ]);

        $user = auth()->user();
        $booking = Booking::findOrFail($request->booking_id);

        // Determine roles
        if ($user->id === $booking->patient_id) {
            $reporterRole = 'patient';
            $nurseId = $booking->nurse_id;
        } elseif ($user->id === $booking->nurse_id) {
            $reporterRole = 'nurse';
            $nurseId = $booking->patient_id; // When nurse reports, "nurse_id" field stores the reported user
        } else {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        // Check for duplicate complaint on same booking by same user
        $existing = Complaint::where('user_id', $user->id)
            ->where('booking_id', $booking->id)
            ->first();

        if ($existing) {
            return redirect()->back()->with('error', 'You have already filed a complaint for this booking.');
        }

        Complaint::create([
            'user_id'        => $user->id,
            'nurse_id'       => $nurseId,
            'complaint_type' => $request->complaint_type,
            'booking_id'     => $booking->id,
            'message'        => $request->message,
            'status'         => 'open',
            'reporter_role'  => $reporterRole,
        ]);

        $redirectRoute = $reporterRole === 'patient' ? 'patient.complaints' : 'nurse.complaints';

        return redirect()->route($redirectRoute)->with('success', 'Complaint submitted successfully. Admin will review it shortly.');
    }

    /**
     * Show the current user's filed complaints with admin replies.
     */
    public function myComplaints()
    {
        $user = auth()->user();

        $filed = $user->complaintsFiled()
            ->with(['nurse', 'booking'])
            ->latest()
            ->get();

        // Also get complaints filed against this user (if nurse)
        $against = collect();
        if ($user->role === 'nurse') {
            $against = $user->complaintsAgainst()
                ->with(['user', 'booking'])
                ->latest()
                ->get();
        }

        $viewName = $user->role === 'nurse' ? 'nurse.complaints' : 'patient.complaints';

        return view($viewName, compact('filed', 'against'));
    }

    /**
     * Admin: Reply to a complaint.
     */
    public function reply(Request $request, Complaint $complaint)
    {
        $request->validate([
            'admin_reply' => 'required|string|max:2000',
        ]);

        $complaint->update([
            'admin_reply' => $request->admin_reply,
            'replied_at'  => now(),
            'status'      => $complaint->status === 'open' ? 'in_review' : $complaint->status,
        ]);

        return redirect()->back()->with('success', 'Reply sent successfully.');
    }

    /**
     * Admin: Update complaint status.
     */
    public function updateStatus(Request $request, Complaint $complaint)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', Complaint::STATUSES),
        ]);

        $complaint->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Complaint status updated.');
    }
}
