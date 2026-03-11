<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
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
