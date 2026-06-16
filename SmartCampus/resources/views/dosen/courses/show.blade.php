@extends('layouts.app')
@section('title', 'Detail Kelas')

@section('content')
<div class="mb-4">
    <a href="{{ route('dosen.courses.index') }}" class="text-decoration-none">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Kelas
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary mb-2">{{ $course->code }}</span>
                <h4 class="fw-bold mb-1">{{ $course->name }}</h4>
                <p class="text-muted mb-0" style="font-size:0.9rem">
                    Kelas {{ $course->class_name ?? '-' }} &middot; {{ $course->academic_year ?? '-' }} &middot; {{ $course->sks }} SKS
                </p>
            </div>
            <div class="text-end">
                <div class="sc-stat-card text-center" style="min-width:120px">
                    <div class="sc-stat-value text-primary">{{ $enrollments->count() }}</div>
                    <div class="sc-stat-label">Mahasiswa Aktif</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent">
        <h6 class="fw-bold mb-0"><i class="bi bi-people me-2"></i>Daftar Mahasiswa Terdaftar</h6>
    </div>
    <div class="card-body p-0">
        @if($enrollments->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr style="font-size:0.85rem">
                            <th class="ps-3" style="width:50px">No</th>
                            <th>Nama Mahasiswa</th>
                            <th>NIM</th>
                            <th>Program Studi</th>
                            <th>Semester</th>
                            <th>Tanggal Bergabung</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($enrollments as $index => $enrollment)
                            <tr style="font-size:0.85rem">
                                <td class="ps-3">{{ $index + 1 }}</td>
                                <td class="fw-semibold">{{ $enrollment->student->user->name ?? '-' }}</td>
                                <td>{{ $enrollment->student->nim ?? '-' }}</td>
                                <td>{{ $enrollment->student->program_studi ?? '-' }}</td>
                                <td>{{ $enrollment->student->semester ?? '-' }}</td>
                                <td>
                                    <i class="bi bi-clock text-muted me-1"></i>
                                    {{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('d M Y') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-5">
                <div class="text-muted">
                    <i class="bi bi-person-x" style="font-size: 2.5rem;"></i>
                    <p class="mt-2 mb-0">Belum ada mahasiswa yang terdaftar di kelas ini.</p>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
