<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\GrantAccessController;
use App\Http\Controllers\DoctorReportController;
use App\Http\Controllers\LabReportController;
use App\Http\Controllers\PatientUploadController;

Route::post('/wallet-auth', [WalletController::class, 'verifySignature']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'userInfo']);

    // Grant access
    Route::post('/grant-access', [GrantAccessController::class, 'store']);
    Route::delete('/grant-access/{authorized_id}', [GrantAccessController::class, 'destroy']);

    // Doctor reports
    Route::post('/doctor-reports', [DoctorReportController::class, 'store']);
    Route::get('/doctor-reports/{patient_id}', [DoctorReportController::class, 'index']);

    // Lab reports
    Route::post('/lab-reports', [LabReportController::class, 'store']);
    Route::get('/lab-reports/{patient_id}', [LabReportController::class, 'index']);

    // Patient uploads (medical records)
    Route::get('/patient/records', [PatientUploadController::class, 'index']);
    Route::delete('/patient/records/{medicalRecord}', [PatientUploadController::class, 'destroy']);
});