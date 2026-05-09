<?php

namespace App\Services\Auth;

use App\Contracts\AuthServiceInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * OTPDecorator — Concrete Decorator (Decorator Pattern)
 *
 * Menambahkan layer verifikasi OTP ke proses autentikasi.
 * Membungkus BasicAuth tanpa mengubah implementasinya.
 * Jika OTP aktif pada akun pengguna, generate dan validasi kode OTP.
 * Jika OTP tidak aktif, proses autentikasi berjalan normal.
 */
class OTPDecorator extends AuthDecorator
{
    private const OTP_LENGTH = 6;
    private const OTP_EXPIRY_MINUTES = 5;

    /**
     * Autentikasi dengan layer OTP tambahan.
     *
     * 1. Validasi BasicAuth (email + password) terlebih dahulu
     * 2. Jika berhasil dan OTP aktif: generate kode OTP
     * 3. Jika OTP tidak aktif: autentikasi langsung berhasil
     */
    public function authenticate(array $credentials): bool
    {
        // Langkah 1: Validasi BasicAuth dulu
        $basicResult = $this->wrapped->authenticate($credentials);

        if (!$basicResult) {
            return false;
        }

        // Langkah 2: Cek apakah user punya OTP aktif
        $user = $this->getWrappedUser();

        if ($user && $this->isOtpRequired($user)) {
            // OTP diperlukan, generate dan simpan kode
            $this->generateOTP($user);
            // Return false karena butuh verifikasi OTP terpisah
            return false;
        }

        // OTP tidak diperlukan, autentikasi langsung berhasil
        return true;
    }

    /**
     * Generate kode OTP 6 digit dan simpan ke database.
     */
    public function generateOTP(User $user): string
    {
        $otpCode = str_pad((string) random_int(0, 999999), self::OTP_LENGTH, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code' => $otpCode,
            'otp_expiry' => Carbon::now()->addMinutes(self::OTP_EXPIRY_MINUTES),
        ]);

        return $otpCode;
    }

    /**
     * Verifikasi kode OTP yang dimasukkan pengguna.
     *
     * @return bool True jika kode valid dan belum kedaluwarsa
     */
    public function verifyOTP(User $user, string $inputCode): bool
    {
        // Validasi kode OTP
        if ($user->otp_code !== $inputCode) {
            return false;
        }

        // Validasi masa berlaku OTP
        if (Carbon::now()->isAfter($user->otp_expiry)) {
            return false;
        }

        // OTP valid, bersihkan kode
        $user->update([
            'otp_code' => null,
            'otp_expiry' => null,
        ]);

        return true;
    }

    /**
     * Cek apakah pengguna memerlukan verifikasi OTP.
     */
    private function isOtpRequired(User $user): bool
    {
        // OTP dianggap aktif jika sudah pernah di-setup
        // (bisa dikembangkan dengan kolom otp_enabled)
        return false; // Default: OTP non-aktif, bisa diaktifkan per user
    }

    /**
     * Mendapatkan user dari wrapped BasicAuth.
     */
    private function getWrappedUser(): ?User
    {
        if ($this->wrapped instanceof BasicAuth) {
            return $this->wrapped->getAuthenticatedUser();
        }
        return null;
    }
}
