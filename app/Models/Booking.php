<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'nurse_id', 'date', 'time', 'service_type', 'service_address', 'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function getAppointmentAtAttribute(): Carbon
    {
        return Carbon::parse($this->date->toDateString() . ' ' . $this->time);
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function nurse()
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
