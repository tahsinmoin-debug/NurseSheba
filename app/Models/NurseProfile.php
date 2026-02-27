<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NurseProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'specialization', 'experience_years', 'district', 'thana',
        'bio', 'availability', 'documents', 'is_approved',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
