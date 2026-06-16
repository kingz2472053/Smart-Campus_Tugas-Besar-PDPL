@extends('layouts.app')
@section('title', 'Kelola Enrollment')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-person-check me-2"></i>Kelola Enrollment</h4>
        <p class="text-muted mb-0" style="font-size:0.9rem">Kelola pendaftaran mahasiswa ke mata kuliah.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#manualEnrollModal">
        <i class="bi bi-plus-circle me-1"></i> Daftarkan Manual
    </button>
</div>

{{-- Tab Navigation --}}
<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'pending' ? 'active' : '' }}" href="?tab=pending">
            <i class="bi bi-hourglass-split me-1"></i> Menunggu Persetujuan
            @if($pendingCount > 0)
                <span class="badge bg-warning text-dark ms-1">{{ $pendingCount }}</span>
            @endif
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'active' ? 'active' : '' }}" href="?tab=active">
            <i class="bi bi-check-circle me-1"></i> Aktif
            <span class="badge bg-success ms-1">{{ $activeCount }}</span>
        </a>
    </li>
</ul>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr style="font-size:0.85rem">
                        <th class="ps-3" style="width:50px">No</th>
                        <th>Mahasiswa</th>
                        <th>NIM</th>
                        <th>Mata Kuliah</th>
                        <th>Kelas</th>
                        <th>Dosen Pengampu</th>
                        <th>Tanggal Daftar</th>
                        @if($tab === 'active')
                            <th>Disetujui Oleh</th>
                        @endif
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enrollments as $index => $enrollment)
                        <tr style="font-size:0.85rem">
                            <td class="ps-3">{{ $enrollments->firstItem() + $index }}</td>
                            <td class="fw-semibold">{{ $enrollment->student->user->name ?? '-' }}</td>
                            <td>{{ $enrollment->student->nim ?? '-' }}</td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary">{{ $enrollment->course->code ?? '' }}</span>
                                {{ $enrollment->course->name ?? '-' }}
                            </td>
                            <td>{{ $enrollment->course->class_name ?? '-' }}</td>
                            <td>{{ $enrollment->course->lecturer->user->name ?? '-' }}</td>
                            <td>
                                <i class="bi bi-clock text-muted me-1"></i>
                                {{ $enrollment->enrolled_at ? $enrollment->enrolled_at->format('d M Y') : '-' }}
                            </td>
                            @if($tab === 'active')
                                <td>{{ $enrollment->verifier->name ?? 'Sistem' }}</td>
                            @endif
                            <td class="text-center">
                                @if($tab === 'pending')
                                    <form action="{{ route('admin.enrollments.approve', $enrollment) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-success" title="Setujui">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.enrollments.reject', $enrollment) }}" method="POST" class="d-inline ms-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Tolak" onclick="return confirm('Yakin ingin menolak pendaftaran ini?')">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.enrollments.destroy', $enrollment) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus enrollment ini?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                <p class="mt-2 mb-0">
                                    @if($tab === 'pending')
                                        Tidak ada pendaftaran yang menunggu persetujuan.
                                    @else
                                        Belum ada data enrollment aktif.
                                    @endif
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if($enrollments->hasPages())
    <div class="d-flex justify-content-center mt-3">
        {{ $enrollments->links() }}
    </div>
@endif

{{-- Modal Manual Enroll --}}
<div class="modal fade" id="manualEnrollModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.enrollments.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="bi bi-person-plus me-2"></i>Daftarkan Mahasiswa Manual</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mahasiswa</label>
                        <select name="student_id" class="form-select" required>
                            <option value="">-- Pilih Mahasiswa --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->user->name }} ({{ $student->nim }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mata Kuliah / Kelas</label>
                        <select name="course_id" class="form-select" required>
                            <option value="">-- Pilih Mata Kuliah --</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->code }} - {{ $course->name }} (Kelas {{ $course->class_name ?? '-' }}) — {{ $course->lecturer->user->name ?? 'Dosen ?' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle me-1"></i> Daftarkan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
