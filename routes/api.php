<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\GrantAccessController;
use App\Http\Controllers\DoctorReportController;
use App\Http\Controllers\LabReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public API routes (only if your frontend uses fetch)
Route::post('/wallet-auth', [WalletController::class, 'verifySignature']);

// If you REALLY need API login/register via JSON, keep these:
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);


// Protected API routes (JSON)
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'userInfo']);

    // Grant access (JSON)
    Route::post('/grant-access', [GrantAccessController::class, 'store']);
    Route::delete('/grant-access/{authorized_id}', [GrantAccessController::class, 'destroy']);

    // Reports (JSON)
    Route::post('/doctor-reports', [DoctorReportController::class, 'store']);
    Route::get('/doctor-reports/{patient_id}', [DoctorReportController::class, 'index']);

    Route::post('/lab-reports', [LabReportController::class, 'store']);
    Route::get('/lab-reports/{patient_id}', [LabReportController::class, 'index']);
});