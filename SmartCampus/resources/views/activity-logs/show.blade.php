@extends('layouts.app')
@section('title', 'Detail Aktivitas')

@section('content')

<div class="mb-4">
    <a href="{{ route('admin.activity-logs.index') }}" class="text-decoration-none" style="font-size: 0.85rem;">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Riwayat Aktivitas
    </a>
</div>

<div class="card border-0 shadow-sm" style="border-radius: 0.75rem;">
    <div class="card-header bg-transparent border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0">
            <i class="bi bi-info-circle me-2 text-primary"></i>Detail Log Aktivitas #{{ $log->id }}
        </h5>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="row g-4">
            {{-- Informasi Utama --}}
            <div class="col-md-6">
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Pengguna</small>
                    <div class="d-flex align-items-center gap-2">
                        <div class="sc-user-avatar" style="width: 36px; height: 36px; font-size: 0.8rem;">
                            {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : '?' }}
                        </div>
                        <div>
                            <div class="fw-semibold">{{ $log->user->name ?? 'Unknown' }}</div>
                            <small class="text-muted">{{ $log->user->email ?? '-' }} · {{ ucfirst($log->user->role ?? '-') }}</small>
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Aksi</small>
                    @php
                        $actionColors = [
                            'LOGIN' => 'success', 'LOGIN_OTP' => 'success', 'LOGOUT' => 'secondary',
                            'CREATE_ASSIGNMENT' => 'primary', 'UPDATE_ASSIGNMENT' => 'warning',
                            'DELETE_ASSIGNMENT' => 'danger', 'SUBMIT_ASSIGNMENT' => 'info',
                        ];
                        $color = $actionColors[$log->action] ?? 'dark';
                    @endphp
                    <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }}" style="font-size: 0.85rem;">
                        {{ $log->action }}
                    </span>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Waktu</small>
                    <div class="fw-semibold">
                        {{ $log->timestamp ? $log->timestamp->format('d M Y H:i:s') : '-' }}
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Target</small>
                    <div class="fw-semibold">
                        {{ $log->target_table ?? '-' }}
                        @if($log->target_id)
                            <span class="text-muted">#{{ $log->target_id }}</span>
                        @endif
                    </div>
                </div>

                <div class="mb-3">
                    <small class="text-muted d-block mb-1">IP Address</small>
                    <code style="font-size: 0.85rem; background: #F1F5F9; padding: 0.2rem 0.5rem; border-radius: 0.25rem;">
                        {{ $log->ip_address ?? '-' }}
                    </code>
                </div>
            </div>

            {{-- Detail JSON --}}
            @if($log->detail_json)
            <div class="col-12">
                <small class="text-muted d-block mb-2">Data Detail (JSON)</small>
                <pre style="background: #1E293B; color: #E2E8F0; padding: 1.25rem; border-radius: 0.75rem; font-size: 0.8rem; max-height: 400px; overflow-y: auto;">{{ json_encode($log->detail_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
