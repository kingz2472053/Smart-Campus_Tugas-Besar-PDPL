@extends('layouts.app')
@section('title', 'Edit Tugas')

@section('content')
<nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb" style="font-size: 0.85rem;">
        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('dosen.assignments.index') }}" class="text-decoration-none">Kelola Tugas</a></li>
        <li class="breadcrumb-item active">Edit Tugas</li>
    </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1"><i class="bi bi-pencil-square me-2"></i>Edit Tugas</h4>
        <p class="text-muted mb-0" style="font-size: 0.875rem;">Perbarui informasi tugas di bawah ini.</p>
    </div>
    <div>
        <span class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size: 0.8rem;">
            <i class="bi bi-clock me-1"></i>Dibuat: {{ $assignment->created_at->format('d M Y, H:i') }}
        </span>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="{{ route('dosen.assignments.update', $assignment) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-4">
                <div class="col-md-6">
                    <label for="course_id" class="form-label fw-medium">Mata Kuliah <span class="text-danger">*</span></label>
                    <select name="course_id" id="course_id" class="form-select @error('course_id') is-invalid @enderror" required>
                        <option value="">— Pilih Mata Kuliah —</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $assignment->course_id) == $course->id ? 'selected' : '' }}>
                                {{ $course->code }} — {{ $course->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('course_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="title" class="form-label fw-medium">Judul Tugas <span class="text-danger">*</span></label>
                    <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror"
                           value="{{ old('title', $assignment->title) }}" required maxlength="255">
                    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label for="description" class="form-label fw-medium">Deskripsi</label>
                    <textarea name="description" id="description" rows="5" class="form-control @error('description') is-invalid @enderror"
                              maxlength="5000">{{ old('description', $assignment->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="deadline" class="form-label fw-medium">Deadline <span class="text-danger">*</span></label>
                    <input type="datetime-local" name="deadline" id="deadline" class="form-control @error('deadline') is-invalid @enderror"
                           value="{{ old('deadline', $assignment->deadline->format('Y-m-d\TH:i')) }}" required>
                    @error('deadline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="max_score" class="form-label fw-medium">Skor Maksimal <span class="text-danger">*</span></label>
                    <input type="number" name="max_score" id="max_score" class="form-control @error('max_score') is-invalid @enderror"
                           value="{{ old('max_score', $assignment->max_score) }}" min="1" max="100" required>
                    @error('max_score')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="file_format_allowed" class="form-label fw-medium">Format File <span class="text-danger">*</span></label>
                    <input type="text" name="file_format_allowed" id="file_format_allowed" class="form-control @error('file_format_allowed') is-invalid @enderror"
                           value="{{ old('file_format_allowed', $assignment->file_format_allowed) }}" required maxlength="100">
                    @error('file_format_allowed')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <small class="text-muted">Pisahkan dengan koma. Contoh: pdf,doc,docx,zip</small>
                </div>

                <div class="col-md-6">
                    <label for="max_file_size_kb" class="form-label fw-medium">Ukuran Maks (KB) <span class="text-danger">*</span></label>
                    <input type="number" name="max_file_size_kb" id="max_file_size_kb" class="form-control @error('max_file_size_kb') is-invalid @enderror"
                           value="{{ old('max_file_size_kb', $assignment->max_file_size_kb) }}" min="100" max="51200" required>
                    @error('max_file_size_kb')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <hr class="my-4">
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('dosen.assignments.show', $assignment) }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg me-1"></i> Batal</a>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> Perbarui Tugas</button>
            </div>
        </form>
    </div>
</div>
@endsection
