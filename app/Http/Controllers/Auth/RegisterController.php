<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\NurseProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:nurse,patient',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        if ($request->role === 'nurse') {
            NurseProfile::create(['user_id' => $user->id]);
        }

        auth()->login($user);

        if ($user->role === 'nurse') {
            return redirect('/nurse/dashboard')->with('success', 'Registration successful! Please complete your profile.');
        }
        return redirect('/patient/dashboard')->with('success', 'Registration successful!');
    }
}
