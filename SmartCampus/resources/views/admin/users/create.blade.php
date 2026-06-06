@extends('layouts.app')
@section('title', 'Tambah Pengguna')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card border-0 shadow-sm" style="max-width: 600px;">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">Tambah Pengguna Baru</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label fw-medium">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-medium">Password</label>
                <input type="password" name="password" class="form-control" required minlength="8">
            </div>
            <div class="mb-4">
                <label class="form-label fw-medium">Role</label>
                <select name="role" class="form-select" required>
                    <option value="">Pilih Role...</option>
                    <option value="admin">Admin</option>
                    <option value="dosen">Dosen</option>
                    <option value="mahasiswa">Mahasiswa</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i> Simpan Pengguna</button>
        </form>
    </div>
</div>
@endsection
