<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ExportController;
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


    // Download Submission File (General Auth Route)
    Route::get('/submissions/{submission}/download', [SubmissionController::class, 'download'])->name('submissions.download');

    // Notifikasi (Semua User Auth)
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'getUnread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read-all');
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');

    // ── Mahasiswa Routes ──
    Route::middleware('role:mahasiswa')->prefix('mahasiswa')->name('mahasiswa.')->group(function () {
        // Manajemen Tugas (Read-Only + Submit)
        Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('/assignments/{assignment}', [AssignmentController::class, 'show'])->name('assignments.show');

        // Submission (Upload tugas)
        Route::post('/assignments/{assignment}/submit', [SubmissionController::class, 'store'])->name('submissions.store');
        Route::put('/submissions/{submission}', [SubmissionController::class, 'update'])->name('submissions.update');

        // Rekap Nilai (Transkrip)
        Route::get('/transcript', [\App\Http\Controllers\TranscriptController::class, 'index'])->name('transcript');


        // Mata Kuliah & Nilai
        Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{course}/grades', [CourseController::class, 'grades'])->name('courses.grades');
        
        // Export (Route yang tadi kita buat)
        Route::get('/courses/{course}/export', [ExportController::class, 'exportCourseGrades'])->name('courses.export');

        // Enrollment (Pendaftaran Kelas)
        Route::get('/enrollments', [\App\Http\Controllers\Mahasiswa\EnrollmentController::class, 'index'])->name('enrollments.index');
        Route::post('/enrollments/{course}', [\App\Http\Controllers\Mahasiswa\EnrollmentController::class, 'store'])->name('enrollments.store');
    });

    // ── Dosen Routes ──
    Route::middleware('role:dosen')->prefix('dosen')->name('dosen.')->group(function () {
        // Undo / Redo untuk tugas
        Route::post('/assignments/undo', [AssignmentController::class, 'undo'])->name('assignments.undo');
        Route::post('/assignments/redo', [AssignmentController::class, 'redo'])->name('assignments.redo');

        // Manajemen Tugas (Full CRUD via Command Pattern)
        Route::resource('assignments', AssignmentController::class);
        Route::get('/assignments/{assignment}/export', [AssignmentController::class, 'exportGrades'])
             ->name('assignments.export');
        Route::post('/submissions/{submission}/grade', [SubmissionController::class, 'storeGrade'])
             ->name('submissions.grade');

        // Monitor Kelas (Dosen)
        Route::get('/courses', [\App\Http\Controllers\Dosen\CourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{course}', [\App\Http\Controllers\Dosen\CourseController::class, 'show'])->name('courses.show');
    });

    // ── Admin Routes ──
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        // User Management (Admin)
        Route::patch('users/{user}/toggle-active', [\App\Http\Controllers\Admin\UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
        
        // Course Management (Admin)
        Route::resource('courses', \App\Http\Controllers\Admin\CourseController::class);

        // Announcement Management (Admin)
        Route::patch('announcements/{announcement}/toggle-active', [\App\Http\Controllers\Admin\AnnouncementController::class, 'toggleActive'])->name('announcements.toggle-active');
        Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class)->except(['show']);

        // Activity Log (semua log dari semua user — full access)
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/{id}', [ActivityLogController::class, 'show'])->name('activity-logs.show');

        // Enrollment Management (Admin)
        Route::get('/enrollments', [\App\Http\Controllers\Admin\EnrollmentController::class, 'index'])->name('enrollments.index');
        Route::post('/enrollments', [\App\Http\Controllers\Admin\EnrollmentController::class, 'store'])->name('enrollments.store');
        Route::patch('/enrollments/{enrollment}/approve', [\App\Http\Controllers\Admin\EnrollmentController::class, 'approve'])->name('enrollments.approve');
        Route::delete('/enrollments/{enrollment}/reject', [\App\Http\Controllers\Admin\EnrollmentController::class, 'reject'])->name('enrollments.reject');
        Route::delete('/enrollments/{enrollment}', [\App\Http\Controllers\Admin\EnrollmentController::class, 'destroy'])->name('enrollments.destroy');
    });
});
