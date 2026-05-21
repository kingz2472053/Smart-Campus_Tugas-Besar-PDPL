@extends('layouts.app')
@section('title', 'Detail Tugas')

@section('content')
@php
    $role = Auth::user()->role;
    $isOwner = ($role === 'dosen' && $assignment->created_by === Auth::id());

    // Deadline status
    $now = now();
    $deadline = $assignment->deadline;
    if ($deadline < $now) {
        $deadlineBadge = 'bg-danger';
        $deadlineText = 'Terlambat';
    } elseif ($deadline <= $now->copy()->addDays(3)) {
        $deadlineBadge = 'bg-warning text-dark';
        $deadlineText = 'Mendekati Deadline';
    } else {
        $deadlineBadge = 'bg-success';
        $deadlineText = 'Aktif';
    }

    // Route index berdasarkan role
    $indexRoute = match($role) {
        'dosen' => route('dosen.assignments.index'),
        'mahasiswa' => route('mahasiswa.assignments.index'),
        'admin' => route('admin.assignments.index'),
        default => route('dashboard'),
    };
    $indexLabel = match($role) {
        'dosen' => 'Kelola Tugas',
        'admin' => 'Semua Tugas',
        default => 'Daftar Tugas',
    };
@endphp

{{-- Breadcrumb --}}
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb" style="font-size: 0.85rem;">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ $indexRoute }}" class="text-decoration-none">{{ $indexLabel }}</a></li>
        <li class="breadcrumb-item active">{{ Str::limit($assignment->title, 40) }}</li>
    </ol>
</nav>

{{-- Header + Aksi --}}
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold mb-1">{{ $assignment->title }}</h4>
        <div class="d-flex gap-2 align-items-center">
            <span class="badge bg-primary bg-opacity-10 text-primary">{{ $assignment->course->name ?? '-' }}</span>
            <span class="badge {{ $deadlineBadge }}">{{ $deadlineText }}</span>
        </div>
    </div>
    @if($isOwner)
        <div class="d-flex gap-2">
            <a href="{{ route('dosen.assignments.edit', $assignment) }}" class="btn btn-outline-warning btn-sm">
                <i class="bi bi-pencil me-1"></i> Edit
            </a>
            <button type="button" class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="bi bi-trash me-1"></i> Hapus
            </button>
        </div>
    @endif
</div>

<div class="row g-4">
    {{-- Detail Tugas --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-file-text me-2"></i>Deskripsi Tugas</h6>
            </div>
            <div class="card-body">
                @if($assignment->description)
                    <div style="white-space: pre-wrap; font-size: 0.9rem; line-height: 1.7;">{{ $assignment->description }}</div>
                @else
                    <p class="text-muted mb-0 fst-italic">Tidak ada deskripsi.</p>
                @endif
            </div>
        </div>

        {{-- Submission Table (Dosen & Admin) --}}
        @if(in_array($role, ['dosen', 'admin']))
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-people me-2"></i>Daftar Submission</h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary">{{ $assignment->submissions->count() }} submission</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="font-size:0.8rem">No</th>
                                    <th style="font-size:0.8rem">Mahasiswa</th>
                                    <th style="font-size:0.8rem">NIM</th>
                                    <th style="font-size:0.8rem">File</th>
                                    <th style="font-size:0.8rem">Waktu Submit</th>
                                    <th style="font-size:0.8rem;text-align:center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assignment->submissions as $index => $sub)
                                    <tr>
                                        <td style="font-size:0.85rem">{{ $index + 1 }}</td>
                                        <td style="font-size:0.85rem">{{ $sub->student->user->name ?? '-' }}</td>
                                        <td style="font-size:0.85rem">{{ $sub->student->nim ?? '-' }}</td>
                                        <td style="font-size:0.85rem">
                                            @if($sub->file_name)
                                                <i class="bi bi-file-earmark me-1"></i>{{ $sub->file_name }}
                                                <small class="text-muted">({{ $sub->file_size_kb }} KB)</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td style="font-size:0.85rem">{{ $sub->submitted_at ? $sub->submitted_at->format('d M Y H:i') : '-' }}</td>
                                        <td style="text-align:center">
                                            @php
                                                $statusBadge = match($sub->status) {
                                                    'submitted' => 'bg-success',
                                                    'late' => 'bg-danger',
                                                    default => 'bg-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $statusBadge }}" style="font-size:0.7rem">{{ ucfirst($sub->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Belum ada mahasiswa yang mengumpulkan tugas.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        {{-- Submission Form (Mahasiswa) --}}
        @if($role === 'mahasiswa')
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-semibold"><i class="bi bi-upload me-2"></i>Kumpulkan Tugas</h6>
                </div>
                <div class="card-body">
                    @if($mySubmission && $mySubmission->file_name)
                        {{-- Sudah pernah submit --}}
                        <div class="alert alert-info d-flex align-items-center mb-3">
                            <i class="bi bi-check-circle me-2"></i>
                            <div>
                                <strong>Sudah dikumpulkan:</strong> {{ $mySubmission->file_name }}
                                <small class="text-muted">({{ $mySubmission->file_size_kb }} KB)</small>
                                <br>
                                <small>Waktu: {{ $mySubmission->submitted_at->format('d M Y H:i') }}
                                    — Status:
                                    <span class="badge {{ $mySubmission->status === 'late' ? 'bg-danger' : 'bg-success' }}">
                                        {{ ucfirst($mySubmission->status) }}
                                    </span>
                                </small>
                            </div>
                        </div>
                        <form action="{{ route('mahasiswa.submissions.update', $mySubmission) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="file" class="form-label fw-medium">Upload Ulang File</label>
                                <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" required>
                                @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">Format: {{ $assignment->file_format_allowed }} | Maks: {{ number_format($assignment->max_file_size_kb / 1024, 1) }} MB</small>
                            </div>
                            <button type="submit" class="btn btn-warning"><i class="bi bi-arrow-repeat me-1"></i> Upload Ulang</button>
                        </form>
                    @else
                        {{-- Belum submit --}}
                        <form action="{{ route('mahasiswa.submissions.store', $assignment) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="file" class="form-label fw-medium">Upload File Tugas</label>
                                <input type="file" name="file" id="file" class="form-control @error('file') is-invalid @enderror" required>
                                @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <small class="text-muted">Format: {{ $assignment->file_format_allowed }} | Maks: {{ number_format($assignment->max_file_size_kb / 1024, 1) }} MB</small>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i> Kumpulkan</button>
                        </form>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Sidebar Info --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-info-circle me-2"></i>Informasi</h6>
            </div>
            <div class="card-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr><td class="text-muted" style="width:120px;font-size:0.85rem">Mata Kuliah</td><td class="fw-medium" style="font-size:0.85rem">{{ $assignment->course->name ?? '-' }}</td></tr>
                    <tr><td class="text-muted" style="font-size:0.85rem">Kode MK</td><td class="fw-medium" style="font-size:0.85rem">{{ $assignment->course->code ?? '-' }}</td></tr>
                    <tr><td class="text-muted" style="font-size:0.85rem">Dosen</td><td class="fw-medium" style="font-size:0.85rem">{{ $assignment->creator->name ?? '-' }}</td></tr>
                    <tr><td class="text-muted" style="font-size:0.85rem">Skor Maks</td><td class="fw-medium" style="font-size:0.85rem">{{ $assignment->max_score }}</td></tr>
                    <tr><td class="text-muted" style="font-size:0.85rem">Format File</td><td class="fw-medium" style="font-size:0.85rem">{{ $assignment->file_format_allowed }}</td></tr>
                    <tr><td class="text-muted" style="font-size:0.85rem">Ukuran Maks</td><td class="fw-medium" style="font-size:0.85rem">{{ number_format($assignment->max_file_size_kb / 1024, 1) }} MB</td></tr>
                    <tr><td class="text-muted" style="font-size:0.85rem">Dibuat</td><td class="fw-medium" style="font-size:0.85rem">{{ $assignment->created_at->format('d M Y') }}</td></tr>
                </table>
            </div>
        </div>

        {{-- Deadline Countdown --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-alarm me-2"></i>Deadline</h6>
            </div>
            <div class="card-body text-center">
                <div class="mb-2">
                    <span class="badge {{ $deadlineBadge }} px-3 py-2" style="font-size: 0.9rem;">{{ $deadlineText }}</span>
                </div>
                <h5 class="fw-bold mb-1">{{ $assignment->deadline->format('d M Y') }}</h5>
                <p class="text-muted mb-2" style="font-size:0.85rem">{{ $assignment->deadline->format('H:i') }} WIB</p>
                @if($assignment->deadline > now())
                    <p class="mb-0" style="font-size:0.85rem">
                        <i class="bi bi-hourglass-split me-1"></i>
                        {{ now()->diffForHumans($assignment->deadline, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }} lagi
                    </p>
                @else
                    <p class="text-danger mb-0" style="font-size:0.85rem">
                        <i class="bi bi-exclamation-triangle me-1"></i>
                        Sudah lewat {{ $assignment->deadline->diffForHumans(now(), ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Delete Modal (Dosen Only) --}}
@if($isOwner)
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle text-danger me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus tugas:</p>
                <div class="bg-light rounded p-3">
                    <strong>{{ $assignment->title }}</strong><br>
                    <small class="text-muted">{{ $assignment->course->name ?? '' }}</small>
                </div>
                <p class="text-danger mt-3 mb-0" style="font-size: 0.85rem;">
                    <i class="bi bi-info-circle me-1"></i>Semua submission terkait juga akan dihapus.
                </p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('dosen.assignments.destroy', $assignment) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="bi bi-trash me-1"></i> Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif
@endsection
