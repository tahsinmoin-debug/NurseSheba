<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function dashboard()
    {
        $bookings = auth()->user()->bookingsAsPatient()
            ->with(['nurse', 'nurse.nurseProfile', 'review'])
            ->latest()
            ->get();

        return view('patient.dashboard', compact('bookings'));
    }
}
