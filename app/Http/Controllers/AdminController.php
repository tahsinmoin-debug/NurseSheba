<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\Booking;
use App\Models\Complaint;
use App\Models\NurseProfile;
use App\Models\SupportRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_nurses' => User::where('role', 'nurse')->count(),
            'total_patients' => User::where('role', 'patient')->count(),
            'total_bookings' => Booking::count(),
            'pending_approvals' => NurseProfile::where('is_approved', false)->count(),
        ];

        $pendingNurses = User::where('role', 'nurse')
            ->whereHas('nurseProfile', function ($q) {
                $q->where('is_approved', false);
            })
            ->with('nurseProfile')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingNurses'));
    }

    public function nurses()
    {
        $nurses = User::where('role', 'nurse')->with('nurseProfile')->latest()->paginate(15);
        return view('admin.nurses', compact('nurses'));
    }

    public function approveNurse($id)
    {
        $profile = NurseProfile::where('user_id', $id)->firstOrFail();
        $profile->update(['is_approved' => true]);
        return redirect()->back()->with('success', 'Nurse approved successfully!');
    }

    public function rejectNurse($id)
    {
        $profile = NurseProfile::where('user_id', $id)->firstOrFail();
        $profile->update(['is_approved' => false]);
        return redirect()->back()->with('success', 'Nurse approval revoked.');
    }

    public function patients()
    {
        $patients = User::where('role', 'patient')->latest()->paginate(15);
        return view('admin.patients', compact('patients'));
    }

    public function bookings()
    {
        $bookings = Booking::with(['patient', 'nurse'])->latest()->paginate(15);
        return view('admin.bookings', compact('bookings'));
    }

    public function complaints()
    {
        $complaints = Complaint::with(['user', 'nurse'])->latest()->get();
        return view('admin.complaints', compact('complaints'));
    }

    public function announcements()
    {
        $announcements = Announcement::latest()->get();
        return view('admin.announcements', compact('announcements'));
    }

    public function storeAnnouncement(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        Announcement::create($request->only('title', 'message'));

        return redirect()->back()->with('success', 'Announcement created!');
    }

    public function supportRequests()
    {
        $requests = SupportRequest::with('user')->latest()->get();
        return view('admin.support', compact('requests'));
    }
}
