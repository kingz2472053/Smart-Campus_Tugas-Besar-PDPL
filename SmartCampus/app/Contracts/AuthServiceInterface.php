<?php

namespace App\Contracts;

/**
 * AuthService Interface — Decorator Pattern
 *
 * Interface utama untuk autentikasi. Diimplementasi oleh BasicAuth (concrete component)
 * dan AuthDecorator (abstract decorator). Memungkinkan penambahan layer autentikasi
 * (seperti OTP) secara transparan tanpa mengubah implementasi dasar.
 */
interface AuthServiceInterface
{
    /**
     * Melakukan proses autentikasi.
     *
     * @param array $credentials Kredensial pengguna (email, password, dll)
     * @return bool True jika autentikasi berhasil
     */
    public function authenticate(array $credentials): bool;
}
