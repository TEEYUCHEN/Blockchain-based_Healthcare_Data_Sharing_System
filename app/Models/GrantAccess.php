<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class GrantAccess extends Model
{
    protected $table = 'grant_access';

    protected $fillable = [
        'patient_id',
        'authorized_id',  // Changed from doctor_id
        'role_type',      // Added
        'status',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }


}