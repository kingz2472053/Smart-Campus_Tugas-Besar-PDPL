<?php

namespace App\Services\User;

use App\Contracts\UserFactoryInterface;
use App\Models\User;

/**
 * LecturerFactory — Concrete Factory (Abstract Factory Pattern)
 *
 * Bertanggung jawab membuat objek User dengan role 'dosen'
 * beserta profil Lecturer yang terkait.
 */
class LecturerFactory implements UserFactoryInterface
{
    /**
     * Membuat user dosen beserta profil Lecturer.
     *
     * @param array $userData ['name', 'email', 'password']
     * @param array $profileData ['nip', 'department', 'jabatan']
     */
    public function createUser(array $userData, array $profileData = []): User
    {
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'password' => $userData['password'],
            'role' => 'dosen',
            'is_active' => true,
        ]);

        if (!empty($profileData)) {
            $user->lecturer()->create($profileData);
        }

        $user->uiPreference()->create([
            'theme' => 'light',
            'notification_channel' => 'dashboard',
        ]);

        return $user;
    }
}
