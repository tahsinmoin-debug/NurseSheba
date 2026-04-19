<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Booking;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $booking = Booking::findOrFail($request->booking_id);

        // Only the patient who made the booking can review
        if ($booking->patient_id !== auth()->id()) {
            return redirect()->back()->with('error', 'Unauthorized.');
        }

        // Reviews are only allowed after booking is completed
        if ($booking->status !== 'completed') {
            return redirect()->back()->with('error', 'You can only review a completed booking.');
        }

        // Prevent duplicate reviews
        if ($booking->review) {
            return redirect()->back()->with('error', 'You have already reviewed this booking.');
        }

        Review::create([
            'booking_id' => $request->booking_id,
            'rating'     => $request->rating,
            'comment'    => $request->comment,
        ]);

        return redirect()->back()->with('success', 'Review submitted successfully! Thank you for your feedback.');
    }
}
