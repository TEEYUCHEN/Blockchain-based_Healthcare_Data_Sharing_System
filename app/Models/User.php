<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'wallet_address',
        'phone',
        'address',
        'specialty',      // for doctor/lab
        'status',         // active/inactive
        'organization_id', // for hospital/lab
        'license_number',  // for doctor/lab
        'profile_pic',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relationships
    public function doctorReports()
    {
        return $this->hasMany(DoctorReport::class, 'doctor_id');
    }

    public function labReports()
    {
        return $this->hasMany(LabReport::class, 'lab_id');
    }

    public function grantedAccesses()
    {
        return $this->hasMany(GrantAccess::class, 'patient_id');
    }

    public function grantedToDoctors()
    {
        return $this->hasMany(GrantAccess::class, 'doctor_id');
    }

    public function medicalRecords()
    {
        return $this->hasMany(\App\Models\MedicalRecord::class, 'patient_id');
    }
}