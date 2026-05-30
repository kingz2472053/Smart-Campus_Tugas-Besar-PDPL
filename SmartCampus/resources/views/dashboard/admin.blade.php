@extends('layouts.app')
@section('title', 'Dashboard Admin')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="sc-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="sc-stat-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <div class="sc-stat-value">{{ $userCount }}</div>
                    <div class="sc-stat-label">Total Pengguna</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="sc-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="sc-stat-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-book"></i>
                </div>
                <div>
                    <div class="sc-stat-value">{{ $courseCount }}</div>
                    <div class="sc-stat-label">Total Mata Kuliah</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pengumuman (Announcements) -->
<div class="row">
    <div class="col-md-5">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-megaphone me-2"></i>Buat Pengumuman Baru</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.announcements.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Judul</label>
                        <input type="text" name="title" class="form-control" required placeholder="Judul Pengumuman">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Isi Pengumuman</label>
                        <textarea name="content" class="form-control" rows="4" required placeholder="Tuliskan isi pengumuman..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Publikasikan</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-semibold"><i class="bi bi-card-list me-2"></i>Pengumuman Terkini</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    @forelse($announcements as $announcement)
                    <div class="list-group-item p-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1 fw-bold">{{ $announcement->title }}</h6>
                                <small class="text-muted d-block mb-2">{{ $announcement->created_at->format('d/m/Y H:i') }} • Oleh: {{ $announcement->user->name }}</small>
                                <p class="mb-0" style="font-size: 0.9rem;">{{ $announcement->content }}</p>
                            </div>
                            <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" onsubmit="return confirm('Hapus pengumuman ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-4">Belum ada pengumuman.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
