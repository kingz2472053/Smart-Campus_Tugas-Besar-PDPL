@extends('layouts.app')
@section('title', 'Rekap Nilai')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Rekap Nilai</h4>
    <p class="text-muted">Transkrip seluruh tugas berdasarkan mata kuliah.</p>
</div>

@forelse($groupedSubmissions as $courseName => $submissions)
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold"><i class="bi bi-book me-2 text-primary"></i>{{ $courseName }}</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="font-size: 0.85rem">Tugas</th>
                        <th style="font-size: 0.85rem">Status</th>
                        <th style="font-size: 0.85rem">Nilai</th>
                        <th style="font-size: 0.85rem">Waktu Kumpul</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($submissions as $sub)
                    <tr>
                        <td class="align-middle fw-medium" style="font-size: 0.9rem">
                            <a href="{{ route('mahasiswa.assignments.show', $sub->assignment_id) }}" class="text-decoration-none">
                                {{ $sub->assignment->title }}
                            </a>
                        </td>
                        <td class="align-middle">
                            @php
                                $badge = match($sub->status) {
                                    'submitted' => 'bg-success',
                                    'late' => 'bg-danger',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ ucfirst($sub->status) }}</span>
                        </td>
                        <td class="align-middle fw-bold text-primary">
                            {{ $sub->latestGrade->result ?? 'Belum Dinilai' }}
                        </td>
                        <td class="align-middle text-muted" style="font-size: 0.85rem">
                            {{ $sub->submitted_at ? $sub->submitted_at->format('d M Y, H:i') : '-' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@empty
<div class="alert alert-info">
    <i class="bi bi-info-circle me-2"></i> Anda belum mengumpulkan tugas apapun.
</div>
@endforelse
@endsection
