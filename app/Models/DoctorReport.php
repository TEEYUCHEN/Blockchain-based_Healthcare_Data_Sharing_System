<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorReport extends Model
{
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'title',
        'description',
        'file_path',
    ];

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}