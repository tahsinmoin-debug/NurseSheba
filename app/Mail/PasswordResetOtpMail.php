<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $otpCode, public int $expiresInMinutes)
    {
    }

    public function build(): self
    {
        return $this->subject('NurseSheba Password Reset OTP')
            ->view('emails.password-reset-otp');
    }
}
