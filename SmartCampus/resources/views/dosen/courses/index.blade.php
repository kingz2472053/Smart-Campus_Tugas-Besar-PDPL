@extends('layouts.app')
@section('title', 'Monitor Kelas')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-people me-2"></i>Monitor Kelas</h4>
        <p class="text-muted mb-0" style="font-size:0.9rem">Daftar mata kuliah yang Anda ampu beserta jumlah mahasiswa terdaftar.</p>
    </div>
</div>

<div class="row g-4">
    @forelse($courses as $course)
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-primary bg-opacity-10 text-primary">{{ $course->code }}</span>
                        <span class="badge bg-success bg-opacity-10 text-success">
                            <i class="bi bi-people-fill me-1"></i>{{ $course->active_students_count }} Mahasiswa
                        </span>
                    </div>
                    <h5 class="card-title fw-bold mb-1">{{ $course->name }}</h5>
                    <p class="text-muted small mb-0">
                        <i class="bi bi-building me-1"></i> Kelas {{ $course->class_name ?? '-' }}
                        <br>
                        <i class="bi bi-calendar me-1"></i> {{ $course->academic_year ?? '-' }} &middot; {{ $course->sks }} SKS
                    </p>
                </div>
                <div class="card-footer bg-transparent border-top-0 pt-0 pb-3">
                    <a href="{{ route('dosen.courses.show', $course) }}" class="btn btn-outline-primary w-100">
                        <i class="bi bi-eye me-1"></i> Lihat Detail Mahasiswa
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted">
                <i class="bi bi-journal-x" style="font-size: 2.5rem;"></i>
                <p class="mt-2 mb-0">Anda belum memiliki kelas yang diampu.</p>
            </div>
        </div>
    @endforelse
</div>
@endsection
