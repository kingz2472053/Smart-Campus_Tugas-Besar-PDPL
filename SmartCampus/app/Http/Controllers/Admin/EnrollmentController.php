<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnrollmentController extends Controller
{
    /**
     * Menampilkan semua data enrollment.
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'pending');

        $pendingCount = Enrollment::where('status', 'pending')->count();
        $activeCount = Enrollment::where('status', 'active')->count();

        if ($tab === 'pending') {
            $enrollments = Enrollment::where('status', 'pending')
                ->with(['student.user', 'course.lecturer.user'])
                ->latest()
                ->paginate(15)
                ->withQueryString();
        } else {
            $enrollments = Enrollment::where('status', 'active')
                ->with(['student.user', 'course.lecturer.user', 'verifier'])
                ->latest()
                ->paginate(15)
                ->withQueryString();
        }

        // Data untuk form manual enroll
        $courses = Course::with('lecturer.user')->where('is_active', true)->orderBy('name')->get();
        $students = Student::with('user')->whereHas('user', function ($q) {
            $q->where('is_active', true);
        })->get();

        return view('admin.enrollments.index', compact('enrollments', 'tab', 'pendingCount', 'activeCount', 'courses', 'students'));
    }

    /**
     * Approve pendaftaran mahasiswa.
     */
    public function approve(Enrollment $enrollment)
    {
        $enrollment->update([
            'status' => 'active',
            'verified_by' => Auth::id(),
        ]);

        return back()->with('success', 'Pendaftaran mahasiswa berhasil disetujui.');
    }

    /**
     * Reject pendaftaran mahasiswa.
     */
    public function reject(Enrollment $enrollment)
    {
        $enrollment->delete();

        return back()->with('success', 'Pendaftaran mahasiswa berhasil ditolak.');
    }

    /**
     * Manual enroll mahasiswa ke kelas.
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'course_id' => 'required|exists:courses,id',
        ]);

        // Cek apakah sudah terdaftar
        $exists = Enrollment::where('student_id', $request->student_id)
            ->where('course_id', $request->course_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Mahasiswa sudah terdaftar di kelas ini.');
        }

        Enrollment::create([
            'student_id' => $request->student_id,
            'course_id' => $request->course_id,
            'status' => 'active',
            'verified_by' => Auth::id(),
        ]);

        return back()->with('success', 'Mahasiswa berhasil didaftarkan ke kelas.');
    }

    /**
     * Hapus enrollment.
     */
    public function destroy(Enrollment $enrollment)
    {
        $enrollment->delete();

        return back()->with('success', 'Data pendaftaran berhasil dihapus.');
    }
}
