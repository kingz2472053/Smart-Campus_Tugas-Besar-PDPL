@extends('layouts.app')
@section('title', 'Edit Pengumuman')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.announcements.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card border-0 shadow-sm" style="max-width: 700px;">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">Edit Pengumuman</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST">
            @csrf @method('PUT')
            <div class="mb-3">
                <label class="form-label fw-medium">Judul Pengumuman <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control" required value="{{ old('title', $announcement->title) }}">
            </div>
            <div class="mb-4">
                <label class="form-label fw-medium">Isi Pengumuman <span class="text-danger">*</span></label>
                <textarea name="content" class="form-control" rows="6" required>{{ old('content', $announcement->content) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection
