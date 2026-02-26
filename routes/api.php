<?php

use Illuminate\Http\Request;
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
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Public API routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/wallet-auth', [WalletController::class, 'verifySignature']);

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'userInfo']);

    // Grant access
    Route::post('/grant-access', [GrantAccessController::class, 'store']);
    Route::delete('/revoke-access/{doctor_id}', [GrantAccessController::class, 'destroy']);

    // Reports
    Route::post('/doctor-reports', [DoctorReportController::class, 'store']);
    Route::get('/doctor-reports/{patient_id}', [DoctorReportController::class, 'index']);
    Route::post('/lab-reports', [LabReportController::class, 'store']);
    Route::get('/lab-reports/{patient_id}', [LabReportController::class, 'index']);
});