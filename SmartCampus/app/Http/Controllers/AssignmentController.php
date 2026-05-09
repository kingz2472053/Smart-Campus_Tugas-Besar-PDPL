<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use App\Services\Task\CreateTaskCommand;
use App\Services\Task\EditTaskCommand;
use App\Services\Task\DeleteTaskCommand;
use App\Services\Task\TaskCommandInvoker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * AssignmentController — Mengelola CRUD tugas menggunakan Command Pattern.
 *
 * Controller ini berfungsi sebagai Client dalam Command Pattern:
 * - Membuat command object (CreateTaskCommand, EditTaskCommand, DeleteTaskCommand)
 * - Mengirim command ke TaskCommandInvoker untuk dieksekusi
 * - Invoker secara otomatis mencatat aktivitas via ActivityLogger (Singleton)
 *
 * Akses berdasarkan role:
 * - Dosen: Full CRUD (hanya tugas miliknya)
 * - Mahasiswa: Read-only (tugas dari enrolled courses)
 * - Admin: Read-only (semua tugas)
 *
 * Prinsip Clean Code:
 * - Single Responsibility: Controller hanya menangani HTTP request/response
 * - Dependency Injection: TaskCommandInvoker di-inject via constructor
 * - Guard Clauses: Early return untuk validasi akses
 * - Meaningful Names: Method dan variabel deskriptif
 */
class AssignmentController extends Controller
{
    /**
     * Invoker untuk menjalankan command (Command Pattern).
     */
    private TaskCommandInvoker $invoker;

    public function __construct()
    {
        $this->invoker = new TaskCommandInvoker();
    }

    // ──────────────────────────────────────
    // READ Operations (Semua Role)
    // ──────────────────────────────────────

    /**
     * Menampilkan daftar tugas berdasarkan role pengguna.
     * - Dosen: tugas yang dibuat olehnya
     * - Mahasiswa: tugas dari mata kuliah yang di-enroll
     * - Admin: semua tugas
     *
     * Mendukung search (judul) dan filter (mata kuliah, status deadline).
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Assignment::with(['course', 'creator']);

        // Role-based query filtering
        $query = $this->applyRoleFilter($query, $user);

        // Search berdasarkan judul tugas
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where('title', 'like', "%{$searchTerm}%");
        }

        // Filter berdasarkan mata kuliah
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->input('course_id'));
        }

        // Filter berdasarkan status deadline
        if ($request->filled('status')) {
            $query = $this->applyDeadlineFilter($query, $request->input('status'));
        }

        // Urutkan berdasarkan deadline terdekat
        $assignments = $query->orderBy('deadline', 'asc')->paginate(10);

        // Daftar courses untuk filter dropdown (role-aware)
        $courses = $this->getCoursesForUser($user);

        return view('assignments.index', compact('assignments', 'courses', 'user'));
    }

    /**
     * Menampilkan detail tugas lengkap.
     * - Dosen/Admin: melihat daftar submissions mahasiswa
     * - Mahasiswa: melihat status submission miliknya
     */
    public function show(Assignment $assignment)
    {
        $user = Auth::user();

        // Guard clause: Mahasiswa hanya bisa lihat tugas dari enrolled courses
        if ($user->role === 'mahasiswa') {
            $this->authorizeStudentAccess($user, $assignment);
        }

        // Eager load relasi yang dibutuhkan
        $assignment->load(['course.lecturer.user', 'creator', 'submissions.student.user']);

        // Data submission mahasiswa (jika role mahasiswa)
        $mySubmission = null;
        if ($user->role === 'mahasiswa' && $user->student) {
            $mySubmission = $assignment->submissions
                ->where('student_id', $user->student->id)
                ->first();
        }

        return view('assignments.show', compact('assignment', 'user', 'mySubmission'));
    }

    // ──────────────────────────────────────
    // CREATE Operation (Dosen Only)
    // ──────────────────────────────────────

    /**
     * Menampilkan form pembuatan tugas baru.
     * Hanya dosen yang memiliki akses.
     */
    public function create()
    {
        $user = Auth::user();
        $courses = $this->getDosenCourses($user);

        // Guard clause: Dosen harus punya minimal 1 mata kuliah
        if ($courses->isEmpty()) {
            return redirect()
                ->route('dosen.assignments.index')
                ->with('error', 'Anda belum memiliki mata kuliah. Hubungi admin untuk menambahkan.');
        }

        return view('assignments.create', compact('courses'));
    }

    /**
     * Menyimpan tugas baru menggunakan CreateTaskCommand.
     *
     * Alur Command Pattern:
     * 1. Validasi input
     * 2. Buat CreateTaskCommand dengan data tervalidasi
     * 3. Kirim ke Invoker untuk eksekusi + logging otomatis
     */
    public function store(Request $request)
    {
        $validated = $this->validateAssignment($request);

        // Tambahkan created_by (dosen yang membuat)
        $validated['created_by'] = Auth::id();

        // Command Pattern: Enkapsulasi operasi create sebagai command
        $command = new CreateTaskCommand($validated);
        $assignment = $this->invoker->execute($command, Auth::id());

        return redirect()
            ->route('dosen.assignments.show', $assignment)
            ->with('success', 'Tugas "' . $assignment->title . '" berhasil dibuat.');
    }

    // ──────────────────────────────────────
    // UPDATE Operation (Dosen Only)
    // ──────────────────────────────────────

    /**
     * Menampilkan form edit tugas.
     * Guard clause: hanya pemilik tugas yang bisa edit.
     */
    public function edit(Assignment $assignment)
    {
        $user = Auth::user();

        // Guard clause: Hanya pemilik tugas yang bisa edit
        $this->authorizeOwnership($user, $assignment);

        $courses = $this->getDosenCourses($user);

        return view('assignments.edit', compact('assignment', 'courses'));
    }

    /**
     * Mengupdate tugas menggunakan EditTaskCommand.
     *
     * Alur Command Pattern:
     * 1. Validasi input
     * 2. Buat EditTaskCommand dengan assignment dan data baru
     * 3. Kirim ke Invoker → menyimpan data before/after + logging
     */
    public function update(Request $request, Assignment $assignment)
    {
        $user = Auth::user();

        // Guard clause: Hanya pemilik tugas yang bisa update
        $this->authorizeOwnership($user, $assignment);

        $validated = $this->validateAssignment($request);

        // Command Pattern: Enkapsulasi operasi edit sebagai command
        $command = new EditTaskCommand($assignment, $validated);
        $this->invoker->execute($command, Auth::id());

        return redirect()
            ->route('dosen.assignments.show', $assignment)
            ->with('success', 'Tugas "' . $assignment->title . '" berhasil diperbarui.');
    }

    // ──────────────────────────────────────
    // DELETE Operation (Dosen Only)
    // ──────────────────────────────────────

    /**
     * Menghapus tugas menggunakan DeleteTaskCommand.
     *
     * Alur Command Pattern:
     * 1. Buat DeleteTaskCommand (menyimpan snapshot sebelum hapus)
     * 2. Kirim ke Invoker → eksekusi delete + logging snapshot
     */
    public function destroy(Assignment $assignment)
    {
        $user = Auth::user();

        // Guard clause: Hanya pemilik tugas yang bisa hapus
        $this->authorizeOwnership($user, $assignment);

        $title = $assignment->title;

        // Command Pattern: Enkapsulasi operasi delete sebagai command
        $command = new DeleteTaskCommand($assignment);
        $this->invoker->execute($command, Auth::id());

        return redirect()
            ->route('dosen.assignments.index')
            ->with('success', 'Tugas "' . $title . '" berhasil dihapus.');
    }

    // ──────────────────────────────────────
    // Private Helper Methods (Clean Code: Extract Method)
    // ──────────────────────────────────────

    /**
     * Validasi input assignment dengan aturan yang konsisten.
     * Digunakan oleh store() dan update() — prinsip DRY.
     *
     * @return array Data yang sudah tervalidasi
     */
    private function validateAssignment(Request $request): array
    {
        return $request->validate([
            'course_id'            => 'required|exists:courses,id',
            'title'                => 'required|string|max:255',
            'description'          => 'nullable|string|max:5000',
            'deadline'             => 'required|date|after:now',
            'max_score'            => 'required|integer|min:1|max:100',
            'file_format_allowed'  => 'required|string|max:100',
            'max_file_size_kb'     => 'required|integer|min:100|max:51200',
        ], [
            'course_id.required'   => 'Mata kuliah wajib dipilih.',
            'course_id.exists'     => 'Mata kuliah tidak ditemukan.',
            'title.required'       => 'Judul tugas wajib diisi.',
            'title.max'            => 'Judul tugas maksimal 255 karakter.',
            'description.max'      => 'Deskripsi maksimal 5000 karakter.',
            'deadline.required'    => 'Deadline wajib diisi.',
            'deadline.after'       => 'Deadline harus lebih dari waktu sekarang.',
            'max_score.required'   => 'Skor maksimal wajib diisi.',
            'max_score.min'        => 'Skor minimal adalah 1.',
            'max_score.max'        => 'Skor maksimal adalah 100.',
            'file_format_allowed.required' => 'Format file wajib diisi.',
            'max_file_size_kb.required'    => 'Ukuran file maksimal wajib diisi.',
            'max_file_size_kb.min'         => 'Ukuran file minimal 100 KB.',
            'max_file_size_kb.max'         => 'Ukuran file maksimal 50 MB.',
        ]);
    }

    /**
     * Terapkan filter query berdasarkan role pengguna.
     * - Dosen: hanya tugas yang dibuat olehnya
     * - Mahasiswa: hanya tugas dari courses yang di-enroll
     * - Admin: semua tugas (tanpa filter)
     */
    private function applyRoleFilter($query, $user)
    {
        return match ($user->role) {
            'dosen' => $query->where('created_by', $user->id),

            'mahasiswa' => $query->whereIn(
                'course_id',
                $user->student
                    ? $user->student->enrollments()
                        ->where('status', 'active')
                        ->pluck('course_id')
                    : []
            ),

            'admin' => $query, // Admin melihat semua

            default => $query->whereRaw('1 = 0'), // Fallback: tidak ada akses
        };
    }

    /**
     * Terapkan filter berdasarkan status deadline.
     * - active: deadline > 3 hari dari sekarang
     * - upcoming: deadline <= 3 hari dari sekarang
     * - overdue: deadline sudah lewat
     */
    private function applyDeadlineFilter($query, string $status)
    {
        return match ($status) {
            'active'   => $query->where('deadline', '>', now()->addDays(3)),
            'upcoming' => $query->where('deadline', '<=', now()->addDays(3))
                                ->where('deadline', '>', now()),
            'overdue'  => $query->where('deadline', '<', now()),
            default    => $query,
        };
    }

    /**
     * Mendapatkan daftar mata kuliah berdasarkan role.
     * - Dosen: mata kuliah yang diampu
     * - Mahasiswa: mata kuliah yang di-enroll
     * - Admin: semua mata kuliah
     */
    private function getCoursesForUser($user)
    {
        return match ($user->role) {
            'dosen'     => $this->getDosenCourses($user),
            'mahasiswa' => $this->getMahasiswaCourses($user),
            'admin'     => Course::orderBy('name')->get(),
            default     => collect(),
        };
    }

    /**
     * Mendapatkan mata kuliah yang diampu oleh dosen.
     */
    private function getDosenCourses($user)
    {
        if (!$user->lecturer) {
            return collect();
        }

        return Course::where('lecturer_id', $user->lecturer->id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    /**
     * Mendapatkan mata kuliah yang di-enroll oleh mahasiswa.
     */
    private function getMahasiswaCourses($user)
    {
        if (!$user->student) {
            return collect();
        }

        $courseIds = $user->student->enrollments()
            ->where('status', 'active')
            ->pluck('course_id');

        return Course::whereIn('id', $courseIds)->orderBy('name')->get();
    }

    /**
     * Guard clause: Verifikasi bahwa dosen adalah pemilik tugas.
     * Abort 403 jika bukan pemilik.
     */
    private function authorizeOwnership($user, Assignment $assignment): void
    {
        if ($assignment->created_by !== $user->id) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola tugas ini.');
        }
    }

    /**
     * Guard clause: Verifikasi bahwa mahasiswa memiliki akses ke tugas
     * (melalui enrollment di course terkait).
     */
    private function authorizeStudentAccess($user, Assignment $assignment): void
    {
        if (!$user->student) {
            abort(403, 'Profil mahasiswa tidak ditemukan.');
        }

        $isEnrolled = $user->student->enrollments()
            ->where('course_id', $assignment->course_id)
            ->where('status', 'active')
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'Anda tidak terdaftar di mata kuliah ini.');
        }
    }
}
