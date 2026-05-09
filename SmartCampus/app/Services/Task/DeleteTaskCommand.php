<?php

namespace App\Services\Task;

use App\Contracts\TaskCommandInterface;
use App\Models\Assignment;

/**
 * DeleteTaskCommand — Concrete Command (Command Pattern)
 *
 * Mengenkapsulasi operasi penghapusan tugas sebagai objek command.
 * Command ini menyimpan snapshot lengkap data assignment sebelum dihapus,
 * yang dapat digunakan untuk fitur Undo di masa depan.
 *
 * Prinsip Clean Code:
 * - Single Responsibility: Hanya bertanggung jawab menghapus assignment
 * - Data Preservation: Menyimpan snapshot lengkap sebelum penghapusan
 * - Defensive Programming: Menyimpan ID sebelum delete agar tidak hilang
 */
class DeleteTaskCommand implements TaskCommandInterface
{
    /**
     * Snapshot lengkap data assignment sebelum dihapus (untuk undo/audit).
     */
    private array $snapshot;

    /**
     * ID assignment yang akan dihapus (disimpan sebelum delete).
     */
    private int $assignmentId;

    /**
     * @param Assignment $assignment Assignment yang akan dihapus
     */
    public function __construct(
        private readonly Assignment $assignment
    ) {
        // Simpan ID dan snapshot sebelum penghapusan
        $this->assignmentId = $this->assignment->id;
        $this->snapshot = $this->assignment->toArray();
    }

    /**
     * Eksekusi penghapusan tugas dari database.
     * Submissions terkait akan terhapus otomatis (cascade delete di migration).
     *
     * @return bool True jika berhasil dihapus
     */
    public function execute(): mixed
    {
        return $this->assignment->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function getAction(): string
    {
        return 'DELETE_ASSIGNMENT';
    }

    /**
     * {@inheritdoc}
     * Menyimpan snapshot lengkap data yang dihapus untuk audit trail dan undo.
     */
    public function getDetail(): array
    {
        return [
            'deleted' => $this->snapshot,
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
     * Mengembalikan ID yang disimpan sebelum penghapusan.
     */
    public function getTargetId(): ?int
    {
        return $this->assignmentId;
    }
}
