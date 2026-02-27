<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function create($nurseId)
    {
        $nurse = User::where('role', 'nurse')->with('nurseProfile')->findOrFail($nurseId);
        return view('patient.book', compact('nurse'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nurse_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'service_type' => 'required|string|max:255',
        ]);

        Booking::create([
            'patient_id' => auth()->id(),
            'nurse_id' => $request->nurse_id,
            'date' => $request->date,
            'time' => $request->time,
            'service_type' => $request->service_type,
            'status' => 'pending',
        ]);

        return redirect()->route('patient.dashboard')->with('success', 'Booking request sent successfully!');
    }

    public function cancel(Booking $booking)
    {
        if ($booking->patient_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }
        $booking->update(['status' => 'cancelled']);
        return redirect()->back()->with('success', 'Booking cancelled.');
    }

    public function accept(Booking $booking)
    {
        if ($booking->nurse_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }
        $booking->update(['status' => 'accepted']);
        return redirect()->back()->with('success', 'Booking accepted.');
    }

    public function complete(Booking $booking)
    {
        if ($booking->nurse_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }
        $booking->update(['status' => 'completed']);
        return redirect()->back()->with('success', 'Booking marked as completed.');
    }
}
