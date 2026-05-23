<?php

namespace App\Services\Auth;

use App\Contracts\AuthServiceInterface;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

/**
 * OTPDecorator — Concrete Decorator (Decorator Pattern)
 *
 * Menambahkan layer verifikasi OTP ke proses autentikasi.
 * Membungkus BasicAuth tanpa mengubah implementasinya.
 * Jika OTP aktif pada akun pengguna, generate dan validasi kode OTP.
 * Jika OTP tidak aktif, proses autentikasi berjalan normal.
 *
 * Alur kerja:
 * 1. BasicAuth memvalidasi email + password
 * 2. Jika berhasil dan user memiliki otp_enabled = true:
 *    - Generate kode OTP 6 digit
 *    - Simpan ke database (user.otp_code, user.otp_expiry)
 *    - Kirim notifikasi ke user (via Laravel Notification)
 *    - Return false → user harus verifikasi OTP di halaman terpisah
 * 3. Jika OTP tidak aktif → login langsung berhasil
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
            $otpCode = $this->generateOTP($user);

            // Kirim notifikasi OTP ke user
            $this->sendOtpNotification($user, $otpCode);

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
     * Kirim notifikasi OTP ke pengguna.
     *
     * Saat ini menggunakan log channel sebagai simulasi pengiriman email.
     * Di production, bisa diganti dengan:
     * - Mail::to($user)->send(new OtpMail($otpCode));
     * - $user->notify(new OtpNotification($otpCode));
     *
     * Ini menunjukkan bahwa Decorator Pattern tidak mengubah
     * implementasi BasicAuth sama sekali — hanya menambahkan layer baru.
     */
    private function sendOtpNotification(User $user, string $otpCode): void
    {
        // Tetap simpan di session untuk fallback/dev mode agar web tidak error kalau email gagal
        session(['otp_display_code' => $otpCode]);

        // Kirim email sungguhan
        try {
            Mail::to($user->email)->send(new OtpMail($user, $otpCode));
            Log::info("📧 [OTP Notification] Email OTP berhasil dikirim ke {$user->email}");
        } catch (\Exception $e) {
            Log::error("❌ [OTP Notification] Gagal mengirim email OTP ke {$user->email}: " . $e->getMessage());
        }
    }

    /**
     * Cek apakah pengguna memerlukan verifikasi OTP.
     *
     * Mengecek kolom otp_enabled pada tabel users.
     * Admin bisa mengaktifkan OTP per user melalui panel admin.
     */
    private function isOtpRequired(User $user): bool
    {
        return (bool) $user->otp_enabled;
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
