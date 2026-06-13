@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
@include('partials.announcements')
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="sc-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="sc-stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <div class="sc-stat-value">{{ $activeStudentCount }}</div>
                    <div class="sc-stat-label">Total Mahasiswa Aktif</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sc-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="sc-stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-person-workspace"></i>
                </div>
                <div>
                    <div class="sc-stat-value">{{ $lecturerCount }}</div>
                    <div class="sc-stat-label">Total Dosen</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sc-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="sc-stat-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-book"></i>
                </div>
                <div>
                    <div class="sc-stat-value">{{ $uniqueCourseCount }}</div>
                    <div class="sc-stat-label">Total Matkul Berjalan</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="sc-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="sc-stat-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-journal-bookmark"></i>
                </div>
                <div>
                    <div class="sc-stat-value">{{ $classCount }}</div>
                    <div class="sc-stat-label">Total Kelas</div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
