<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Booking;
use App\Models\Complaint;
use App\Models\SupportRequest;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        if (!auth()->check()) {
            return $this->guestHome();
        }

        return match (auth()->user()->role) {
            'admin' => $this->adminHome(),
            'nurse' => $this->nurseHome(),
            default => $this->patientHome(),
        };
    }

    public function nurseHome()
    {
        $nurseContext = null;
        $bookingSummary = null;
        $nextBooking = null;

        if (auth()->check() && auth()->user()->role === 'nurse') {
            $user = auth()->user();
            $profile = $user->nurseProfile;
            $nurseContext = [
                'has_profile' => (bool) $profile,
                'is_approved' => (bool) optional($profile)->is_approved,
                'is_available' => $profile ? (bool) $profile->availability : null,
            ];

            $bookingSummary = [
                'pending' => $user->bookingsAsNurse()->where('status', 'pending')->count(),
                'accepted' => $user->bookingsAsNurse()->where('status', 'accepted')->count(),
                'completed' => $user->bookingsAsNurse()->where('status', 'completed')->count(),
            ];

            $nextBooking = $user->bookingsAsNurse()
                ->with('patient')
                ->whereIn('status', ['pending', 'accepted'])
                ->orderBy('date')
                ->orderBy('time')
                ->first();
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
            'bookingSummary' => $bookingSummary,
            'nextBooking' => $nextBooking,
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

        $user = auth()->user();
        $patientSummary = null;
        $recentBookings = collect();
        $nextBooking = null;

        if ($user && $user->role === 'patient') {
            $bookings = $user->bookingsAsPatient()
                ->with(['nurse', 'nurse.nurseProfile', 'review'])
                ->latest('date')
                ->latest('time')
                ->get();

            $patientSummary = [
                'total' => $bookings->count(),
                'pending' => $bookings->where('status', 'pending')->count(),
                'accepted' => $bookings->where('status', 'accepted')->count(),
                'completed' => $bookings->where('status', 'completed')->count(),
                'reviews_pending' => $bookings
                    ->where('status', 'completed')
                    ->filter(fn ($booking) => !$booking->review)
                    ->count(),
            ];

            $recentBookings = $bookings->take(3);
            $nextBooking = $user->bookingsAsPatient()
                ->with(['nurse', 'nurse.nurseProfile'])
                ->whereIn('status', ['pending', 'accepted'])
                ->orderBy('date')
                ->orderBy('time')
                ->first();
        }

        return view('home.patient', [
            'featuredNurses' => $featuredNurses,
            'locations' => config('dhaka_areas', []),
            'patientSummary' => $patientSummary,
            'recentBookings' => $recentBookings,
            'nextBooking' => $nextBooking,
        ]);
    }

    private function guestHome()
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

    private function adminHome()
    {
        $stats = [
            'total_nurses' => User::where('role', 'nurse')->count(),
            'total_patients' => User::where('role', 'patient')->count(),
            'total_bookings' => Booking::count(),
            'pending_approvals' => User::where('role', 'nurse')
                ->whereHas('nurseProfile', function ($q) {
                    $q->where('is_approved', false);
                })
                ->count(),
        ];

        $pendingNurses = User::where('role', 'nurse')
            ->whereHas('nurseProfile', function ($q) {
                $q->where('is_approved', false);
            })
            ->with('nurseProfile')
            ->latest()
            ->take(5)
            ->get();

        $operations = [
            'support_requests' => SupportRequest::count(),
            'complaints' => Complaint::count(),
            'announcements' => Announcement::count(),
        ];

        $recentSupportRequests = SupportRequest::with('user')
            ->latest()
            ->take(4)
            ->get();

        return view('home.admin', [
            'stats' => $stats,
            'pendingNurses' => $pendingNurses,
            'operations' => $operations,
            'recentSupportRequests' => $recentSupportRequests,
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
