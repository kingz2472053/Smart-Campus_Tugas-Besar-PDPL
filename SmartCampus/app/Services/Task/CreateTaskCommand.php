<?php

namespace App\Services\Task;

use App\Contracts\TaskCommandInterface;
use App\Models\Assignment;

/**
 * CreateTaskCommand — Concrete Command (Command Pattern)
 *
 * Mengenkapsulasi operasi pembuatan tugas baru sebagai objek command.
 * Command ini menerima data tugas dan mengeksekusi pembuatan record
 * di tabel assignments.
 *
 * Prinsip Clean Code:
 * - Single Responsibility: Hanya bertanggung jawab membuat assignment
 * - Encapsulation: Data dan logika pembuatan dibungkus dalam satu objek
 * - Immutability: Data yang diterima tidak diubah setelah konstruksi
 */
class CreateTaskCommand implements TaskCommandInterface
{
    /**
     * Assignment yang berhasil dibuat (diisi setelah execute).
     */
    private ?Assignment $createdAssignment = null;

    /**
     * @param array $data Data tugas yang akan dibuat
     *                    Keys: title, course_id, description, deadline,
     *                          max_score, file_format_allowed, max_file_size_kb, created_by
     */
    public function __construct(
        private readonly array $data
    ) {}

    /**
     * Eksekusi pembuatan tugas baru di database.
     *
     * @return Assignment Model assignment yang berhasil dibuat
     */
    public function execute(): mixed
    {
        $this->createdAssignment = Assignment::create($this->data);

        if ($this->createdAssignment) {
            try {
                // Ambil semua mahasiswa yang mengambil kelas ini
                $enrollments = \App\Models\Enrollment::where('course_id', $this->createdAssignment->course_id)
                    ->with('student.user')
                    ->get();

                $sender = new \App\Services\Notification\DashboardNotificationSender();
                $courseName = $this->createdAssignment->course->name ?? 'Mata Kuliah';
                $deadline = $this->createdAssignment->deadline ? $this->createdAssignment->deadline->format('d M Y, H:i') : '-';

                foreach ($enrollments as $enrollment) {
                    if ($enrollment->student && $enrollment->student->user) {
                        $sender->sendNotification(
                            $enrollment->student->user,
                            "Tugas baru dipublikasikan: '{$this->createdAssignment->title}' untuk mata kuliah {$courseName} (Deadline: {$deadline}).",
                            ['assignment_id' => $this->createdAssignment->id]
                        );
                    }
                }
            } catch (\Exception $e) {
                // Tangkap jika terjadi error relasi agar proses creation tidak gagal
                \Illuminate\Support\Facades\Log::error("Failed to notify students on task creation: " . $e->getMessage());
            }
        }

        return $this->createdAssignment;
    }

    /**
     * {@inheritdoc}
     */
    public function getAction(): string
    {
        return 'CREATE_ASSIGNMENT';
    }

    /**
     * {@inheritdoc}
     * Menyimpan data assignment yang dibuat untuk audit trail.
     */
    public function getDetail(): array
    {
        return [
            'created' => $this->data,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTargetTable(): string
    {
        return 'assignments';
    }

    /**
     * {@inheritdoc}
     * Mengembalikan ID assignment setelah dibuat, null jika belum dieksekusi.
     */
    public function getTargetId(): ?int
    {
        return $this->createdAssignment?->id;
    }
}
