<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * ActivityLogger — Singleton Pattern
 *
 * Memastikan hanya ada satu instance logger di seluruh siklus hidup aplikasi.
 * Semua komponen sistem memanggil ActivityLogger::getInstance() untuk
 * mendapatkan instance yang sama. Mencatat semua aktivitas penting ke
 * tabel ACTIVITY_LOG untuk audit trail.
 *
 * Cross-cutting concern yang digunakan oleh semua design pattern lain.
 */
class ActivityLogger
{
    /**
     * Instance tunggal (Singleton).
     */
    private static ?ActivityLogger $instance = null;

    /**
     * Private constructor — mencegah instansiasi langsung dari luar.
     */
    private function __construct()
    {
        // Private: tidak bisa di-new dari luar kelas
    }

    /**
     * Mendapatkan instance tunggal ActivityLogger.
     * Menggunakan lazy initialization.
     *
     * @return ActivityLogger
     */
    public static function getInstance(): ActivityLogger
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Mencatat aktivitas ke tabel ACTIVITY_LOG.
     *
     * @param string $action Jenis aksi (LOGIN, CREATE_TASK, SUBMIT, dll.)
     * @param int|null $userId ID pengguna yang melakukan aksi
     * @param string|null $targetTable Tabel yang terpengaruh
     * @param int|null $targetId ID record yang terpengaruh
     * @param array|null $detail Data sebelum/sesudah perubahan (untuk Undo/Redo)
     */
    public function log(
        string $action,
        ?int $userId = null,
        ?string $targetTable = null,
        ?int $targetId = null,
        ?array $detail = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $userId ?? Auth::id() ?? 0,
            'action' => $action,
            'target_table' => $targetTable,
            'target_id' => $targetId,
            'detail_json' => $detail,
            'ip_address' => Request::ip(),
            'timestamp' => now(),
        ]);
    }

    /**
     * Mengambil log berdasarkan filter.
     *
     * @param array $filters Filter opsional: user_id, action, date_from, date_to
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLogs(array $filters = [])
    {
        $query = ActivityLog::with('user')->orderBy('timestamp', 'desc');

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (!empty($filters['date_from'])) {
            $query->where('timestamp', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->where('timestamp', '<=', $filters['date_to']);
        }

        return $query->get();
    }

    /**
     * Mencegah cloning instance (bagian dari Singleton Pattern).
     */
    private function __clone()
    {
    }
}
