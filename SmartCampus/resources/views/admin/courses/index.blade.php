@extends('layouts.app')
@section('title', 'Manajemen Mata Kuliah')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Kelola Mata Kuliah</h4>
    <a href="{{ route('admin.courses.create') }}" class="btn btn-primary">
        <i class="bi bi-journal-plus me-1"></i> Tambah Mata Kuliah
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Nama Mata Kuliah</th>
                        <th>Kelas</th>
                        <th>Tahun Ajaran</th>
                        <th>Dosen Pengampu</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($courses as $course)
                    <tr>
                        <td class="align-middle fw-medium">{{ $course->code }}</td>
                        <td class="align-middle">{{ $course->name }}</td>
                        <td class="align-middle"><span class="badge bg-primary">{{ $course->class_name }}</span></td>
                        <td class="align-middle text-muted">{{ $course->academic_year }}</td>
                        <td class="align-middle text-muted">{{ $course->lecturer->user->name ?? '-' }}</td>
                        <td class="align-middle text-end">
                            <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus mata kuliah ini?')"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="mt-3">
    {{ $courses->links() }}
</div>
@endsection
