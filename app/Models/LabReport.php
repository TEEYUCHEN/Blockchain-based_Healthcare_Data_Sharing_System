<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LabReport extends Model
{
    protected $fillable = [
        'lab_id',
        'patient_id',
        'test_type',
        'result',
        'file_path',
    ];

    public function lab()
    {
        return $this->belongsTo(User::class, 'lab_id');
    }

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }
}