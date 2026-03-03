<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GrantAccessController;

// Home
Route::get('/', fn() => view('welcome'));

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

    Route::get('/patient/upload', function () {
        abort_unless(auth()->user()->role === 'patient', 403);
        return view('patient.upload');
    })->name('patient.upload');

    // Page 1: Granted Access list (tabs)
    Route::get('/patient/grant-access', [GrantAccessController::class, 'index'])
        ->name('patient.grant.access');

    // Page 2: Browse doctors/labs (tabs + search)
    Route::get('/patient/grant-access/browse', [GrantAccessController::class, 'browse'])
        ->name('patient.grant.access.browse');

    // Actions from forms (redirect back + flash message)
    Route::post('/patient/grant-access', [GrantAccessController::class, 'store'])
        ->name('patient.grant.access.store');

    Route::post('/patient/grant-access/revoke', [GrantAccessController::class, 'destroy'])
        ->name('patient.grant.access.revoke');
});