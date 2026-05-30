@extends('layouts.app')
@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="sc-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="sc-stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-book"></i>
                </div>
                <div>
                    <div class="sc-stat-value">{{ $enrollmentCount }}</div>
                    <div class="sc-stat-label">Mata Kuliah Aktif</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="sc-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="sc-stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-journal-check"></i>
                </div>
                <div>
                    <div class="sc-stat-value">{{ $submissionCount }}</div>
                    <div class="sc-stat-label">Total Submission</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="sc-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="sc-stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="sc-stat-value">{{ $pendingCount }}</div>
                    <div class="sc-stat-label">Belum Dikerjakan</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-person-circle me-2"></i>Profil Mahasiswa</h6>
    </div>
    <div class="card-body">
        @if($student)
        <table class="table table-borderless mb-0">
            <tr><td class="text-muted" style="width:150px">NIM</td><td class="fw-medium">{{ $student->nim }}</td></tr>
            <tr><td class="text-muted">Nama</td><td class="fw-medium">{{ $user->name }}</td></tr>
            <tr><td class="text-muted">Program Studi</td><td class="fw-medium">{{ $student->program_studi }}</td></tr>
            <tr><td class="text-muted">Semester</td><td class="fw-medium">{{ $student->semester }}</td></tr>
            <tr><td class="text-muted">Angkatan</td><td class="fw-medium">{{ $student->angkatan }}</td></tr>
        </table>
        @else
        <p class="text-muted mb-0">Profil mahasiswa belum dilengkapi.</p>
        @endif
    </div>
</div>
{{-- NOTIFICATION FOR DEADLINE --}}
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-bell me-2"></i>Notifikasi Terbaru</h6>
    </div>
    <div class="card-body p-0">
        @if(isset($notifications) && $notifications->count() > 0)
            <div class="list-group list-group-flush">
                @foreach($notifications as $notif)
                    <div class="list-group-item d-flex justify-content-between align-items-start py-3 {{ !$notif->is_read ? 'bg-light' : '' }}">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold {{ !$notif->is_read ? 'text-primary' : 'text-dark' }}">
                                {{ $notif->message }}
                            </div>
                            <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}</small>
                        </div>
                        @if(!$notif->is_read)
                            <span class="badge bg-danger rounded-pill">Baru</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-4 text-center text-muted">
                <i class="bi bi-bell-slash fs-3 d-block mb-2"></i>
                Tidak ada notifikasi saat ini.
            </div>
        @endif
    </div>
</div>
{{-- NOTIFICATION FOR DEADLINE --}}
@endsection
