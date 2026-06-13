@extends('layouts.app')
@section('title', 'Mata Kuliah Saya')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-book me-2"></i>Mata Kuliah Saya</h4>
</div>

<div class="row g-4">
    @forelse($courses as $course)
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm">
                <div class="card-body">
                    <span class="badge bg-primary bg-opacity-10 text-primary mb-2">{{ $course->code }}</span>
                    <h5 class="card-title fw-bold mb-1">{{ $course->name }}</h5>
                    <p class="text-muted small mb-3">
                        <i class="bi bi-person me-1"></i> {{ $course->lecturer->user->name ?? 'Dosen Belum Diatur' }}
                        <br>
                        <i class="bi bi-award me-1"></i> {{ $course->sks }} SKS
                    </p>
                </div>
                <div class="card-footer bg-white border-top-0 pt-0 pb-3">
                    <a href="{{ route('mahasiswa.courses.grades', $course) }}" class="btn btn-outline-primary w-100">
                        <i class="bi bi-clipboard-data me-1"></i> Lihat Nilai
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted">
                <i class="bi bi-journal-x" style="font-size: 2.5rem;"></i>
                <p class="mt-2 mb-0">Anda belum terdaftar di mata kuliah apapun.</p>
            </div>
        </div>
    @endforelse
</div>
@endsection