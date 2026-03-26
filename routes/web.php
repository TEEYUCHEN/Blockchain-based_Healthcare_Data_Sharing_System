<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GrantAccessController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\PatientUploadController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LabController;

// Default page when start server
Route::get('/', function () {
    return redirect()->route('login.form');
});

// Register
Route::get('/register', fn() => view('auth.register'))->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Login
Route::get('/login', fn() => view('auth.login'))->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Logout (web session)
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard
Route::get('/dashboard', fn() => view('auth.dashboard'))
    ->middleware('auth')
    ->name('dashboard');


// Patient Panel Routes
Route::middleware('auth')->group(function () {

    Route::get('/patient/records', [MedicalRecordController::class, 'index'])
        ->name('patient.records');

    Route::get('/patient/grant-access', [GrantAccessController::class, 'index'])
        ->name('patient.grant.access');

    Route::get('/patient/records', [PatientUploadController::class, 'index'])->name('patient.records');

    Route::get('/patient/grant-access/browse', [GrantAccessController::class, 'browse'])
        ->name('patient.grant.access.browse');

    Route::get('/patient/access/{id}', [GrantAccessController::class, 'show'])
        ->name('patient.access.show');

    Route::get('/patient/upload', function () {
        abort_unless(auth()->user()->role === 'patient', 403);
        return view('patient.upload');
    })->name('patient.upload');

    //Route::post('/patient/grant-access', [GrantAccessController::class, 'store'])->name('patient.grant.access.store');
    Route::post('/patient/grant/access/store', [GrantAccessController::class, 'store'])
        ->name('patient.grant.access.store');

    Route::post('/patient/grant-access/revoke', [GrantAccessController::class, 'destroy'])
        ->name('patient.grant.access.revoke');

    Route::post('/patient/records', [PatientUploadController::class, 'store'])
        ->name('patient.records.store');

    Route::get('/records', [MedicalRecordController::class, 'index'])
        ->name('records.index');

    Route::get('/records/{medicalRecord}', [MedicalRecordController::class, 'show'])
        ->name('records.show');

    Route::post('/records/{id}/delete', [MedicalRecordController::class, 'delete'])
        ->name('records.delete');

});

// Doctor Panel Routes
Route::middleware(['auth', 'role:doctor'])->group(function () {

    Route::get('/doctor/diagnosis/{patient}', [DoctorController::class, 'create'])
        ->name('doctor.write_diagnosis');

    Route::get('/doctor/patient-list', [DoctorController::class, 'patientList'])->name('doctor.patient_list');

    Route::get('/doctor/patient-reports/{id}', [DoctorController::class, 'patientReports'])
        ->name('doctor.patient_reports');


    Route::post('/doctor/submit-diagnosis', [DoctorController::class, 'submitDiagnosis'])
        ->name('doctor.submit_diagnosis');

    Route::get('/doctor/report/{id}/edit', [DoctorController::class, 'edit'])
        ->name('doctor.edit_report');

    Route::post('/doctor/report/{id}/update', [DoctorController::class, 'update'])
        ->name('doctor.update_report');

    Route::get('/doctor/patient-details/{id}', [DoctorController::class, 'patientDetails'])->name('doctor.patient_details');
});

// Lab Panel Routes
Route::middleware(['auth', 'role:lab'])->group(function () {
    Route::get('/lab/patient_list', [LabController::class, 'patients'])
        ->name('lab.patient_list');

    Route::get('/lab/write_lab_report/{patient}', [LabController::class, 'create'])
        ->name('lab.write_lab_report');

    Route::post('/lab/upload', [LabController::class, 'store'])
        ->name('lab.upload');

    Route::get('/lab/reports/{patient_id}', [LabController::class, 'index'])
        ->name('lab.reports');

    Route::get('/lab/patient-details/{id}', [LabController::class, 'patientDetails'])->name('lab.patient_details');

    Route::get('/lab/report/{id}/edit', [LabController::class, 'edit'])
        ->name('lab.edit_report');

    Route::post('/lab/report/{id}/update', [LabController::class, 'update'])
        ->name('lab.update_report');
});

// Profile Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'view'])->name('profile.view');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

// File download route
Route::middleware(['auth'])->group(function () {

    Route::get('/records/view/{type}/{id}', [MedicalRecordController::class, 'view'])
        ->name('records.view');

    Route::get('/records/download/{type}/{id}', [MedicalRecordController::class, 'download'])
        ->name('records.download');

});