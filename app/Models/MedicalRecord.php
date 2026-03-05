<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicalRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'uploaded_by_user_id',
        'uploaded_by_role',
        'title',
        'description',
        'category',
        'original_filename',
        'stored_path',
        'mime_type',
        'size',
        'file_hash',
        'blockchain_tx_hash',
        'verification_status',
        'verified_by_user_id',
        'verified_at'
    ];
}