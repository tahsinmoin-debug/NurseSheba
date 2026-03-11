<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\NurseProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Throwable;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm(Request $request)
    {
        $preferredRole = in_array($request->query('role'), ['nurse', 'patient'], true)
            ? $request->query('role')
            : 'patient';

        return view('auth.register', [
            'locations' => config('dhaka_areas', []),
            'preferredRole' => $preferredRole,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'location' => ['required', 'string', Rule::in(config('dhaka_areas', []))],
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:nurse,patient',
            'qualification' => 'exclude_unless:role,nurse|required|string|max:255',
            'gender' => 'exclude_unless:role,nurse|required|in:male,female',
            'experience_years' => 'exclude_unless:role,nurse|required|integer|min:0|max:60',
            'specialization' => 'exclude_unless:role,nurse|required|string|max:255',
            'license_document' => 'exclude_unless:role,nurse|required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'location' => $request->location,
                'password' => Hash::make($request->password),
                'role' => $request->role,
            ]);

            if ($request->role === 'nurse') {
                $licenseDocument = $request->file('license_document')
                    ? $request->file('license_document')->store('license_documents', 'public')
                    : null;

                NurseProfile::create([
                    'user_id' => $user->id,
                    'qualification' => $request->qualification,
                    'gender' => $request->gender,
                    'experience_years' => $request->experience_years,
                    'specialization' => $request->specialization,
                    'district' => 'Dhaka',
                    'thana' => $request->location,
                    'license_document' => $licenseDocument,
                    'documents' => $licenseDocument,
                ]);
            }

            auth()->login($user);
        } catch (Throwable $exception) {
            report($exception);

            return back()
                ->withInput($request->except(['password', 'password_confirmation', 'license_document']))
                ->withErrors([
                    'register' => 'Registration failed due to a server/database issue. Please verify your DB connection and try again.',
                ]);
        }

        if ($user->role === 'nurse') {
            return redirect('/nurse/dashboard')->with('success', 'Registration successful! Please complete your profile.');
        }

        return redirect('/patient/dashboard')->with('success', 'Registration successful!');
    }
}
