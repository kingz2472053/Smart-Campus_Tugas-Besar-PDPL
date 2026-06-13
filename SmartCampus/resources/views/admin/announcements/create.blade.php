@extends('layouts.app')
@section('title', 'Buat Pengumuman')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card border-0 shadow-sm" style="max-width: 700px;">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">Buat Pengumuman Baru</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.announcements.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium">Judul Pengumuman <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" required placeholder="Contoh: Jadwal UTS Semester Genap 2024" value="{{ old('title') }}">
            </div>
            <div class="mb-4">
                <label class="form-label fw-medium">Isi Pengumuman <span class="text-danger">*</span></label>
                <textarea name="content" class="form-control" rows="6" required placeholder="Tulis isi pengumuman di sini...">{{ old('content') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-megaphone me-1"></i> Publikasi Pengumuman</button>
        </form>
    </div>
</div>
@endsection
