<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function dashboard(Request $request)
    {
        $statusFilter = $request->query('status', 'all');
        if (!in_array($statusFilter, ['all', 'pending', 'accepted', 'completed', 'cancelled'], true)) {
            $statusFilter = 'all';
        }

        $bookings = auth()->user()->bookingsAsPatient()
            ->with(['nurse', 'nurse.nurseProfile', 'review', 'payment'])
            ->orderByDesc('date')
            ->orderByDesc('time')
            ->get();

        if ($statusFilter !== 'all') {
            $bookings = $bookings->where('status', $statusFilter)->values();
        }

        $upcomingBookings = $bookings
            ->filter(function ($booking) {
                return in_array($booking->status, ['pending', 'accepted'], true)
                    && $booking->appointment_at->greaterThanOrEqualTo(now());
            })
            ->sortBy('appointment_at')
            ->values();

        $pastBookings = $bookings
            ->reject(function ($booking) {
                return in_array($booking->status, ['pending', 'accepted'], true)
                    && $booking->appointment_at->greaterThanOrEqualTo(now());
            })
            ->values();

        $statusCounts = auth()->user()->bookingsAsPatient()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return view('patient.dashboard', compact(
            'upcomingBookings',
            'pastBookings',
            'statusFilter',
            'statusCounts'
        ));
    }
}
