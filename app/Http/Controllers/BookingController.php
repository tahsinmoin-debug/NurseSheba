<?php

namespace App\Http\Controllers;

use App\Models\Booking;
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

        return view('patient.book', compact('nurse'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nurse_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'service_type' => 'required|string|max:255',
            'service_address' => 'required|string|max:500',
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
            'patient_id' => auth()->id(),
            'nurse_id' => $request->nurse_id,
            'date' => $request->date,
            'time' => $request->time,
            'service_type' => $request->service_type,
            'service_address' => $request->service_address,
            'status' => 'pending',
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
        return redirect()->back()->with('success', 'Booking accepted.');
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
