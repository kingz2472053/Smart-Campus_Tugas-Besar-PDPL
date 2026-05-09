<?php

namespace App\Contracts;

/**
 * TaskCommandInterface — Command Pattern Interface
 *
 * Mendefinisikan kontrak untuk semua command yang berkaitan
 * dengan manajemen tugas (Create, Edit, Delete).
 * Setiap command mengenkapsulasi satu operasi CRUD sebagai objek.
 *
 * Prinsip Clean Code:
 * - Single Responsibility: Setiap command hanya melakukan satu operasi
 * - Interface Segregation: Interface ini fokus pada task commands saja
 * - Open/Closed: Mudah menambah command baru tanpa mengubah kode existing
 */
interface TaskCommandInterface
{
    /**
     * Menjalankan command dan mengembalikan hasil operasi.
     *
     * @return mixed Hasil eksekusi (Assignment model atau boolean)
     */
    public function execute(): mixed;

    /**
     * Mendapatkan nama aksi untuk logging.
     * Contoh: 'CREATE_ASSIGNMENT', 'UPDATE_ASSIGNMENT', 'DELETE_ASSIGNMENT'
     *
     * @return string Nama aksi yang deskriptif
     */
    public function getAction(): string;

    /**
     * Mendapatkan detail data untuk audit trail.
     * Data ini disimpan di activity_logs.detail_json
     * dan dapat digunakan untuk fitur Undo/Redo.
     *
     * @return array Data detail (before/after untuk edit, snapshot untuk delete)
     */
    public function getDetail(): array;

    /**
     * Mendapatkan nama tabel target operasi.
     *
     * @return string Nama tabel (contoh: 'assignments')
     */
    public function getTargetTable(): string;

    /**
     * Mendapatkan ID record target operasi.
     * Null jika record belum dibuat (pada CreateTaskCommand).
     *
     * @return int|null ID record target
     */
    public function getTargetId(): ?int;
}
