@extends('layouts.auth')

@section('title', 'Login')

@section('content')
    <div class="auth-brand">
        <div class="auth-brand-icon">
            <i class="bi bi-mortarboard-fill"></i>
        </div>
        <h2>SmartCampus</h2>
        <p>Sistem Manajemen Tugas & Proyek Mahasiswa</p>
    </div>

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="alert alert-danger py-2 px-3" style="font-size:0.85rem; border-radius:0.5rem;">
            <i class="bi bi-exclamation-triangle me-1"></i>
            {{ $errors->first() }}
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success py-2 px-3" style="font-size:0.85rem; border-radius:0.5rem;">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login.process') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                <input type="email" class="form-control" id="email" name="email"
                       value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
            </div>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                <input type="password" class="form-control" id="password" name="password"
                       placeholder="Masukkan password" required>
            </div>
        </div>

        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-box-arrow-in-right me-1"></i> Masuk
        </button>
    </form>

    <div class="text-center mt-3" style="font-size:0.8rem; color:#94A3B8;">
        Mata Kuliah Pola Desain Perangkat Lunak
    </div>
@endsection
