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
@endsection
