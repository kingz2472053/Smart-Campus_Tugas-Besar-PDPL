<?php

namespace App\Services\User;

use App\Contracts\UserFactoryInterface;

/**
 * UserFactoryManager — Factory Resolver (Abstract Factory Pattern)
 *
 * Menentukan factory konkret yang tepat berdasarkan role pengguna.
 * Menerapkan Open/Closed Principle: penambahan role baru hanya perlu
 * menambah factory baru dan mendaftarkannya di sini.
 */
class UserFactoryManager
{
    /**
     * Mendapatkan factory yang sesuai berdasarkan role.
     *
     * @param string $role 'mahasiswa', 'dosen', atau 'admin'
     * @return UserFactoryInterface
     * @throws \InvalidArgumentException Jika role tidak dikenali
     */
    public static function getFactory(string $role): UserFactoryInterface
    {
        return match ($role) {
            'mahasiswa' => new StudentFactory(),
            'dosen' => new LecturerFactory(),
            'admin' => new AdminFactory(),
            default => throw new \InvalidArgumentException("Role tidak dikenali: {$role}"),
        };
    }
}
