<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    const COMPLAINT_TYPES = [
        'Service Quality Issue',
        'Nurse Did Not Show Up',
        'Payment Issue',
        'Misconduct',
        'Fake Profile',
        'Incomplete Service',
        'Technical Issue',
    ];

    const STATUSES = ['open', 'in_review', 'resolved', 'closed'];

    protected $fillable = [
        'user_id', 'nurse_id', 'complaint_type', 'booking_id',
        'message', 'status', 'reporter_role', 'admin_reply', 'replied_at',
    ];

    protected $casts = [
        'replied_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function nurse()
    {
        return $this->belongsTo(User::class, 'nurse_id');
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'open'      => 'danger',
            'in_review' => 'warning',
            'resolved'  => 'success',
            'closed'    => 'secondary',
            default     => 'light',
        };
    }
}
