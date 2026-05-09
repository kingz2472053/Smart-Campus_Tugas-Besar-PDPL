<?php

namespace App\Services\User;

use App\Contracts\UserFactoryInterface;
use App\Models\User;

/**
 * AdminFactory — Concrete Factory (Abstract Factory Pattern)
 *
 * Bertanggung jawab membuat objek User dengan role 'admin'.
 * Admin tidak memiliki profil tambahan (Student/Lecturer).
 */
class AdminFactory implements UserFactoryInterface
{
    /**
     * Membuat user admin.
     *
     * @param array $userData ['name', 'email', 'password']
     * @param array $profileData Tidak digunakan untuk Admin
     */
    public function createUser(array $userData, array $profileData = []): User
    {
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => $userData['password'],
            'role' => 'admin',
            'is_active' => true,
        ]);

        $user->uiPreference()->create([
            'theme' => 'light',
            'notification_channel' => 'dashboard',
        ]);

        return $user;
    }
}
