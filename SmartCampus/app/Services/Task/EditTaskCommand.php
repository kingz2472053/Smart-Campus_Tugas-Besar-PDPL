<?php

namespace App\Services\Task;

use App\Contracts\TaskCommandInterface;
use App\Models\Assignment;

/**
 * EditTaskCommand — Concrete Command (Command Pattern)
 *
 * Mengenkapsulasi operasi pengeditan tugas sebagai objek command.
 * Command ini menyimpan data sebelum dan sesudah perubahan,
 * yang dapat digunakan untuk fitur Undo/Redo di masa depan.
 *
 * Prinsip Clean Code:
 * - Single Responsibility: Hanya bertanggung jawab mengedit assignment
 * - Data Preservation: Menyimpan snapshot data sebelum perubahan
 * - Immutability: Data baru (newData) tidak diubah setelah konstruksi
 */
class EditTaskCommand implements TaskCommandInterface
{
    /**
     * Snapshot data sebelum perubahan (untuk undo/audit).
     */
    private array $oldData = [];

    /**
     * @param Assignment $assignment Assignment yang akan diedit
     * @param array      $newData    Data baru untuk update
     *                               Keys: title, course_id, description, deadline,
     *                                     max_score, file_format_allowed, max_file_size_kb
     */
    public function __construct(
        private readonly Assignment $assignment,
        private readonly array $newData
    ) {
        // Simpan snapshot data sebelum perubahan (mendukung undo/redo)
        $this->oldData = $this->assignment->only([
            'title',
            'course_id',
            'description',
            'deadline',
            'max_score',
            'file_format_allowed',
            'max_file_size_kb',
        ]);
    }

    /**
     * Eksekusi update tugas di database.
     *
     * @return Assignment Model assignment yang berhasil diupdate
     */
    public function execute(): mixed
    {
        $this->assignment->update($this->newData);

        return $this->assignment->fresh();
    }

    /**
     * {@inheritdoc}
     */
    public function getAction(): string
    {
        return 'UPDATE_ASSIGNMENT';
    }

    /**
     * {@inheritdoc}
     * Menyimpan data sebelum dan sesudah perubahan untuk audit trail dan undo/redo.
     */
    public function getDetail(): array
    {
        return [
            'before' => $this->oldData,
            'after'  => $this->newData,
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
     */
    public function getTargetId(): ?int
    {
        return $this->assignment->id;
    }
}
