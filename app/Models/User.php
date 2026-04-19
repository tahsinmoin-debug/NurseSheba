<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'address', 'location', 'role', 'login_count', 'last_login_at', 'last_login_ip',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
        'last_login_at'     => 'datetime',
    ];

    public function nurseProfile()
    {
        return $this->hasOne(NurseProfile::class);
    }

    public function bookingsAsPatient()
    {
        return $this->hasMany(Booking::class, 'patient_id');
    }

    public function bookingsAsNurse()
    {
        return $this->hasMany(Booking::class, 'nurse_id');
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, Booking::class, 'patient_id', 'booking_id');
    }

    /**
     * Get all reviews received by this nurse (through their bookings).
     */
    public function reviewsAsNurse()
    {
        return $this->hasManyThrough(Review::class, Booking::class, 'nurse_id', 'booking_id');
    }

    /**
     * Complaints filed against this user (as nurse).
     */
    public function complaintsAgainst()
    {
        return $this->hasMany(Complaint::class, 'nurse_id');
    }

    /**
     * Complaints filed by this user.
     */
    public function complaintsFiled()
    {
        return $this->hasMany(Complaint::class, 'user_id');
    }

    /**
     * Get the nurse's average rating (computed from reviews on completed bookings).
     */
    public function getAverageRatingAttribute(): ?float
    {
        if ($this->role !== 'nurse') {
            return null;
        }

        $avg = $this->reviewsAsNurse()->avg('rating');
        return $avg ? round($avg, 1) : null;
    }

    /**
     * Get total number of reviews for this nurse.
     */
    public function getReviewCountAttribute(): int
    {
        if ($this->role !== 'nurse') {
            return 0;
        }

        return $this->reviewsAsNurse()->count();
    }
}
