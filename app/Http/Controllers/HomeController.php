<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
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
            'districts' => $this->districts,
        ]);
    }

    public function nurses(Request $request)
    {
        $query = User::where('role', 'nurse')
            ->whereHas('nurseProfile', function ($q) {
                $q->where('is_approved', true);
            })
            ->with('nurseProfile');

        if ($request->district) {
            $query->whereHas('nurseProfile', function ($q) use ($request) {
                $q->where('district', $request->district);
            });
        }

        if ($request->specialization) {
            $query->whereHas('nurseProfile', function ($q) use ($request) {
                $q->where('specialization', 'like', '%' . $request->specialization . '%');
            });
        }

        $nurses = $query->paginate(12);

        return view('nurses.index', [
            'nurses' => $nurses,
            'districts' => $this->districts,
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
