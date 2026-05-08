<?php

namespace App\Services\Auth;

use App\Contracts\AuthServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

/**
 * BasicAuth — Concrete Component (Decorator Pattern)
 *
 * Implementasi autentikasi dasar menggunakan email dan password.
 * Ini adalah komponen yang dibungkus oleh AuthDecorator.
 * Memvalidasi kredensial terhadap database tanpa layer tambahan.
 */
class BasicAuth implements AuthServiceInterface
{
    private ?User $authenticatedUser = null;

    /**
     * Melakukan autentikasi dasar: validasi email + password.
     *
     * @param array $credentials ['email' => string, 'password' => string]
     * @return bool
     */
    public function authenticate(array $credentials): bool
    {
        $user = User::where('email', $credentials['email'])->first();

        if (!$user) {
            return false;
        }

        // Cek apakah akun aktif (mendukung fitur nonaktifasi oleh Admin)
        if (!$user->is_active) {
            return false;
        }

        // Validasi password menggunakan Hash facade
        if (!Hash::check($credentials['password'], $user->password)) {
            return false;
        }

        $this->authenticatedUser = $user;
        return true;
    }

    /**
     * Mendapatkan user yang berhasil diautentikasi.
     */
    public function getAuthenticatedUser(): ?User
    {
        return $this->authenticatedUser;
    }
}
