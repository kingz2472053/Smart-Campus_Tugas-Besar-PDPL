@extends('layouts.auth')

@section('title', 'Verifikasi OTP')

@section('content')
    <div class="auth-brand">
        <div class="auth-brand-icon">
            <i class="bi bi-shield-lock"></i>
        </div>
        <h2>Verifikasi OTP</h2>
        <p>Masukkan kode 6 digit yang dikirim ke email Anda</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger py-2 px-3" style="font-size:0.85rem;">
            <i class="bi bi-exclamation-triangle me-1"></i>{{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('otp.verify') }}">
        @csrf
        <div class="mb-4">
            <label for="otp_code" class="form-label">Kode OTP</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-key"></i></span>
                <input type="text" class="form-control text-center" id="otp_code" name="otp_code"
                       maxlength="6" placeholder="000000" required autofocus
                       style="font-size:1.5rem; letter-spacing:0.5rem; font-weight:700;">
            </div>
            <div class="form-text text-center mt-2">Kode berlaku selama 5 menit</div>
        </div>
        <button type="submit" class="btn btn-primary w-100">
            <i class="bi bi-check-circle me-1"></i> Verifikasi
        </button>
    </form>

    <div class="text-center mt-3">
        <a href="{{ route('login') }}" style="font-size:0.85rem; color:#4F46E5;">
            <i class="bi bi-arrow-left"></i> Kembali ke Login
        </a>
    </div>
@endsection
