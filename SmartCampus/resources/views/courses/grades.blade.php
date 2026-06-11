@extends('layouts.app')
@section('title', 'Rekap Nilai: ' . $course->name)

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb" style="font-size: 0.85rem;">
        <li class="breadcrumb-item"><a href="{{ route('mahasiswa.courses.index') }}" class="text-decoration-none">Mata Kuliah Saya</a></li>
        <li class="breadcrumb-item active">{{ $course->name }}</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-end mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-award me-2"></i>Rekap Nilai</h4>
        <p class="text-muted mb-0">{{ $course->name }} ({{ $course->code }})</p>
    </div>
    
    {{-- TOMBOL EXPORT MENGGUNAKAN STRATEGY PATTERN --}}
    <div class="d-flex gap-2">
        <a href="{{ route('mahasiswa.courses.export', ['course' => $course->id, 'format' => 'pdf']) }}" class="btn btn-danger btn-sm">
            <i class="bi bi-file-earmark-pdf me-1"></i> Export PDF
        </a>
        <a href="{{ route('mahasiswa.courses.export', ['course' => $course->id, 'format' => 'csv']) }}" class="btn btn-success btn-sm">
            <i class="bi bi-file-earmark-spreadsheet me-1"></i> Export CSV
        </a>
    </div>
</div>

{{-- Flash Messages --}}
@if(session('error'))
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-1"></i>{{ session('error') }}</div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="font-size: 0.8rem; width: 50px;">No</th>
                        <th style="font-size: 0.8rem;">Judul Tugas</th>
                        <th style="font-size: 0.8rem;">Deadline</th>
                        <th style="font-size: 0.8rem;">Status Kumpul</th>
                        <th style="font-size: 0.8rem; text-align: center;">Skor Maks</th>
                        <th style="font-size: 0.8rem; text-align: center;">Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $index => $assignment)
                        @php
                            $sub = $assignment->submissions->first();
                            $statusText = $sub ? ucfirst($sub->status) : 'Belum Kumpul';
                            $statusColor = match($statusText) {
                                'Graded' => 'success',
                                'Submitted' => 'primary',
                                'Late' => 'danger',
                                default => 'secondary'
                            };
                        @endphp
                        <tr>
                            <td style="font-size: 0.85rem;">{{ $index + 1 }}</td>
                            <td>
                                <a href="{{ route('mahasiswa.assignments.show', $assignment) }}" class="text-decoration-none fw-medium" style="font-size: 0.85rem;">
                                    {{ $assignment->title }}
                                </a>
                            </td>
                            <td style="font-size: 0.85rem;">{{ $assignment->deadline->format('d M Y, H:i') }}</td>
                            <td><span class="badge bg-{{ $statusColor }}" style="font-size: 0.7rem;">{{ $statusText }}</span></td>
                            <td style="font-size: 0.85rem; text-align: center;">{{ $assignment->max_score }}</td>
                            <td style="text-align: center;">
                                @if($sub && $sub->status === 'graded')
                                    <span class="badge bg-info text-dark" style="font-size: 0.8rem;">{{ $sub->latestGrade->result ?? '-' }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Belum ada tugas untuk mata kuliah ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection