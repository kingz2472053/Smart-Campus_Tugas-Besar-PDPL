@extends('layouts.app')
@section('title', 'Riwayat Aktivitas')

@section('content')

{{-- Header --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-clock-history me-2"></i>Riwayat Aktivitas</h4>
        <p class="text-muted mb-0" style="font-size: 0.85rem;">
            Catatan seluruh aktivitas sistem — dicatat melalui <strong>Singleton Pattern (ActivityLogger)</strong>
        </p>
    </div>
    <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2" style="font-size: 0.8rem;">
        <i class="bi bi-database me-1"></i> {{ $logs->count() }} entri ditemukan
    </span>
</div>

{{-- Filter Card --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius: 0.75rem;">
    <div class="card-body">
        <form method="GET" action="{{ route(Auth::user()->role === 'admin' ? 'admin.activity-logs.index' : (Auth::user()->role === 'dosen' ? 'dosen.activity-logs.index' : 'mahasiswa.activity-logs.index')) }}">
            <div class="row g-3 align-items-end">

                {{-- Filter: User (Hanya Admin) --}}
                @if(Auth::user()->role === 'admin')
                <div class="col-md-3">
                    <label class="form-label fw-semibold" style="font-size: 0.8rem;">
                        <i class="bi bi-person me-1"></i> Filter Pengguna
                    </label>
                    <select name="user_id" class="form-select form-select-sm">
                        <option value="">Semua Pengguna</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} ({{ ucfirst($u->role) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                @endif

                {{-- Filter: Aksi --}}
                <div class="{{ Auth::user()->role === 'admin' ? 'col-md-3' : 'col-md-4' }}">
                    <label class="form-label fw-semibold" style="font-size: 0.8rem;">
                        <i class="bi bi-lightning me-1"></i> Filter Aksi
                    </label>
                    <select name="action" class="form-select form-select-sm">
                        <option value="">Semua Aksi</option>
                        @foreach($actions as $action)
                            <option value="{{ $action }}" {{ request('action') == $action ? 'selected' : '' }}>
                                {{ $action }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter: Tanggal Mulai --}}
                <div class="{{ Auth::user()->role === 'admin' ? 'col-md-2' : 'col-md-3' }}">
                    <label class="form-label fw-semibold" style="font-size: 0.8rem;">
                        <i class="bi bi-calendar-event me-1"></i> Dari Tanggal
                    </label>
                    <input type="date" name="date_from" class="form-control form-control-sm" value="{{ request('date_from') }}">
                </div>

                {{-- Filter: Tanggal Akhir --}}
                <div class="{{ Auth::user()->role === 'admin' ? 'col-md-2' : 'col-md-3' }}">
                    <label class="form-label fw-semibold" style="font-size: 0.8rem;">
                        <i class="bi bi-calendar-check me-1"></i> Sampai Tanggal
                    </label>
                    <input type="date" name="date_to" class="form-control form-control-sm" value="{{ request('date_to') }}">
                </div>

                {{-- Tombol Filter --}}
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                        <i class="bi bi-funnel me-1"></i> Filter
                    </button>
                    <a href="{{ route(Auth::user()->role === 'admin' ? 'admin.activity-logs.index' : (Auth::user()->role === 'dosen' ? 'dosen.activity-logs.index' : 'mahasiswa.activity-logs.index')) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Activity Log --}}
<div class="card border-0 shadow-sm" style="border-radius: 0.75rem;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr style="background: #F8FAFC;">
                    <th class="ps-3" style="font-size: 0.75rem; color: #64748B; font-weight: 600;">WAKTU</th>
                    @if(Auth::user()->role === 'admin')
                    <th style="font-size: 0.75rem; color: #64748B; font-weight: 600;">PENGGUNA</th>
                    @endif
                    <th style="font-size: 0.75rem; color: #64748B; font-weight: 600;">AKSI</th>
                    <th style="font-size: 0.75rem; color: #64748B; font-weight: 600;">TARGET</th>
                    <th style="font-size: 0.75rem; color: #64748B; font-weight: 600;">IP ADDRESS</th>
                    <th style="font-size: 0.75rem; color: #64748B; font-weight: 600;">DETAIL</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    {{-- Waktu --}}
                    <td class="ps-3">
                        <div style="font-size: 0.85rem; font-weight: 500;">
                            {{ $log->timestamp ? $log->timestamp->format('d M Y') : '-' }}
                        </div>
                        <div style="font-size: 0.75rem; color: #94A3B8;">
                            {{ $log->timestamp ? $log->timestamp->format('H:i:s') : '' }}
                        </div>
                    </td>

                    {{-- Pengguna (Admin only) --}}
                    @if(Auth::user()->role === 'admin')
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="sc-user-avatar" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                {{ $log->user ? strtoupper(substr($log->user->name, 0, 1)) : '?' }}
                            </div>
                            <div>
                                <div style="font-size: 0.85rem; font-weight: 500;">
                                    {{ $log->user->name ?? 'Unknown' }}
                                </div>
                                <div style="font-size: 0.7rem; color: #94A3B8;">
                                    {{ $log->user ? ucfirst($log->user->role) : '-' }}
                                </div>
                            </div>
                        </div>
                    </td>
                    @endif

                    {{-- Badge Aksi --}}
                    <td>
                        @php
                            $actionColors = [
                                'LOGIN' => 'success',
                                'LOGIN_OTP' => 'success',
                                'LOGOUT' => 'secondary',
                                'CREATE_ASSIGNMENT' => 'primary',
                                'UPDATE_ASSIGNMENT' => 'warning',
                                'DELETE_ASSIGNMENT' => 'danger',
                                'SUBMIT_ASSIGNMENT' => 'info',
                            ];
                            $color = $actionColors[$log->action] ?? 'dark';
                        @endphp
                        <span class="badge bg-{{ $color }} bg-opacity-10 text-{{ $color }}" style="font-size: 0.75rem;">
                            {{ $log->action }}
                        </span>
                    </td>

                    {{-- Target --}}
                    <td>
                        @if($log->target_table)
                            <span style="font-size: 0.8rem;">
                                {{ $log->target_table }}
                                @if($log->target_id)
                                    <span class="text-muted">#{{ $log->target_id }}</span>
                                @endif
                            </span>
                        @else
                            <span class="text-muted" style="font-size: 0.8rem;">-</span>
                        @endif
                    </td>

                    {{-- IP Address --}}
                    <td>
                        <code style="font-size: 0.8rem; background: #F1F5F9; padding: 0.15rem 0.4rem; border-radius: 0.25rem;">
                            {{ $log->ip_address ?? '-' }}
                        </code>
                    </td>

                    {{-- Tombol Detail --}}
                    <td>
                        @if($log->detail_json)
                            <button class="btn btn-sm btn-outline-primary" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#detailModal{{ $log->id }}"
                                    style="font-size: 0.75rem;">
                                <i class="bi bi-eye me-1"></i> Lihat
                            </button>
                        @else
                            <span class="text-muted" style="font-size: 0.75rem;">-</span>
                        @endif
                    </td>
                </tr>

                {{-- Modal Detail JSON --}}
                @if($log->detail_json)
                <div class="modal fade" id="detailModal{{ $log->id }}" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content" style="border-radius: 0.75rem;">
                            <div class="modal-header border-0 pb-0">
                                <h6 class="modal-title fw-bold">
                                    <i class="bi bi-info-circle me-1 text-primary"></i>
                                    Detail Aktivitas — {{ $log->action }}
                                </h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <small class="text-muted">Pengguna:</small>
                                    <div class="fw-semibold">{{ $log->user->name ?? 'Unknown' }}</div>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">Waktu:</small>
                                    <div class="fw-semibold">{{ $log->timestamp ? $log->timestamp->format('d M Y H:i:s') : '-' }}</div>
                                </div>
                                <div>
                                    <small class="text-muted">Data Detail (JSON):</small>
                                    <pre style="background: #1E293B; color: #E2E8F0; padding: 1rem; border-radius: 0.5rem; font-size: 0.8rem; max-height: 300px; overflow-y: auto;">{{ json_encode($log->detail_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                @empty
                <tr>
                    <td colspan="{{ Auth::user()->role === 'admin' ? 6 : 5 }}" class="text-center py-5">
                        <div style="color: #94A3B8;">
                            <i class="bi bi-inbox" style="font-size: 2.5rem;"></i>
                            <p class="mt-2 mb-0">Belum ada aktivitas yang tercatat.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
