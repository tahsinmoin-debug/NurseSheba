<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NurseController extends Controller
{
    private $districts = [
        'Dhaka', 'Chittagong', 'Sylhet', 'Rajshahi', 'Khulna', 'Barishal',
        'Mymensingh', 'Rangpur', 'Comilla', 'Gazipur', 'Narayanganj', 'Narsingdi',
        'Tangail', 'Jamalpur', 'Sherpur', 'Netrokona', 'Kishoreganj', 'Manikganj',
        'Munshiganj', 'Faridpur', 'Gopalganj', 'Madaripur', 'Rajbari', 'Shariatpur',
        'Sunamganj', 'Habiganj', 'Moulvibazar', 'Bogura', 'Joypurhat', 'Naogaon',
        'Natore', 'Chapainawabganj', 'Pabna', 'Sirajganj', 'Jessore', 'Satkhira',
        'Magura', 'Jhenaidah', 'Narail', 'Chuadanga', 'Kushtia', 'Meherpur',
        'Bagerhat', 'Patuakhali', 'Bhola', 'Jhalokati', 'Barguna', 'Pirojpur',
        'Barisal', 'Bandarban', 'Brahmanbaria', 'Chandpur', "Cox's Bazar", 'Feni',
        'Khagrachhari', 'Lakshmipur', 'Noakhali', 'Rangamati',
    ];

    public function dashboard()
    {
        $bookings = auth()->user()->bookingsAsNurse()
            ->with(['patient'])
            ->latest()
            ->get();

        return view('nurse.dashboard', compact('bookings'));
    }

    public function profile()
    {
        $profile = auth()->user()->nurseProfile;
        return view('nurse.profile', ['profile' => $profile, 'districts' => $this->districts]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'specialization' => 'required|string|max:255',
            'experience_years' => 'required|integer|min:0',
            'district' => 'required|string',
            'thana' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'availability' => 'nullable|boolean',
        ]);

        $profile = auth()->user()->nurseProfile;
        if (!$profile) {
            $profile = new \App\Models\NurseProfile(['user_id' => auth()->id()]);
        }

        $profile->fill([
            'specialization' => $request->specialization,
            'experience_years' => $request->experience_years,
            'district' => $request->district,
            'thana' => $request->thana,
            'bio' => $request->bio,
            'availability' => $request->has('availability'),
        ]);
        $profile->save();

        return redirect()->route('nurse.profile')->with('success', 'Profile updated successfully!');
    }
}
