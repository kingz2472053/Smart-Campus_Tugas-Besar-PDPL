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
                <label class="form-label fw-medium">Tahun Ajaran <span class="text-danger">*</span></label>
                <input type="text" name="academic_year" class="form-control" required placeholder="Contoh: 2023/2024 Genap" value="2023/2024 Genap">
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-medium">Kode Mata Kuliah <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" required placeholder="Contoh: IF123">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Nama Mata Kuliah <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" required placeholder="Contoh: Rekayasa Perangkat Lunak">
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-medium">SKS</label>
                    <input type="number" name="sks" class="form-control" value="3" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-medium">Semester Target</label>
                    <select name="semester" class="form-select" required>
                        @for($i = 1; $i <= 8; $i++)
                            <option value="{{ $i }}" {{ $i == 1 ? 'selected' : '' }}>Semester {{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-primary">Daftar Kelas</h6>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-class-btn"><i class="bi bi-plus-circle me-1"></i>Tambah Kelas</button>
            </div>

            <div id="classes-container">
                <div class="class-row border p-3 mb-3 bg-light rounded position-relative">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label" style="font-size:0.85rem">Nama Kelas <span class="text-danger">*</span></label>
                            <input type="text" name="classes[0][class_name]" class="form-control form-control-sm" required placeholder="Misal: A">
                        </div>
                        <div class="col-md-7">
                            <label class="form-label" style="font-size:0.85rem">Dosen Pengampu <span class="text-danger">*</span></label>
                            <select name="classes[0][lecturer_id]" class="form-select form-select-sm" required>
                                <option value="">Pilih Dosen...</option>
                                @foreach(\App\Models\Lecturer::with('user')->get() as $lecturer)
                                    <option value="{{ $lecturer->id }}">{{ $lecturer->user->name ?? 'Unknown' }} (NIP: {{ $lecturer->nip }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label" style="font-size:0.85rem">Kuota</label>
                            <input type="number" name="classes[0][kuota]" class="form-control form-control-sm" value="40" required>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 mt-2"><i class="bi bi-save me-1"></i> Simpan Mata Kuliah & Kelas</button>
        </form>
    </div>
</div>

<!-- Template for new class rows -->
<template id="class-row-template">
    <div class="class-row border p-3 mb-3 bg-light rounded position-relative">
        <button type="button" class="btn btn-sm btn-danger remove-class-btn position-absolute top-0 end-0 m-2" style="padding: 0.1rem 0.3rem;"><i class="bi bi-x"></i></button>
        <div class="row">
            <div class="col-md-3">
                <label class="form-label" style="font-size:0.85rem">Nama Kelas <span class="text-danger">*</span></label>
                <input type="text" name="classes[__INDEX__][class_name]" class="form-control form-control-sm" required placeholder="Misal: B">
            </div>
            <div class="col-md-7">
                <label class="form-label" style="font-size:0.85rem">Dosen Pengampu <span class="text-danger">*</span></label>
                <select name="classes[__INDEX__][lecturer_id]" class="form-select form-select-sm" required>
                    <option value="">Pilih Dosen...</option>
                    @foreach(\App\Models\Lecturer::with('user')->get() as $lecturer)
                        <option value="{{ $lecturer->id }}">{{ $lecturer->user->name ?? 'Unknown' }} (NIP: {{ $lecturer->nip }})</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label" style="font-size:0.85rem">Kuota</label>
                <input type="number" name="classes[__INDEX__][kuota]" class="form-control form-control-sm" value="40" required>
            </div>
        </div>
    </div>
</template>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        let classIndex = 1;
        const container = document.getElementById('classes-container');
        const addBtn = document.getElementById('add-class-btn');
        const template = document.getElementById('class-row-template').innerHTML;

        addBtn.addEventListener('click', function() {
            const newRowHtml = template.replace(/__INDEX__/g, classIndex);
            container.insertAdjacentHTML('beforeend', newRowHtml);
            classIndex++;
        });

        container.addEventListener('click', function(e) {
            if (e.target.closest('.remove-class-btn')) {
                const row = e.target.closest('.class-row');
                // Ensure at least one class remains
                if (container.querySelectorAll('.class-row').length > 1) {
                    row.remove();
                } else {
                    alert('Minimal harus ada 1 kelas.');
                }
            }
        });
    });
</script>
@endpush
