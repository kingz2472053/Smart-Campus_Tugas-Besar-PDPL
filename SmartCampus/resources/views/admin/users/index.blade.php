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
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Detail ID</th>
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
                                {{ $user->lecturer->nidn ?? '-' }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="align-middle text-end">
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus pengguna ini?')"><i class="bi bi-trash"></i></button>
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
