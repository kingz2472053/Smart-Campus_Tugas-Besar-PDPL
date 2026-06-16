<?php

namespace App\Http\Controllers\Mahasiswa;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    /**
     * Menampilkan daftar kelas yang tersedia untuk didaftarkan.
     */
    public function index()
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            abort(403, 'Akses ditolak.');
        }

        // Ambil ID kelas yang sudah di-enroll (baik pending maupun active)
        $enrolledCourseIds = Enrollment::where('student_id', $student->id)
            ->pluck('course_id')
            ->toArray();

        // Ambil status enrollment mahasiswa per course
        $myEnrollments = Enrollment::where('student_id', $student->id)
            ->get()
            ->keyBy('course_id');

        // Ambil kelas yang tersedia (belum di-enroll)
        $availableCourses = Course::where('is_active', true)
            ->whereNotIn('id', $enrolledCourseIds)
            ->with('lecturer.user')
            ->withCount(['enrollments as active_students_count' => function ($query) {
                $query->where('status', 'active');
            }])
            ->orderBy('name')
            ->get();

        // Kelas yang sudah di-enroll (pending & active)
        $enrolledCourses = Course::whereIn('id', $enrolledCourseIds)
            ->with('lecturer.user')
            ->get()
            ->map(function ($course) use ($myEnrollments) {
                $course->enrollment_status = $myEnrollments[$course->id]->status ?? null;
                return $course;
            });

        return view('mahasiswa.enrollments.index', compact('availableCourses', 'enrolledCourses'));
    }

    /**
     * Mendaftar ke kelas (status pending).
     */
    public function store(Course $course)
    {
        $user = Auth::user();
        $student = $user->student;

        if (!$student) {
            abort(403, 'Akses ditolak.');
        }

        // Cek apakah sudah terdaftar
        $exists = Enrollment::where('student_id', $student->id)
            ->where('course_id', $course->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Anda sudah terdaftar di kelas ini.');
        }

        // Cek kuota
        $activeCount = $course->enrollments()->where('status', 'active')->count();
        if ($course->kuota && $activeCount >= $course->kuota) {
            return back()->with('error', 'Kuota kelas ini sudah penuh.');
        }

        Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Pendaftaran kelas berhasil diajukan. Menunggu persetujuan admin.');
    }
}
