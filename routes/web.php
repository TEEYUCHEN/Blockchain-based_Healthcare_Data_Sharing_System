<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GrantAccessController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\PatientUploadController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ProfileController;

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


// ======================
// Patient pages + actions
// ======================
Route::middleware('auth')->group(function () {

    Route::get('/patient/records', [MedicalRecordController::class, 'index'])
        ->name('patient.records');

    // Page 1: Granted Access list (tabs)
    Route::get('/patient/grant-access', [GrantAccessController::class, 'index'])
        ->name('patient.grant.access');

    // Page 2: Browse doctors/labs (tabs + search)
    Route::get('/patient/grant-access/browse', [GrantAccessController::class, 'browse'])
        ->name('patient.grant.access.browse');

    // Upload medical record form
    Route::get('/patient/upload', function () {
        abort_unless(auth()->user()->role === 'patient', 403);
        return view('patient.upload');
    })->name('patient.upload');

    // Actions from forms (redirect back + flash message)
    Route::post('/patient/grant-access', [GrantAccessController::class, 'store'])
        ->name('patient.grant.access.store');

    Route::post('/patient/grant-access/revoke', [GrantAccessController::class, 'destroy'])
        ->name('patient.grant.access.revoke');

    // Medical records
    Route::post('/patient/records', [PatientUploadController::class, 'store'])
        ->name('patient.records.store');

    Route::get('/records', [MedicalRecordController::class, 'index'])
        ->name('records.index');

    Route::get('/records/{medicalRecord}', [MedicalRecordController::class, 'show'])
        ->name('records.show');

    Route::get('/records/{medicalRecord}/download', [MedicalRecordController::class, 'download'])
        ->name('records.download');

    Route::post('/records/{id}/delete', [MedicalRecordController::class, 'delete'])
        ->name('records.delete');

});

// Doctor Panel Routes
Route::middleware(['auth', 'role:doctor'])->group(function () {
    Route::get('/doctor/patient-list', [DoctorController::class, 'patientList'])->name('doctor.patient_list');

    Route::get('/doctor/view-patient-reports', [DoctorController::class, 'viewPatientReports'])
        ->name('doctor.view_patient_reports');

    Route::get('/doctor/patient-reports/{id}', [DoctorController::class, 'patientReports'])
        ->name('doctor.patient_reports');

    Route::get('/doctor/write-diagnosis', [DoctorController::class, 'writeDiagnosis'])
        ->name('doctor.write_diagnosis');

    Route::post('/doctor/submit-diagnosis', [DoctorController::class, 'submitDiagnosis'])
        ->name('doctor.submit_diagnosis');

    Route::get('/doctor/patient-details/{id}', [DoctorController::class, 'patientDetails'])->name('doctor.patient_details');
});

// Profile Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'view'])->name('profile.view');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});