<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id', 'nurse_id', 'date', 'time', 'service_type', 'service_address', 'duration_hours', 'status',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // Combine the stored date and time into one Carbon instance for convenience.
    public function getAppointmentAtAttribute(): Carbon
    {
        return Carbon::parse($this->date->toDateString() . ' ' . $this->time);
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    // Link each booking to its assigned nurse account.
    public function nurse()
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    // A booking can have multiple complaints recorded against it.
    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
}
