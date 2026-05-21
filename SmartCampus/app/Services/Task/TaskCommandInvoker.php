<?php

namespace App\Services\Task;

use App\Contracts\TaskCommandInterface;
use App\Services\ActivityLogger;

/**
 * TaskCommandInvoker — Invoker (Command Pattern)
 *
 * Bertanggung jawab menjalankan command dan mencatat aktivitas
 * secara otomatis ke ActivityLogger (Singleton Pattern).
 *
 * Invoker ini memisahkan pemanggil (controller) dari eksekusi command,
 * sehingga setiap operasi CRUD dijamin tercatat di audit trail.
 *
 * Prinsip Clean Code:
 * - Single Responsibility: Hanya menjalankan command dan logging
 * - Dependency Inversion: Bergantung pada interface, bukan concrete class
 * - DRY: Logging terpusat, tidak perlu diulang di setiap controller method
 *
 * Integrasi Design Pattern:
 * - Command Pattern: Menerima dan menjalankan TaskCommandInterface
 * - Singleton Pattern: Menggunakan ActivityLogger::getInstance()
 */
class TaskCommandInvoker
{
    /**
     * Menjalankan command dan mencatat hasilnya ke ActivityLogger.
     *
     * Alur eksekusi:
     * 1. Jalankan command->execute() untuk operasi database
     * 2. Catat aktivitas ke activity_logs via Singleton Logger
     * 3. Kembalikan hasil eksekusi ke pemanggil
     *
     * @param TaskCommandInterface $command Command yang akan dijalankan
     * @param int                  $userId  ID pengguna yang menjalankan command
     * @return mixed Hasil eksekusi command
     */
    public function execute(TaskCommandInterface $command, int $userId): mixed
    {
        // 1. Eksekusi command (operasi database)
        $result = $command->execute();

        // 2. Catat ke ActivityLogger (Singleton Pattern)
        ActivityLogger::getInstance()->log(
            action: $command->getAction(),
            userId: $userId,
            targetTable: $command->getTargetTable(),
            targetId: $command->getTargetId(),
            detail: $command->getDetail()
        );

        // 3. Kembalikan hasil
        return $result;
    }
}
