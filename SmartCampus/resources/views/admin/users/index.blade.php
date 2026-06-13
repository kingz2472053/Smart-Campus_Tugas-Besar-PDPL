@extends('layouts.app')
@section('title', 'Manajemen Pengguna')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Kelola Pengguna</h4>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="bi bi-person-plus me-1"></i> Tambah Pengguna
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white pt-3 pb-0 border-bottom-0">
        <ul class="nav nav-tabs border-bottom-0">
            <li class="nav-item">
                <a class="nav-link {{ $currentRole === 'mahasiswa' ? 'active fw-bold text-primary border-bottom-0' : 'text-muted' }}" href="{{ route('admin.users.index', ['role' => 'mahasiswa']) }}">Mahasiswa</a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ $currentRole === 'dosen' ? 'active fw-bold text-primary border-bottom-0' : 'text-muted' }}" href="{{ route('admin.users.index', ['role' => 'dosen']) }}">Dosen</a>
            </li>
        </ul>
    </div>
    <div class="card-body p-0 border-top">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Detail ID</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="align-middle fw-medium">{{ $user->name }}</td>
                        <td class="align-middle text-muted">{{ $user->email }}</td>
                        <td class="align-middle">
                            <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'dosen' ? 'success' : 'primary') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="align-middle text-muted" style="font-size: 0.85rem;">
                            @if($user->role === 'mahasiswa')
                                {{ $user->student->nim ?? '-' }}
                            @elseif($user->role === 'dosen')
                                {{ $user->lecturer->nip ?? '-' }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="align-middle">
                            @if($user->is_active)
                                <span class="badge bg-success bg-opacity-10 text-success"><i class="bi bi-check-circle me-1"></i>Aktif</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger"><i class="bi bi-x-circle me-1"></i>Nonaktif</span>
                            @endif
                        </td>
                        <td class="align-middle text-end">
                            <form action="{{ route('admin.users.toggle-active', $user) }}" method="POST" class="d-inline">
                                @csrf @method('PATCH')
                                @if($user->is_active)
                                    <button class="btn btn-sm btn-outline-warning" title="Nonaktifkan Akun" onclick="return confirm('Nonaktifkan pengguna ini?')"><i class="bi bi-power"></i></button>
                                @else
                                    <button class="btn btn-sm btn-outline-success" title="Aktifkan Akun" onclick="return confirm('Aktifkan pengguna ini?')"><i class="bi bi-check-circle"></i></button>
                                @endif
                            </form>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Hapus Akun" onclick="return confirm('Hapus pengguna ini?')"><i class="bi bi-trash"></i></button>
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
    {{ $users->links() }}
</div>
@endsection
