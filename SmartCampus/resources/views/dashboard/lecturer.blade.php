@extends('layouts.app')
@section('title', 'Dashboard Dosen')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="sc-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="sc-stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-book"></i>
                </div>
                <div>
                    <div class="sc-stat-value">{{ $courseCount }}</div>
                    <div class="sc-stat-label">Mata Kuliah Diampu</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="sc-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="sc-stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-clipboard-check"></i>
                </div>
                <div>
                    <div class="sc-stat-value">{{ $assignmentCount }}</div>
                    <div class="sc-stat-label">Total Tugas Dibuat</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-semibold"><i class="bi bi-person-circle me-2"></i>Profil Dosen</h6>
    </div>
    <div class="card-body">
        @if($lecturer)
        <table class="table table-borderless mb-0">
            <tr><td class="text-muted" style="width:150px">NIP</td><td class="fw-medium">{{ $lecturer->nip }}</td></tr>
            <tr><td class="text-muted">Nama</td><td class="fw-medium">{{ $user->name }}</td></tr>
            <tr><td class="text-muted">Departemen</td><td class="fw-medium">{{ $lecturer->department }}</td></tr>
            <tr><td class="text-muted">Jabatan</td><td class="fw-medium">{{ $lecturer->jabatan ?? '-' }}</td></tr>
        </table>
        @else
        <p class="text-muted mb-0">Profil dosen belum dilengkapi.</p>
        @endif
    </div>
</div>
@endsection
