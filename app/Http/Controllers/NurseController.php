<?php

namespace App\Http\Controllers;

use App\Models\NurseProfile;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NurseController extends Controller
{
    public function dashboard(Request $request)
    {
        $statusFilter = $request->query('status', 'all');
        if (!in_array($statusFilter, ['all', 'pending', 'accepted', 'completed', 'cancelled'], true)) {
            $statusFilter = 'all';
        }

        $user = auth()->user();

        $bookings = $user->bookingsAsNurse()
            ->with(['patient', 'payment'])
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

        $statusCounts = $user->bookingsAsNurse()
            ->selectRaw('status, count(*) as aggregate')
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        // Rating summary for the nurse
        $ratingSummary = [
            'average' => $user->average_rating,
            'count'   => $user->review_count,
        ];

        // Recent reviews
        $recentReviews = $user->reviewsAsNurse()
            ->with('booking.patient')
            ->latest()
            ->take(5)
            ->get();

        return view('nurse.dashboard', compact(
            'upcomingBookings',
            'pastBookings',
            'statusFilter',
            'statusCounts',
            'ratingSummary',
            'recentReviews'
        ));
    }

    public function profile()
    {
        $profile = auth()->user()->nurseProfile;
        return view('nurse.profile', [
            'profile'   => $profile,
            'locations' => config('dhaka_areas', []),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'qualification'    => 'required|string|max:255',
            'gender'           => 'required|in:male,female',
            'specialization'   => 'required|string|max:255',
            'experience_years' => 'required|integer|min:0',
            'location'         => ['required', 'string', Rule::in(config('dhaka_areas', []))],
            'bio'              => 'nullable|string',
            'availability'     => 'nullable|boolean',
            'license_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $profile = auth()->user()->nurseProfile;
        if (!$profile) {
            $profile = new NurseProfile(['user_id' => auth()->id()]);
        }

        if ($request->hasFile('license_document')) {
            $licenseDocumentPath      = $request->file('license_document')->store('license_documents', 'public');
            $profile->license_document = $licenseDocumentPath;
            $profile->documents        = $licenseDocumentPath;
        }

        $profile->fill([
            'qualification'    => $request->qualification,
            'gender'           => $request->gender,
            'specialization'   => $request->specialization,
            'experience_years' => $request->experience_years,
            'district'         => 'Dhaka',
            'thana'            => $request->location,
            'bio'              => $request->bio,
            'availability'     => $request->has('availability'),
        ]);
        $profile->save();

        $user           = auth()->user();
        $user->location = $request->location;
        $user->save();

        return redirect()->route('nurse.profile')->with('success', 'Profile updated successfully!');
    }

    /**
     * Toggle the nurse's availability status from the dashboard  .
     */
    public function toggleAvailability()
    {
        $profile = auth()->user()->nurseProfile;

        if (!$profile) {
            return redirect()->back()->with('error', 'Please complete your profile first.');
        }

        $profile->update(['availability' => !$profile->availability]);

        $status = $profile->availability ? 'Available' : 'Not Available';
        return redirect()->back()->with('success', "Your status is now: {$status}");
    }
}
