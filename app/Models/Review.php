<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = ['booking_id', 'rating', 'comment'];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function nurse()
    {
        return $this->hasOneThrough(
            User::class,
            Booking::class,
            'id',        // bookings.id
            'id',        // users.id
            'booking_id', // reviews.booking_id
            'nurse_id'   // bookings.nurse_id
        );
    }

    public function patient()
    {
        return $this->hasOneThrough(
            User::class,
            Booking::class,
            'id',
            'id',
            'booking_id',
            'patient_id'
        );
    }
}
