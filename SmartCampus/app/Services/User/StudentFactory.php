<?php

namespace App\Services\User;

use App\Contracts\UserFactoryInterface;
use App\Models\User;

/**
 * StudentFactory — Concrete Factory (Abstract Factory Pattern)
 *
 * Bertanggung jawab membuat objek User dengan role 'mahasiswa'
 * beserta profil Student yang terkait.
 */
class StudentFactory implements UserFactoryInterface
{
    /**
     * Membuat user mahasiswa beserta profil Student.
     *
     * @param array $userData ['name', 'email', 'password']
     * @param array $profileData ['nim', 'program_studi', 'semester', 'angkatan']
     */
    public function createUser(array $userData, array $profileData = []): User
    {
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => $userData['password'],
            'role' => 'mahasiswa',
            'is_active' => true,
        ]);

        // Buat profil Student terkait
        if (!empty($profileData)) {
            $user->student()->create($profileData);
        }

        // Buat UI Preference default
        $user->uiPreference()->create([
            'theme' => 'light',
            'notification_channel' => 'dashboard',
        ]);

        return $user;
    }
}
