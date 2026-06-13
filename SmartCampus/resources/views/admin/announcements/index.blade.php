@extends('layouts.app')
@section('title', 'Manajemen Pengumuman')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Kelola Pengumuman</h4>
    <a href="{{ route('admin.announcements.create') }}" class="btn btn-primary">
        <i class="bi bi-megaphone me-1"></i> Buat Pengumuman
    </a>
</div>

@if($announcements->isEmpty())
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5 text-muted">
            <i class="bi bi-megaphone" style="font-size: 2rem;"></i>
            <p class="mt-2">Belum ada pengumuman. Buat pengumuman pertama!</p>
        </div>
    </div>
@else
    @foreach($announcements as $announcement)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h6 class="fw-bold mb-1">{{ $announcement->title }}</h6>
                    <p class="text-muted mb-2" style="font-size: 0.8rem;">
                        <i class="bi bi-person me-1"></i>{{ $announcement->creator->name ?? '-' }}
                        <span class="mx-1">•</span>
                        <i class="bi bi-clock me-1"></i>{{ $announcement->created_at->format('d M Y, H:i') }}
                        <span class="mx-1">•</span>
                        @if($announcement->is_active)
                            <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                        @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-x-circle me-1"></i>Nonaktif</span>
                        @endif
                    </p>
                </div>
                <div class="d-flex gap-1">
                    <form action="{{ route('admin.announcements.toggle-active', $announcement) }}" method="POST">
                        @csrf @method('PATCH')
                        @if($announcement->is_active)
                            <button class="btn btn-sm btn-outline-warning" title="Nonaktifkan"><i class="bi bi-eye-slash"></i></button>
                        @else
                            <button class="btn btn-sm btn-outline-success" title="Aktifkan"><i class="bi bi-eye"></i></button>
                        @endif
                    </form>
                    <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus pengumuman ini?')"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
            <div class="text-muted" style="font-size: 0.9rem;">
                {{ Str::limit($announcement->content, 200) }}
            </div>
        </div>
    </div>
    @endforeach

    <div class="mt-3">
        {{ $announcements->links() }}
    </div>
@endif
@endsection
