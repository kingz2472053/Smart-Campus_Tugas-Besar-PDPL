<?php

namespace App\Http\Controllers\Dosen;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Menampilkan daftar mata kuliah yang diajar oleh dosen.
     */
    public function index()
    {
        $user = Auth::user();
        $lecturer = $user->lecturer;

        if (!$lecturer) {
            abort(403, 'Akses ditolak.');
        }

        $courses = Course::where('lecturer_id', $lecturer->id)
            ->withCount(['enrollments as active_students_count' => function ($query) {
                $query->where('status', 'active');
            }])
            ->latest()
            ->get();

        return view('dosen.courses.index', compact('courses'));
    }

    /**
     * Menampilkan detail kelas beserta daftar mahasiswa yang terdaftar.
     */
    public function show(Course $course)
    {
        $user = Auth::user();
        $lecturer = $user->lecturer;

        // Pastikan dosen hanya bisa melihat kelas miliknya
        if (!$lecturer || $course->lecturer_id !== $lecturer->id) {
            abort(403, 'Anda tidak memiliki akses ke kelas ini.');
        }

        $enrollments = $course->enrollments()
            ->where('status', 'active')
            ->with('student.user')
            ->latest('enrolled_at')
            ->get();

        return view('dosen.courses.show', compact('course', 'enrollments'));
    }
}
