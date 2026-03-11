<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetOtpMail;
use App\Models\PasswordResetOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetOtpController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $otp = (string) random_int(100000, 999999);
        $expiresInMinutes = (int) env('PASSWORD_RESET_OTP_EXPIRE_MINUTES', 10);
        $expiresAt = now()->addMinutes($expiresInMinutes);

        PasswordResetOtp::updateOrCreate(
            ['email' => $request->email],
            [
                'otp_hash' => Hash::make($otp),
                'reset_token' => null,
                'expires_at' => $expiresAt,
                'verified_at' => null,
            ]
        );

        Mail::to($request->email)->send(new PasswordResetOtpMail($otp, $expiresInMinutes));

        return redirect()->route('password.otp.verify.form', ['email' => $request->email])
            ->with('success', 'A password reset OTP has been sent to your email.');
    }

    public function showVerifyForm(Request $request)
    {
        return view('auth.verify-otp', ['email' => $request->query('email')]);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $otpRecord = PasswordResetOtp::where('email', $request->email)->first();

        if (!$otpRecord || now()->greaterThan($otpRecord->expires_at)) {
            return back()->withErrors([
                'otp' => 'OTP expired. Please request a new OTP.',
            ])->onlyInput('email');
        }

        if (!Hash::check($request->otp, $otpRecord->otp_hash)) {
            return back()->withErrors([
                'otp' => 'Invalid OTP code.',
            ])->onlyInput('email');
        }

        $otpRecord->verified_at = now();
        $otpRecord->reset_token = Str::random(64);
        $otpRecord->save();

        return redirect()->route('password.otp.reset.form', ['token' => $otpRecord->reset_token]);
    }

    public function showResetForm(string $token)
    {
        $otpRecord = PasswordResetOtp::where('reset_token', $token)->first();

        if (
            !$otpRecord ||
            !$otpRecord->verified_at ||
            now()->greaterThan($otpRecord->expires_at)
        ) {
            return redirect()->route('password.otp.request.form')
                ->with('error', 'Password reset session expired. Please request a new OTP.');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $otpRecord->email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $otpRecord = PasswordResetOtp::where('email', $request->email)
            ->where('reset_token', $request->token)
            ->first();

        if (
            !$otpRecord ||
            !$otpRecord->verified_at ||
            now()->greaterThan($otpRecord->expires_at)
        ) {
            return redirect()->route('password.otp.request.form')
                ->with('error', 'Password reset session expired. Please request a new OTP.');
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return redirect()->route('password.otp.request.form')
                ->with('error', 'User not found for this reset request.');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        $otpRecord->delete();

        return redirect()->route('login')
            ->with('success', 'Password reset successful. Please login with your new password.');
    }
}
