<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            $user = auth()->user();

            if ($user->role === 'admin') {
                return redirect('/admin/dashboard');
            }

            if ($user->role === 'nurse') {
                return $this->nurseHome();
            }
        }

        return $this->patientHome();
    }

    public function nurseHome()
    {
        $nurseContext = null;
        if (auth()->check() && auth()->user()->role === 'nurse') {
            $profile = auth()->user()->nurseProfile;
            $nurseContext = [
                'has_profile' => (bool) $profile,
                'is_approved' => (bool) optional($profile)->is_approved,
                'is_available' => $profile ? (bool) $profile->availability : null,
            ];
        }

        $approvedNurseCount = User::where('role', 'nurse')
            ->whereHas('nurseProfile', function ($q) {
                $q->where('is_approved', true);
            })
            ->count();

        $completedBookingCount = Booking::where('status', 'completed')->count();
        $patientCount = User::where('role', 'patient')->count();
        $coveredAreaCount = count(config('dhaka_areas', []));

        return view('home.nurse', [
            'approvedNurseCount' => $approvedNurseCount,
            'completedBookingCount' => $completedBookingCount,
            'patientCount' => $patientCount,
            'coveredAreaCount' => $coveredAreaCount,
            'nurseContext' => $nurseContext,
        ]);
    }

    private function patientHome()
    {
        $featuredNurses = User::where('role', 'nurse')
            ->whereHas('nurseProfile', function ($q) {
                $q->where('is_approved', true);
            })
            ->with('nurseProfile')
            ->latest()
            ->take(6)
            ->get();

        return view('home.index', [
            'featuredNurses' => $featuredNurses,
            'locations' => config('dhaka_areas', []),
        ]);
    }

    public function nurses(Request $request)
    {
        $query = User::where('role', 'nurse')
            ->whereHas('nurseProfile', function ($q) {
                $q->where('is_approved', true);
            })
            ->with('nurseProfile');

        if ($request->location) {
            $query->where('location', $request->location);
        }

        if ($request->specialization) {
            $query->whereHas('nurseProfile', function ($q) use ($request) {
                $q->where('specialization', 'like', '%' . $request->specialization . '%');
            });
        }

        $nurses = $query->paginate(12);

        return view('nurses.index', [
            'nurses' => $nurses,
            'locations' => config('dhaka_areas', []),
        ]);
    }

    public function nurseProfile($id)
    {
        $nurse = User::where('role', 'nurse')->with([
            'nurseProfile',
            'bookingsAsNurse.review',
        ])->findOrFail($id);

        $reviews = collect();
        foreach ($nurse->bookingsAsNurse as $booking) {
            if ($booking->review) {
                $reviews->push($booking->review);
            }
        }

        return view('nurses.show', compact('nurse', 'reviews'));
    }
}
