<?php

namespace App\Contracts;

/**
 * UserFactory Interface — Abstract Factory Pattern
 *
 * Mendefinisikan interface untuk membuat keluarga objek User.
 * Setiap factory konkret (StudentFactory, LecturerFactory, AdminFactory)
 * bertanggung jawab membuat User dan profil yang sesuai.
 */
interface UserFactoryInterface
{
    /**
     * Membuat user baru dengan role yang sesuai.
     *
     * @param array $userData Data user (name, email, password)
     * @param array $profileData Data profil tambahan (nim, nip, dll)
     * @return \App\Models\User
     */
    public function createUser(array $userData, array $profileData = []): \App\Models\User;
}
