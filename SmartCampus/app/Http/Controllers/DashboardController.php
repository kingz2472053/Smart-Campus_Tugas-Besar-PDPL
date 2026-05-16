<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification; // Tambahkan import ini

/**
 * DashboardController — Mengarahkan pengguna ke dashboard sesuai role.
 *
 * Menggunakan Abstract Factory Pattern: role menentukan tampilan yang dimuat.
 */
class DashboardController extends Controller
{
    /**
     * Redirect ke dashboard sesuai role pengguna.
     */
    public function index()
    {
        $user = Auth::user();

        return match ($user->role) {
            'admin' => $this->adminDashboard(),
            'dosen' => $this->lecturerDashboard(),
            'mahasiswa' => $this->studentDashboard(),
            default => abort(403),
        };
    }

    /**
     * Dashboard Mahasiswa.
     */
    private function studentDashboard()
    {
        $user = Auth::user();
        $student = $user->student;

        $data = [
            'user' => $user,
            'student' => $student,
            'enrollmentCount' => $student ? $student->enrollments()->where('status', 'active')->count() : 0,
            'submissionCount' => $student ? $student->submissions()->count() : 0,
            'pendingCount' => $student ? $student->submissions()->where('progress', 'not_started')->count() : 0,
            'notifications' => Notification::where('user_id', $user->id)
                                ->orderBy('created_at', 'desc')
                                ->limit(5)
                                ->get(),
        ];

        return view('dashboard.student', $data);
    }

    /**
     * Dashboard Dosen.
     */
    private function lecturerDashboard()
    {
        $user = Auth::user();
        $lecturer = $user->lecturer;

        $data = [
            'user' => $user,
            'lecturer' => $lecturer,
            'courseCount' => $lecturer ? $lecturer->courses()->count() : 0,
            'assignmentCount' => $lecturer ? $lecturer->courses()->withCount('assignments')->get()->sum('assignments_count') : 0,
        ];

        return view('dashboard.lecturer', $data);
    }

    /**
     * Dashboard Admin.
     */
    private function adminDashboard()
    {
        $user = Auth::user();

        $data = [
            'user' => $user,
            'userCount' => \App\Models\User::count(),
            'courseCount' => \App\Models\Course::count(),
            'recentLogs' => \App\Models\ActivityLog::with('user')
                ->orderBy('timestamp', 'desc')
                ->limit(10)
                ->get(),
        ];

        return view('dashboard.admin', $data);
    }
}
