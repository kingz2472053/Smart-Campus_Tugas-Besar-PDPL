@extends('layouts.app')
@section('title', 'Daftar Tugas')

@section('content')
{{-- Header Section --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">
            <i class="bi bi-journal-text me-2"></i>
            @if(Auth::user()->role === 'dosen')
                Kelola Tugas
            @elseif(Auth::user()->role === 'admin')
                Semua Tugas
            @else
                Daftar Tugas
            @endif
        </h4>
        <p class="text-muted mb-0" style="font-size: 0.875rem;">
            @if(Auth::user()->role === 'dosen')
                Kelola tugas untuk mata kuliah yang Anda ampu.
            @elseif(Auth::user()->role === 'admin')
                Monitoring semua tugas dari seluruh dosen.
            @else
                Lihat tugas dari mata kuliah yang Anda ambil.
            @endif
        </p>
    </div>

    {{-- Tombol Buat Tugas (Dosen Only) --}}
    @if(Auth::user()->role === 'dosen')
        <a href="{{ route('dosen.assignments.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i> Buat Tugas Baru
        </a>
    @endif
</div>

{{-- Search & Filter Section --}}
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body py-3">
        @php
            $routeName = match(Auth::user()->role) {
                'dosen' => 'dosen.assignments.index',
                'mahasiswa' => 'mahasiswa.assignments.index',
                'admin' => 'admin.assignments.index',
                default => 'dashboard',
            };
        @endphp
        <form action="{{ route($routeName) }}" method="GET" class="row g-2 align-items-end">
            {{-- Search --}}
            <div class="col-md-4">
                <label class="form-label fw-medium" style="font-size: 0.8rem;">Cari Tugas</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="Cari judul tugas..."
                           value="{{ request('search') }}">
                </div>
            </div>

            {{-- Filter: Mata Kuliah --}}
            <div class="col-md-3">
                <label class="form-label fw-medium" style="font-size: 0.8rem;">Mata Kuliah</label>
                <select name="course_id" class="form-select">
                    <option value="">Semua Mata Kuliah</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter: Status Deadline --}}
            <div class="col-md-3">
                <label class="form-label fw-medium" style="font-size: 0.8rem;">Status Deadline</label>
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>🟢 Aktif</option>
                    <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>🟡 Mendekati</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>🔴 Terlambat</option>
                </select>
            </div>

            {{-- Tombol --}}
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-fill">
                    <i class="bi bi-funnel"></i> Filter
                </button>
                <a href="{{ route($routeName) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-x-lg"></i>
                </a>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Tugas --}}
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="font-size: 0.8rem; width: 50px;">No</th>
                        <th style="font-size: 0.8rem;">Judul Tugas</th>
                        <th style="font-size: 0.8rem;">Mata Kuliah</th>
                        <th style="font-size: 0.8rem;">Deadline</th>
                        <th style="font-size: 0.8rem; text-align: center;">Status</th>
                        <th style="font-size: 0.8rem; text-align: center;">Skor</th>
                        @if(Auth::user()->role === 'admin')
                            <th style="font-size: 0.8rem;">Dosen</th>
                        @endif
                        <th style="font-size: 0.8rem; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($assignments as $index => $assignment)
                        @php
                            // Hitung status deadline
                            $now = now();
                            $deadline = $assignment->deadline;
                            if ($deadline < $now) {
                                $badgeClass = 'bg-danger';
                                $badgeText = 'Terlambat';
                                $badgeIcon = '🔴';
                            } elseif ($deadline <= $now->copy()->addDays(3)) {
                                $badgeClass = 'bg-warning text-dark';
                                $badgeText = 'Mendekati';
                                $badgeIcon = '🟡';
                            } else {
                                $badgeClass = 'bg-success';
                                $badgeText = 'Aktif';
                                $badgeIcon = '🟢';
                            }

                            // Route show berdasarkan role
                            $showRoute = match(Auth::user()->role) {
                                'dosen' => route('dosen.assignments.show', $assignment),
                                'mahasiswa' => route('mahasiswa.assignments.show', $assignment),
                                'admin' => route('admin.assignments.show', $assignment),
                                default => '#',
                            };
                        @endphp
                        <tr>
                            <td style="font-size: 0.85rem;">{{ $assignments->firstItem() + $index }}</td>
                            <td>
                                <a href="{{ $showRoute }}" class="text-decoration-none fw-medium" style="font-size: 0.85rem;">
                                    {{ $assignment->title }}
                                </a>
                            </td>
                            <td style="font-size: 0.85rem;">
                                <span class="badge bg-primary bg-opacity-10 text-primary" style="font-size: 0.75rem;">
                                    {{ $assignment->course->name ?? '-' }}
                                </span>
                            </td>
                            <td style="font-size: 0.85rem;">
                                {{ $deadline->format('d M Y, H:i') }}
                            </td>
                            <td style="text-align: center;">
                                <span class="badge {{ $badgeClass }}" style="font-size: 0.7rem;">
                                    {{ $badgeIcon }} {{ $badgeText }}
                                </span>
                            </td>
                            <td style="font-size: 0.85rem; text-align: center;">{{ $assignment->max_score }}</td>

                            @if(Auth::user()->role === 'admin')
                                <td style="font-size: 0.85rem;">{{ $assignment->creator->name ?? '-' }}</td>
                            @endif

                            <td style="text-align: center;">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ $showRoute }}" class="btn btn-sm btn-outline-primary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if(Auth::user()->role === 'dosen' && $assignment->created_by === Auth::id())
                                        <a href="{{ route('dosen.assignments.edit', $assignment) }}" class="btn btn-sm btn-outline-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="Hapus"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal{{ $assignment->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- Delete Confirmation Modal (Dosen Only) --}}
                        @if(Auth::user()->role === 'dosen' && $assignment->created_by === Auth::id())
                            <div class="modal fade" id="deleteModal{{ $assignment->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header border-0">
                                            <h5 class="modal-title fw-bold">
                                                <i class="bi bi-exclamation-triangle text-danger me-2"></i>Konfirmasi Hapus
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>Apakah Anda yakin ingin menghapus tugas berikut?</p>
                                            <div class="bg-light rounded p-3">
                                                <strong>{{ $assignment->title }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $assignment->course->name ?? '' }}</small>
                                            </div>
                                            <p class="text-danger mt-3 mb-0" style="font-size: 0.85rem;">
                                                <i class="bi bi-info-circle me-1"></i>
                                                Semua submission mahasiswa terkait juga akan dihapus.
                                            </p>
                                        </div>
                                        <div class="modal-footer border-0">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <form action="{{ route('dosen.assignments.destroy', $assignment) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="bi bi-trash me-1"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->role === 'admin' ? 8 : 7 }}" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-journal-x" style="font-size: 2.5rem;"></i>
                                    <p class="mt-2 mb-0">Belum ada tugas.</p>
                                    @if(Auth::user()->role === 'dosen')
                                        <a href="{{ route('dosen.assignments.create') }}" class="btn btn-sm btn-primary mt-2">
                                            <i class="bi bi-plus-lg me-1"></i> Buat Tugas Pertama
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Pagination --}}
    @if($assignments->hasPages())
        <div class="card-footer bg-white border-top-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted">
                    Menampilkan {{ $assignments->firstItem() }}-{{ $assignments->lastItem() }}
                    dari {{ $assignments->total() }} tugas
                </small>
                {{ $assignments->withQueryString()->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
