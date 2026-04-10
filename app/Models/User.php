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
}
