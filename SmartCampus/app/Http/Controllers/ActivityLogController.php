<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogger;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * ActivityLogController — Menampilkan riwayat aktivitas sistem.
 *
 * Menggunakan Singleton Pattern: ActivityLogger::getInstance()
 * dipanggil untuk mengambil data log yang sudah tercatat.
 *
 * Hak akses:
 * - Admin: melihat SEMUA log dari semua user
 * - Dosen/Mahasiswa: melihat log milik sendiri saja
 */
class ActivityLogController extends Controller
{
    /**
     * Menampilkan halaman riwayat aktivitas dengan filter.
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $logger = ActivityLogger::getInstance();

        // Guard: Hanya admin yang boleh mengakses Riwayat Aktivitas
        if ($user->role !== 'admin') {
            abort(403, 'Akses ditolak. Hanya administrator yang dapat melihat riwayat aktivitas.');
        }

        // Bangun filter dari query string
        $filters = [];

        // Admin bisa filter berdasarkan user
        if ($request->filled('user_id')) {
            $filters['user_id'] = $request->user_id;
        }

        if ($request->filled('action')) {
            $filters['action'] = $request->action;
        }

        if ($request->filled('date_from')) {
            $filters['date_from'] = $request->date_from . ' 00:00:00';
        }

        if ($request->filled('date_to')) {
            $filters['date_to'] = $request->date_to . ' 23:59:59';
        }

        // Ambil log melalui Singleton ActivityLogger
        $logs = $logger->getLogs($filters);

        // Untuk dropdown filter user (khusus admin)
        $users = $user->role === 'admin' ? User::orderBy('name')->get() : collect();

        // Daftar aksi unik untuk dropdown filter
        $actions = \App\Models\ActivityLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        return view('activity-logs.index', compact('logs', 'users', 'actions'));
    }

    /**
     * Menampilkan detail satu entri log.
     */
    public function show($id)
    {
        $user = request()->user();
        $log = \App\Models\ActivityLog::with('user')->findOrFail($id);

        // Guard: Hanya admin yang boleh melihat detail log
        if ($user->role !== 'admin') {
            abort(403, 'Anda tidak memiliki akses ke log ini.');
        }

        return view('activity-logs.show', compact('log'));
    }
}
