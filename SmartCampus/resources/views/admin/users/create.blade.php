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
        <form action="{{ route('admin.users.store') }}" method="POST" autocomplete="off">
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
                <select name="role" id="role-select" class="form-select" required>
                    <option value="">Pilih Role...</option>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                </select>
            </div>

            <!-- Fields for Mahasiswa -->
            <div id="mahasiswa-fields" class="d-none bg-light p-3 rounded mb-3 border">
                <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-person-badge me-2"></i>Data Mahasiswa</h6>
                <div class="mb-3">
                    <label class="form-label" style="font-size:0.85rem">NIM</label>
                    <input type="text" name="nim" class="form-control form-control-sm" placeholder="Otomatis jika kosong">
                </div>
                <div class="mb-2">
                    <label class="form-label" style="font-size:0.85rem">Angkatan <span class="text-danger">*</span></label>
                    <input type="text" name="angkatan" id="angkatan" class="form-control form-control-sm" placeholder="Misal: 2023">
                </div>
            </div>

            <!-- Fields for Dosen -->
            <div id="dosen-fields" class="d-none bg-light p-3 rounded mb-3 border">
                <h6 class="fw-bold mb-3 text-success"><i class="bi bi-person-workspace me-2"></i>Data Dosen</h6>
                <div class="mb-3">
                    <label class="form-label" style="font-size:0.85rem">NIP</label>
                    <input type="text" name="nip" class="form-control form-control-sm" placeholder="Otomatis jika kosong">
                </div>
                <div class="mb-2">
                    <label class="form-label" style="font-size:0.85rem">Departemen <span class="text-danger">*</span></label>
                    <input type="text" name="department" id="department" class="form-control form-control-sm" placeholder="Misal: Teknik Informatika">
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100"><i class="bi bi-save me-1"></i> Simpan Pengguna</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role-select');
        const mhsFields = document.getElementById('mahasiswa-fields');
        const dosenFields = document.getElementById('dosen-fields');
        
        // Required fields
        const angkatan = document.getElementById('angkatan');
        const dept = document.getElementById('department');

        roleSelect.addEventListener('change', function() {
            // Hide all first
            mhsFields.classList.add('d-none');
            dosenFields.classList.add('d-none');
            
            angkatan.removeAttribute('required');
            dept.removeAttribute('required');

            if (this.value === 'mahasiswa') {
                mhsFields.classList.remove('d-none');
                angkatan.setAttribute('required', 'required');
            } else if (this.value === 'dosen') {
                dosenFields.classList.remove('d-none');
                dept.setAttribute('required', 'required');
            }
        });
    });
</script>
@endpush
