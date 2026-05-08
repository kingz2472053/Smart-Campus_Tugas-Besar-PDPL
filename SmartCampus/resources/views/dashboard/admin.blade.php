@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="sc-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="sc-stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <div class="sc-stat-value">{{ $userCount }}</div>
                    <div class="sc-stat-label">Total Pengguna</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="sc-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="sc-stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-book"></i>
                </div>
                <div>
                    <div class="sc-stat-value">{{ $courseCount }}</div>
                    <div class="sc-stat-label">Total Mata Kuliah</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Log (Singleton Pattern) -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-clock-history me-2"></i>Aktivitas Terbaru</h6>
        <a href="#" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="font-size:0.8rem">Waktu</th>
                        <th style="font-size:0.8rem">Pengguna</th>
                        <th style="font-size:0.8rem">Aksi</th>
                        <th style="font-size:0.8rem">Target</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLogs as $log)
                    <tr>
                        <td style="font-size:0.8rem">{{ $log->timestamp->format('d/m/Y H:i') }}</td>
                        <td style="font-size:0.8rem">{{ $log->user->name ?? 'System' }}</td>
                        <td><span class="badge bg-primary bg-opacity-10 text-primary" style="font-size:0.7rem">{{ $log->action }}</span></td>
                        <td style="font-size:0.8rem">{{ $log->target_table ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-3">Belum ada aktivitas tercatat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
