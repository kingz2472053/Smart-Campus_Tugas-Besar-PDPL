<?php

namespace App\Http\Controllers;

use App\Services\Auth\BasicAuth;
use App\Services\Auth\OTPDecorator;
use App\Services\ActivityLogger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * AuthController — Menangani Login, Logout, dan verifikasi OTP.
 *
 * Menggunakan Decorator Pattern: BasicAuth dibungkus OTPDecorator.
 * Menggunakan Singleton: ActivityLogger mencatat semua aktivitas auth.
 */
class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    /**
     * Proses login menggunakan Decorator Pattern.
     *
     * Alur: BasicAuth → OTPDecorator (jika aktif) → redirect dashboard
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');

        // Decorator Pattern: BasicAuth dibungkus OTPDecorator
        $basicAuth = new BasicAuth();
        $authService = new OTPDecorator($basicAuth);

        $result = $authService->authenticate($credentials);

        if ($result) {
            // Autentikasi berhasil (tanpa OTP atau OTP non-aktif)
            $user = $basicAuth->getAuthenticatedUser();
            Auth::login($user);

            // Singleton Pattern: catat aktivitas login
            ActivityLogger::getInstance()->log(
                'LOGIN',
                $user->id,
                'users',
                $user->id,
                ['role' => $user->role, 'ip' => $request->ip()]
            );

            return redirect()->route('dashboard');
        }

        // Cek apakah gagal karena OTP diperlukan
        $user = $basicAuth->getAuthenticatedUser();
        if ($user && $user->otp_code) {
            // Simpan user_id di session untuk verifikasi OTP
            $request->session()->put('otp_user_id', $user->id);
            return redirect()->route('otp.show');
        }

        return back()->withErrors([
            'email' => 'Email atau password tidak valid, atau akun tidak aktif.',
        ])->withInput($request->only('email'));
    }

    /**
     * Tampilkan halaman OTP.
     */
    public function showOtp(Request $request)
    {
        if (!$request->session()->has('otp_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.otp');
    }

    /**
     * Verifikasi kode OTP.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp_code' => 'required|string|size:6',
        ]);

        $userId = $request->session()->get('otp_user_id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login')->with('error', 'Sesi OTP tidak valid.');
        }

        $basicAuth = new BasicAuth();
        $otpDecorator = new OTPDecorator($basicAuth);

        if ($otpDecorator->verifyOTP($user, $request->otp_code)) {
            Auth::login($user);
            $request->session()->forget('otp_user_id');

            ActivityLogger::getInstance()->log(
                'LOGIN_OTP',
                $user->id,
                'users',
                $user->id,
                ['method' => 'otp_verified']
            );

            return redirect()->route('dashboard');
        }

        return back()->withErrors([
            'otp_code' => 'Kode OTP tidak valid atau sudah kedaluwarsa.',
        ]);
    }

    /**
     * Proses logout.
     */
    public function logout(Request $request)
    {
        $userId = Auth::id();

        ActivityLogger::getInstance()->log(
            'LOGOUT',
            $userId,
            'users',
            $userId
        );

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Berhasil logout.');
    }
}
