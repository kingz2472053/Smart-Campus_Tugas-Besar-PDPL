@extends('layouts.app')
@section('title', 'Tambah Pengguna')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h6 class="mb-0 fw-bold">Tambah Pengguna Baru (Batch)</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.users.store') }}" method="POST" autocomplete="off" id="batchUserForm">
            @csrf
            
            <div class="mb-4" style="max-width: 400px;">
                <label class="form-label fw-medium">Pilih Role Pengguna yang Ingin Ditambahkan <span class="text-danger">*</span></label>
                <select name="role" id="role-select" class="form-select" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="mahasiswa">Mahasiswa</option>
                    <option value="dosen">Dosen</option>
                </select>
            </div>

            <hr>

            <div id="dynamic-container" class="d-none">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-primary" id="table-title">Daftar Pengguna</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="add-row-btn"><i class="bi bi-plus-circle me-1"></i>Tambah Baris</button>
                </div>
                
                <div class="alert alert-info py-2" style="font-size: 0.85rem;">
                    <i class="bi bi-info-circle me-1"></i> Password akan di-generate otomatis oleh sistem dan ditampilkan setelah Anda klik Simpan.
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light" style="font-size: 0.85rem;">
                            <tr>
                                <th>Nama Lengkap <span class="text-danger">*</span></th>
                                <th>Email <span class="text-danger">*</span></th>
                                <th id="th-identifier">NIM/NIP</th>
                                <th id="th-attribute">Angkatan/Departemen <span class="text-danger">*</span></th>
                                <th style="width: 50px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="rows-container">
                            <!-- Rows will be injected here via JS -->
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Simpan Pengguna</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Template for Row -->
<template id="row-template">
    <tr>
        <td>
            <input type="text" name="users[__INDEX__][name]" class="form-control form-control-sm" required placeholder="Nama Lengkap">
        </td>
        <td>
            <input type="email" name="users[__INDEX__][email]" class="form-control form-control-sm" required placeholder="Email">
        </td>
        <td>
            <input type="text" name="users[__INDEX__][identifier]" class="form-control form-control-sm input-identifier" placeholder="Kosongkan u/ Otomatis">
        </td>
        <td>
            <input type="text" name="users[__INDEX__][attribute]" class="form-control form-control-sm input-attribute" required placeholder="...">
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger remove-row-btn" title="Hapus Baris"><i class="bi bi-trash"></i></button>
        </td>
    </tr>
</template>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let rowIndex = 0;
        const roleSelect = document.getElementById('role-select');
        const container = document.getElementById('dynamic-container');
        const rowsContainer = document.getElementById('rows-container');
        const addBtn = document.getElementById('add-row-btn');
        const template = document.getElementById('row-template').innerHTML;

        const thIdentifier = document.getElementById('th-identifier');
        const thAttribute = document.getElementById('th-attribute');
        const tableTitle = document.getElementById('table-title');

        function addRow() {
            const currentRole = roleSelect.value;
            const newRowHtml = template.replace(/__INDEX__/g, rowIndex);
            rowsContainer.insertAdjacentHTML('beforeend', newRowHtml);
            
            const newlyAddedRow = rowsContainer.lastElementChild;
            const inputIdentifier = newlyAddedRow.querySelector('.input-identifier');
            const inputAttribute = newlyAddedRow.querySelector('.input-attribute');

            if (currentRole === 'mahasiswa') {
                inputIdentifier.placeholder = "Kosongkan u/ Otomatis";
                inputAttribute.placeholder = "Misal: 2023";
            } else {
                inputIdentifier.placeholder = "Otomatis Jika Kosong";
                inputAttribute.placeholder = "Misal: Teknik Informatika";
            }

            rowIndex++;
        }

        roleSelect.addEventListener('change', function() {
            if (this.value) {
                container.classList.remove('d-none');
                
                if (this.value === 'mahasiswa') {
                    tableTitle.innerHTML = '<i class="bi bi-people me-1"></i> Daftar Mahasiswa Baru';
                    thIdentifier.innerHTML = 'NIM';
                    thAttribute.innerHTML = 'Angkatan <span class="text-danger">*</span>';
                } else {
                    tableTitle.innerHTML = '<i class="bi bi-person-workspace me-1"></i> Daftar Dosen Baru';
                    thIdentifier.innerHTML = 'NIP';
                    thAttribute.innerHTML = 'Departemen <span class="text-danger">*</span>';
                }

                // Kosongkan dan buat 1 baris awal
                rowsContainer.innerHTML = '';
                rowIndex = 0;
                addRow();
            } else {
                container.classList.add('d-none');
            }
        });

        addBtn.addEventListener('click', function() {
            addRow();
        });

        rowsContainer.addEventListener('click', function(e) {
            const removeBtn = e.target.closest('.remove-row-btn');
            if (removeBtn) {
                const row = removeBtn.closest('tr');
                if (rowsContainer.querySelectorAll('tr').length > 1) {
                    row.remove();
                } else {
                    alert('Minimal harus ada 1 baris.');
                }
            }
        });
    });
</script>
@endpush
