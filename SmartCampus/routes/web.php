<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;
use Illuminate\Support\Facades\Route;

// ──────────────────────────────────────
// Public Routes (Guest)
// ──────────────────────────────────────

Route::middleware('guest')->group(function () {
    Route::get('/', function () {
        return redirect()->route('login');
    });

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');

    // OTP Routes
    Route::get('/otp', [AuthController::class, 'showOtp'])->name('otp.show');
    Route::post('/otp', [AuthController::class, 'verifyOtp'])->name('otp.verify');
});

// ──────────────────────────────────────
// Authenticated Routes
// ──────────────────────────────────────

Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard (role-based)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Mahasiswa Routes ──
    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        // Manajemen Tugas (Read-Only + Submit)
        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');

        // Submission (Upload tugas)
        Route::post('/assignments/{assignment}/submit', [SubmissionController::class, 'store'])->name('submissions.store');
        Route::put('/submissions/{submission}', [SubmissionController::class, 'update'])->name('submissions.update');
    });

    // ── Dosen Routes ──
    Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
        // Manajemen Tugas (Full CRUD via Command Pattern)
        Route::resource('assignments', AssignmentController::class);
    });

    // ── Admin Routes ──
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // Manajemen Tugas (Read-Only Overview)
        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');
    });
});
