<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Submission;
use App\Models\User;
use App\Services\User\UserFactoryManager;
use Illuminate\Database\Seeder;

/**
 * SmartCampusSeeder — Data dummy untuk testing.
 *
 * Menggunakan Abstract Factory Pattern (UserFactoryManager)
 * untuk membuat user sesuai role masing-masing.
 *
 * Ditambahkan: Courses, Enrollments, dan Assignments
 * untuk mendukung fitur Manajemen Tugas (CRUD).
 */
class SmartCampusSeeder extends Seeder
{
    public function run(): void
    {
        // ══════════════════════════════════════
        // PHASE 1: Users (Abstract Factory Pattern)
        // ══════════════════════════════════════

        // ── Admin (1 akun) ──
        $adminFactory = UserFactoryManager::getFactory('admin');
        $adminFactory->createUser([
            'name' => 'Admin SmartCampus',
            'email' => 'admin@smartcampus.ac.id',
            'password' => 'password',
        ]);

        // ── Dosen (7 akun — 1 dosen per mata kuliah) ──
        $lecturerFactory = UserFactoryManager::getFactory('dosen');

        // 1. Dr. Budi → IN235 PDPL
        $lecturerFactory->createUser(
            ['name' => 'Dr. Budi Santoso', 'email' => 'budi@smartcampus.ac.id', 'password' => 'password'],
            ['nip' => '198501012010011001', 'department' => 'Teknik Informatika', 'jabatan' => 'Lektor']
        );

        // 2. Dr. Sari → IN212 Web Dasar
        $lecturerFactory->createUser(
            ['name' => 'Dr. Sari Dewi', 'email' => 'sari@smartcampus.ac.id', 'password' => 'password'],
            ['nip' => '198802022012022002', 'department' => 'Sistem Informasi', 'jabatan' => 'Asisten Ahli']
        );

        // 3. Prof. Ahmad → MK017 Pancasila
        $lecturerFactory->createUser(
            ['name' => 'Prof. Ahmad Wijaya', 'email' => 'ahmad@smartcampus.ac.id', 'password' => 'password'],
            ['nip' => '197603032005031003', 'department' => 'Teknik Informatika', 'jabatan' => 'Guru Besar']
        );

        // 4. Dr. Dewi → IN241 Statistika
        $lecturerFactory->createUser(
            ['name' => 'Dr. Dewi Lestari', 'email' => 'dewi@smartcampus.ac.id', 'password' => 'password'],
            ['nip' => '199001012015012001', 'department' => 'Teknik Informatika', 'jabatan' => 'Lektor']
        );

        // 5. Dr. Rizki → IN242 Kecerdasan Mesin
        $lecturerFactory->createUser(
            ['name' => 'Dr. Rizki Ramadhan', 'email' => 'rizki@smartcampus.ac.id', 'password' => 'password'],
            ['nip' => '198703032013031001', 'department' => 'Teknik Informatika', 'jabatan' => 'Lektor Kepala']
        );

        // 6. Dr. Maya → IN254 Proyek PL
        $lecturerFactory->createUser(
            ['name' => 'Dr. Maya Putri', 'email' => 'maya@smartcampus.ac.id', 'password' => 'password'],
            ['nip' => '199205052018052001', 'department' => 'Sistem Informasi', 'jabatan' => 'Asisten Ahli']
        );

        // 7. Dr. Hendra → IN244 Strategi Algoritmik
        $lecturerFactory->createUser(
            ['name' => 'Dr. Hendra Kusuma', 'email' => 'hendra@smartcampus.ac.id', 'password' => 'password'],
            ['nip' => '198404042010041001', 'department' => 'Teknik Informatika', 'jabatan' => 'Lektor']
        );

        // ── Mahasiswa (5 akun) ──
        $studentFactory = UserFactoryManager::getFactory('mahasiswa');

        $studentFactory->createUser(
            ['name' => 'Francisco Valentino', 'email' => 'francisco@student.ac.id', 'password' => 'password'],
            ['nim' => '2472040', 'program_studi' => 'Teknik Informatika', 'semester' => 4, 'angkatan' => '2024']
        );

        $studentFactory->createUser(
            ['name' => 'Teofilus Juan P.', 'email' => 'juan@student.ac.id', 'password' => 'password'],
            ['nim' => '2472053', 'program_studi' => 'Teknik Informatika', 'semester' => 4, 'angkatan' => '2024']
        );

        $studentFactory->createUser(
            ['name' => 'Calvin Yohanis', 'email' => 'calvin@student.ac.id', 'password' => 'password'],
            ['nim' => '2272017', 'program_studi' => 'Teknik Informatika', 'semester' => 8, 'angkatan' => '2022']
        );

        $studentFactory->createUser(
            ['name' => 'Dave Andrew', 'email' => 'dave@student.ac.id', 'password' => 'password'],
            ['nim' => '2172015', 'program_studi' => 'Teknik Informatika', 'semester' => 10, 'angkatan' => '2021']
        );

        $studentFactory->createUser(
            ['name' => 'Andi Pratama', 'email' => 'andi@student.ac.id', 'password' => 'password'],
            ['nim' => '2472099', 'program_studi' => 'Sistem Informasi', 'semester' => 4, 'angkatan' => '2024']
        );

        // ══════════════════════════════════════
        // PHASE 2: Courses, Enrollments, Assignments
        // ══════════════════════════════════════

        $this->seedCoursesAndAssignments();
    }

    /**
     * Membuat data dummy Courses, Enrollments, dan Assignments.
     * Menggunakan query email untuk mencari user (bukan hardcoded ID).
     */
    private function seedCoursesAndAssignments(): void
    {
        // ── Ambil data dosen (7 dosen) ──
        $budi   = User::where('email', 'budi@smartcampus.ac.id')->first();
        $sari   = User::where('email', 'sari@smartcampus.ac.id')->first();
        $ahmad  = User::where('email', 'ahmad@smartcampus.ac.id')->first();
        $dewi   = User::where('email', 'dewi@smartcampus.ac.id')->first();
        $rizki  = User::where('email', 'rizki@smartcampus.ac.id')->first();
        $maya   = User::where('email', 'maya@smartcampus.ac.id')->first();
        $hendra = User::where('email', 'hendra@smartcampus.ac.id')->first();

        // ── Ambil data mahasiswa ──
        $francisco = User::where('email', 'francisco@student.ac.id')->first();
        $juan = User::where('email', 'juan@student.ac.id')->first();
        $calvin = User::where('email', 'calvin@student.ac.id')->first();
        $dave = User::where('email', 'dave@student.ac.id')->first();
        $andi = User::where('email', 'andi@student.ac.id')->first();

        // ══════════════════════════════════════
        // Buat 7 Courses (1 dosen = 1 mata kuliah)
        // ══════════════════════════════════════

        // Dr. Sari → IN212 Web Dasar
        $webDasar = Course::create([
            'lecturer_id' => $sari->lecturer->id,
            'name' => 'Web Dasar',
            'code' => 'IN212',
            'sks' => 3,
            'semester' => 'Genap 2025/2026',
            'kuota' => 40,
            'description' => 'Mata kuliah dasar pengembangan web: HTML, CSS, JavaScript, dan framework dasar.',
            'is_active' => true,
        ]);

        // Dr. Budi → IN235 PDPL
        $pdpl = Course::create([
            'lecturer_id' => $budi->lecturer->id,
            'name' => 'Pola Desain Perangkat Lunak',
            'code' => 'IN235',
            'sks' => 3,
            'semester' => 'Genap 2025/2026',
            'kuota' => 40,
            'description' => 'Mata kuliah tentang penerapan design patterns (Singleton, Factory, Command, Observer, dll) dalam pengembangan perangkat lunak.',
            'is_active' => true,
        ]);

        // Prof. Ahmad → MK017 Pancasila
        $pancasila = Course::create([
            'lecturer_id' => $ahmad->lecturer->id,
            'name' => 'Pancasila',
            'code' => 'MK017',
            'sks' => 2,
            'semester' => 'Genap 2025/2026',
            'kuota' => 50,
            'description' => 'Mata kuliah wajib tentang nilai-nilai Pancasila dan penerapannya dalam kehidupan bermasyarakat.',
            'is_active' => true,
        ]);

        // Dr. Dewi → IN241 Statistika
        $statistika = Course::create([
            'lecturer_id' => $dewi->lecturer->id,
            'name' => 'Statistika',
            'code' => 'IN241',
            'sks' => 3,
            'semester' => 'Genap 2025/2026',
            'kuota' => 40,
            'description' => 'Mata kuliah tentang statistika deskriptif, inferensial, probabilitas, dan penerapannya dalam informatika.',
            'is_active' => true,
        ]);

        // Dr. Rizki → IN242 Kecerdasan Mesin
        $kecerdasanMesin = Course::create([
            'lecturer_id' => $rizki->lecturer->id,
            'name' => 'Kecerdasan Mesin',
            'code' => 'IN242',
            'sks' => 3,
            'semester' => 'Genap 2025/2026',
            'kuota' => 35,
            'description' => 'Pengantar kecerdasan mesin: machine learning, neural networks, deep learning, dan NLP.',
            'is_active' => true,
        ]);

        // Dr. Maya → IN254 Proyek PL
        $proyekPL = Course::create([
            'lecturer_id' => $maya->lecturer->id,
            'name' => 'Proyek Perangkat Lunak',
            'code' => 'IN254',
            'sks' => 3,
            'semester' => 'Genap 2025/2026',
            'kuota' => 30,
            'description' => 'Mata kuliah proyek pengembangan perangkat lunak secara tim dengan metodologi Agile/Scrum.',
            'is_active' => true,
        ]);

        // Dr. Hendra → IN244 Strategi Algoritmik
        $stratAlgo = Course::create([
            'lecturer_id' => $hendra->lecturer->id,
            'name' => 'Strategi Algoritmik',
            'code' => 'IN244',
            'sks' => 3,
            'semester' => 'Genap 2025/2026',
            'kuota' => 35,
            'description' => 'Mata kuliah tentang strategi perancangan algoritma: brute force, greedy, dynamic programming, backtracking.',
            'is_active' => true,
        ]);

        // ══════════════════════════════════════
        // Buat Enrollments (mahasiswa ↔ course)
        // ══════════════════════════════════════

        $adminId = User::where('role', 'admin')->first()->id;

        $enrollData = [
            // Francisco: PDPL, Web Dasar, Statistika, Pancasila
            [$francisco->student->id, $pdpl->id],
            [$francisco->student->id, $webDasar->id],
            [$francisco->student->id, $statistika->id],
            [$francisco->student->id, $pancasila->id],

            // Juan: PDPL, Web Dasar, Kecerdasan Mesin, Strategi Algoritmik, Pancasila
            [$juan->student->id, $pdpl->id],
            [$juan->student->id, $webDasar->id],
            [$juan->student->id, $kecerdasanMesin->id],
            [$juan->student->id, $stratAlgo->id],
            [$juan->student->id, $pancasila->id],

            // Calvin: PDPL, Proyek PL, Kecerdasan Mesin
            [$calvin->student->id, $pdpl->id],
            [$calvin->student->id, $proyekPL->id],
            [$calvin->student->id, $kecerdasanMesin->id],

            // Dave: Statistika, Proyek PL, Strategi Algoritmik
            [$dave->student->id, $statistika->id],
            [$dave->student->id, $proyekPL->id],
            [$dave->student->id, $stratAlgo->id],

            // Andi: Web Dasar, Statistika, Pancasila
            [$andi->student->id, $webDasar->id],
            [$andi->student->id, $statistika->id],
            [$andi->student->id, $pancasila->id],
        ];

        foreach ($enrollData as [$studentId, $courseId]) {
            Enrollment::create([
                'student_id' => $studentId,
                'course_id'  => $courseId,
                'enrolled_at' => now()->subWeeks(8),
                'status'     => 'active',
                'verified_by' => $adminId,
            ]);
        }

        // ══════════════════════════════════════
        // Buat Assignments (variasi deadline)
        // ══════════════════════════════════════

        // 1. [TERLAMBAT] PDPL — Tugas Singleton Pattern (2 minggu lalu)
        Assignment::create([
            'course_id' => $pdpl->id,
            'title' => 'Tugas 1: Implementasi Singleton Pattern',
            'description' => "Implementasikan Singleton Pattern pada sebuah logger system.\n\nKetentuan:\n- Buat class Logger yang hanya memiliki satu instance\n- Implementasikan method log() untuk mencatat aktivitas\n- Buktikan bahwa hanya ada satu instance yang dibuat\n- Sertakan unit test",
            'deadline' => now()->subWeeks(2),
            'max_score' => 100,
            'file_format_allowed' => 'pdf,zip',
            'max_file_size_kb' => 10240,
            'created_by' => $budi->id,
        ]);

        // 2. [TERLAMBAT] Web Dasar — Tugas HTML/CSS (5 hari lalu)
        Assignment::create([
            'course_id' => $webDasar->id,
            'title' => 'Tugas 1: Landing Page dengan HTML & CSS',
            'description' => "Buat landing page responsif menggunakan HTML5 dan CSS3.\n\nKetentuan:\n- Minimal 3 section (Hero, About, Contact)\n- Responsif (mobile-first)\n- Gunakan Flexbox atau Grid\n- Tidak boleh menggunakan framework CSS",
            'deadline' => now()->subDays(5),
            'max_score' => 100,
            'file_format_allowed' => 'zip,rar',
            'max_file_size_kb' => 10240,
            'created_by' => $sari->id,
        ]);

        // 3. [MENDEKATI] PDPL — Tugas Command Pattern (2 hari lagi)
        Assignment::create([
            'course_id' => $pdpl->id,
            'title' => 'Tugas 2: Command Pattern pada CRUD System',
            'description' => "Implementasikan Command Pattern untuk operasi CRUD.\n\nKetentuan:\n- Buat interface Command dengan method execute()\n- Implementasikan CreateCommand, UpdateCommand, DeleteCommand\n- Buat Invoker yang menjalankan command\n- Integrasikan dengan Activity Logger",
            'deadline' => now()->addDays(2),
            'max_score' => 100,
            'file_format_allowed' => 'pdf,zip,rar',
            'max_file_size_kb' => 20480,
            'created_by' => $budi->id,
        ]);

        // 4. [MENDEKATI] Kecerdasan Mesin — Tugas Regresi (1 hari lagi)
        Assignment::create([
            'course_id' => $kecerdasanMesin->id,
            'title' => 'Tugas 1: Implementasi Linear Regression',
            'description' => "Implementasikan algoritma Linear Regression dari scratch.\n\nKetentuan:\n- Gunakan Python\n- Dataset disediakan di lampiran\n- Evaluasi menggunakan MSE dan R-squared\n- Buat visualisasi hasil prediksi",
            'deadline' => now()->addDays(1),
            'max_score' => 100,
            'file_format_allowed' => 'pdf,zip,py',
            'max_file_size_kb' => 15360,
            'created_by' => $rizki->id,
        ]);

        // 5. [AKTIF] PDPL — Tugas Besar Smart Campus (2 minggu lagi)
        Assignment::create([
            'course_id' => $pdpl->id,
            'title' => 'Tugas Besar: Smart Campus Application',
            'description' => "Bangun aplikasi Smart Campus dengan menerapkan minimal 5 design pattern.\n\nKetentuan:\n- Gunakan Laravel sebagai framework\n- Terapkan: Singleton, Factory, Decorator, Command, Observer\n- Implementasi lengkap dengan testing\n- Buat dokumentasi design pattern yang digunakan",
            'deadline' => now()->addWeeks(2),
            'max_score' => 100,
            'file_format_allowed' => 'pdf,zip,rar',
            'max_file_size_kb' => 51200,
            'created_by' => $budi->id,
        ]);

        // 6. [AKTIF] Statistika — Tugas Analisis Data (3 minggu lagi)
        Assignment::create([
            'course_id' => $statistika->id,
            'title' => 'Tugas 1: Analisis Data Deskriptif',
            'description' => "Lakukan analisis statistik deskriptif pada dataset yang diberikan.\n\nKetentuan:\n- Hitung mean, median, modus, standar deviasi\n- Buat histogram dan box plot\n- Interpretasikan hasil analisis\n- Gunakan Excel atau Python",
            'deadline' => now()->addWeeks(3),
            'max_score' => 100,
            'file_format_allowed' => 'pdf,xlsx,zip',
            'max_file_size_kb' => 10240,
            'created_by' => $dewi->id,
        ]);

        // 7. [AKTIF] Strategi Algoritmik — Tugas DP (2 minggu lagi)
        Assignment::create([
            'course_id' => $stratAlgo->id,
            'title' => 'Tugas 1: Dynamic Programming - Knapsack Problem',
            'description' => "Selesaikan variasi Knapsack Problem menggunakan Dynamic Programming.\n\nKetentuan:\n- Implementasikan solusi 0/1 Knapsack\n- Analisis kompleksitas waktu dan ruang\n- Bandingkan dengan pendekatan Greedy\n- Sertakan test case",
            'deadline' => now()->addWeeks(2),
            'max_score' => 100,
            'file_format_allowed' => 'pdf,zip,cpp,java',
            'max_file_size_kb' => 10240,
            'created_by' => $hendra->id,
        ]);

        // 8. [AKTIF] Proyek PL — Proposal Proyek (4 minggu lagi)
        Assignment::create([
            'course_id' => $proyekPL->id,
            'title' => 'Tugas 1: Proposal Proyek Perangkat Lunak',
            'description' => "Buat proposal proyek perangkat lunak secara tim.\n\nKetentuan:\n- Latar belakang dan rumusan masalah\n- Analisis kebutuhan (functional & non-functional)\n- Rancangan arsitektur sistem\n- Timeline pengerjaan (Gantt chart)",
            'deadline' => now()->addWeeks(4),
            'max_score' => 100,
            'file_format_allowed' => 'pdf,doc,docx',
            'max_file_size_kb' => 10240,
            'created_by' => $maya->id,
        ]);

        // ══════════════════════════════════════
        // Buat Dummy Submissions (mahasiswa submit tugas)
        // ══════════════════════════════════════
        $this->seedSubmissions($francisco, $juan, $calvin, $dave, $andi);
    }

    /**
     * Membuat dummy submissions dari mahasiswa.
     */
    private function seedSubmissions($francisco, $juan, $calvin, $dave, $andi): void
    {
        // Ambil assignment yang sudah lewat deadline (untuk submit realistis)
        $assignments = Assignment::orderBy('id')->get();

        // Tugas 1 (Singleton Pattern) — 3 mahasiswa submit
        if ($assignments->has(0)) {
            $a1 = $assignments[0];
            Submission::create([
                'assignment_id' => $a1->id,
                'student_id'    => $francisco->student->id,
                'file_path'     => 'submissions/1_1_singleton.pdf',
                'file_name'     => 'Singleton_Francisco.pdf',
                'file_format'   => 'pdf',
                'file_size_kb'  => 2048,
                'submitted_at'  => now()->subWeeks(3),
                'status'        => 'submitted',
                'progress'      => 'completed',
            ]);
            Submission::create([
                'assignment_id' => $a1->id,
                'student_id'    => $juan->student->id,
                'file_path'     => 'submissions/2_1_singleton.pdf',
                'file_name'     => 'Singleton_Juan.pdf',
                'file_format'   => 'pdf',
                'file_size_kb'  => 1536,
                'submitted_at'  => now()->subWeeks(2)->subDays(1),
                'status'        => 'submitted',
                'progress'      => 'completed',
            ]);
            Submission::create([
                'assignment_id' => $a1->id,
                'student_id'    => $calvin->student->id,
                'file_path'     => 'submissions/3_1_singleton.zip',
                'file_name'     => 'Singleton_Calvin.zip',
                'file_format'   => 'zip',
                'file_size_kb'  => 4096,
                'submitted_at'  => now()->subDays(10),
                'status'        => 'late',
                'progress'      => 'completed',
            ]);
        }

        // Tugas 2 (Landing Page) — 2 mahasiswa submit
        if ($assignments->has(1)) {
            $a2 = $assignments[1];
            Submission::create([
                'assignment_id' => $a2->id,
                'student_id'    => $juan->student->id,
                'file_path'     => 'submissions/2_2_landing.zip',
                'file_name'     => 'LandingPage_Juan.zip',
                'file_format'   => 'zip',
                'file_size_kb'  => 3072,
                'submitted_at'  => now()->subDays(6),
                'status'        => 'submitted',
                'progress'      => 'completed',
            ]);
            Submission::create([
                'assignment_id' => $a2->id,
                'student_id'    => $andi->student->id,
                'file_path'     => 'submissions/5_2_landing.zip',
                'file_name'     => 'LandingPage_Andi.zip',
                'file_format'   => 'zip',
                'file_size_kb'  => 5120,
                'submitted_at'  => now()->subDays(3),
                'status'        => 'late',
                'progress'      => 'completed',
            ]);
        }

        // Tugas 3 (Command Pattern) — 2 mahasiswa submit
        if ($assignments->has(2)) {
            $a3 = $assignments[2];
            Submission::create([
                'assignment_id' => $a3->id,
                'student_id'    => $francisco->student->id,
                'file_path'     => 'submissions/1_3_command.pdf',
                'file_name'     => 'CommandPattern_Francisco.pdf',
                'file_format'   => 'pdf',
                'file_size_kb'  => 1024,
                'submitted_at'  => now()->subHours(5),
                'status'        => 'submitted',
                'progress'      => 'on_progress',
            ]);
            Submission::create([
                'assignment_id' => $a3->id,
                'student_id'    => $juan->student->id,
                'file_path'     => 'submissions/2_3_command.zip',
                'file_name'     => 'CommandPattern_Juan.zip',
                'file_format'   => 'zip',
                'file_size_kb'  => 8192,
                'submitted_at'  => now()->subHours(2),
                'status'        => 'submitted',
                'progress'      => 'on_progress',
            ]);
        }

        // Tugas 5 (Smart Campus) — 1 mahasiswa submit
        if ($assignments->has(4)) {
            $a5 = $assignments[4];
            Submission::create([
                'assignment_id' => $a5->id,
                'student_id'    => $juan->student->id,
                'file_path'     => 'submissions/2_5_smartcampus.zip',
                'file_name'     => 'SmartCampus_Juan_Draft.zip',
                'file_format'   => 'zip',
                'file_size_kb'  => 15360,
                'submitted_at'  => now()->subDays(1),
                'status'        => 'submitted',
                'progress'      => 'on_progress',
            ]);
        }
    }
}
