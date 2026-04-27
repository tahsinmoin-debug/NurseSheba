<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// NurseProfile model handles nurse-related profile information
class NurseProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'qualification', 'gender', 'specialization', 'experience_years', 'district', 'thana',
        'bio', 'availability', 'documents', 'license_document', 'is_approved',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
