<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Grade;

class CourseController extends Controller
{
    /**
     * Menampilkan daftar mata kuliah yang di-enroll mahasiswa.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Guard clause: Pastikan user adalah mahasiswa
        if ($user->role !== 'mahasiswa' || !$user->student) {
            abort(403, 'Akses ditolak.');
        }

        // Ambil mata kuliah yang status enrollment-nya active
        $courses = Course::whereHas('enrollments', function ($query) use ($user) {
            $query->where('student_id', $user->student->id)
                  ->where('status', 'active');
        })->with('lecturer.user')->get();

        return view('courses.index', compact('courses'));
    }

    /**
     * Menampilkan daftar nilai tugas untuk satu mata kuliah spesifik.
     */
    public function grades(Course $course)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Validasi: Apakah mahasiswa terdaftar di mata kuliah ini?
        $isEnrolled = $course->enrollments()
            ->where('student_id', $user->student->id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'Anda tidak terdaftar di mata kuliah ini.');
        }

        // Ambil semua tugas untuk mata kuliah ini, beserta submission & nilai mahasiswa
        $assignments = $course->assignments()
            ->with(['submissions' => function ($query) use ($user) {
                $query->where('student_id', $user->student->id)->with('latestGrade');
            }])
            ->orderBy('deadline', 'desc')
            ->get();

        return view('courses.grades', compact('course', 'assignments'));
    }
}