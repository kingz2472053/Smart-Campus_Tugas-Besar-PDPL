@extends('layouts.app')
@section('title', 'Daftar Kelas')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1"><i class="bi bi-search me-2"></i>Daftar Kelas</h4>
    <p class="text-muted mb-0" style="font-size:0.9rem">Cari dan daftar ke kelas mata kuliah yang tersedia.</p>
</div>

{{-- Kelas yang sudah didaftarkan --}}
@if($enrolledCourses->count() > 0)
<div class="mb-4">
    <h5 class="fw-semibold mb-3"><i class="bi bi-bookmark-check me-2"></i>Kelas Saya</h5>
    <div class="row g-3">
        @foreach($enrolledCourses as $course)
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-primary bg-opacity-10 text-primary">{{ $course->code }}</span>
                            @if($course->enrollment_status === 'active')
                                <span class="badge bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-check-circle me-1"></i>Aktif
                                </span>
                            @elseif($course->enrollment_status === 'pending')
                                <span class="badge bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-hourglass-split me-1"></i>Menunggu
                                </span>
                            @endif
                        </div>
                        <h6 class="card-title fw-bold mb-1">{{ $course->name }}</h6>
                        <p class="text-muted small mb-0">
                            <i class="bi bi-person me-1"></i> {{ $course->lecturer->user->name ?? '-' }}
                            <br>
                            <i class="bi bi-building me-1"></i> Kelas {{ $course->class_name ?? '-' }} &middot; {{ $course->sks }} SKS
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
<hr class="my-4">
@endif

{{-- Kelas yang tersedia --}}
<h5 class="fw-semibold mb-3"><i class="bi bi-journal-plus me-2"></i>Kelas Tersedia</h5>
<div class="row g-3">
    @forelse($availableCourses as $course)
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary">{{ $course->code }}</span>
                        <span class="badge bg-secondary bg-opacity-10 text-secondary">
                            <i class="bi bi-people me-1"></i>{{ $course->active_students_count }}{{ $course->kuota ? '/'.$course->kuota : '' }}
                        </span>
                    </div>
                    <h6 class="card-title fw-bold mb-1">{{ $course->name }}</h6>
                    <p class="text-muted small mb-3">
                        <i class="bi bi-person me-1"></i> {{ $course->lecturer->user->name ?? '-' }}
                        <br>
                        <i class="bi bi-building me-1"></i> Kelas {{ $course->class_name ?? '-' }} &middot; {{ $course->sks }} SKS
                    </p>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-3">
                    <form action="{{ route('mahasiswa.enrollments.store', $course) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100" onclick="return confirm('Yakin ingin mendaftar ke kelas ini?')">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Daftar Kelas
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted">
                <i class="bi bi-check-circle" style="font-size: 2.5rem;"></i>
                <p class="mt-2 mb-0">Anda sudah terdaftar di semua kelas yang tersedia.</p>
            </div>
        </div>
    @endforelse
</div>
@endsection
