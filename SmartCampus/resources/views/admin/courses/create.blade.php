@extends('layouts.app')
@section('title', 'Tambah Mata Kuliah')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.courses.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card border-0 shadow-sm" style="max-width: 600px;">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">Tambah Mata Kuliah Baru</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.courses.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium">Kode Mata Kuliah</label>
                <input type="text" name="code" class="form-control" required placeholder="Contoh: IF123">
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Nama Mata Kuliah</label>
                <input type="text" name="name" class="form-control" required placeholder="Contoh: Rekayasa Perangkat Lunak">
            </div>
            <div class="mb-4">
                <label class="form-label fw-medium">Dosen Pengampu</label>
                <select name="lecturer_id" class="form-select" required>
                    <option value="">Pilih Dosen...</option>
                    @foreach(\App\Models\Lecturer::with('user')->get() as $lecturer)
                        <option value="{{ $lecturer->id }}">{{ $lecturer->user->name ?? 'Unknown' }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i> Simpan Mata Kuliah</button>
        </form>
    </div>
</div>
@endsection
