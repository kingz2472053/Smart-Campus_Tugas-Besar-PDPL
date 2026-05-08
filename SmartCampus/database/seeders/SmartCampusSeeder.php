<?php

namespace Database\Seeders;

use App\Services\User\UserFactoryManager;
use Illuminate\Database\Seeder;

/**
 * SmartCampusSeeder — Data dummy untuk testing.
 *
 * Menggunakan Abstract Factory Pattern (UserFactoryManager)
 * untuk membuat user sesuai role masing-masing.
 */
class SmartCampusSeeder extends Seeder
{
    public function run(): void
    {
        // ── Admin (1 akun) ──
        $adminFactory = UserFactoryManager::getFactory('admin');
        $adminFactory->createUser([
            'name' => 'Admin SmartCampus',
            'email' => 'admin@smartcampus.ac.id',
            'password' => 'password',
        ]);

        // ── Dosen (3 akun) ──
        $lecturerFactory = UserFactoryManager::getFactory('dosen');

        $lecturerFactory->createUser(
            ['name' => 'Dr. Budi Santoso', 'email' => 'budi@smartcampus.ac.id', 'password' => 'password'],
            ['nip' => '198501012010011001', 'department' => 'Teknik Informatika', 'jabatan' => 'Lektor']
        );

        $lecturerFactory->createUser(
            ['name' => 'Dr. Sari Dewi', 'email' => 'sari@smartcampus.ac.id', 'password' => 'password'],
            ['nip' => '198802022012022002', 'department' => 'Sistem Informasi', 'jabatan' => 'Asisten Ahli']
        );

        $lecturerFactory->createUser(
            ['name' => 'Prof. Ahmad Wijaya', 'email' => 'ahmad@smartcampus.ac.id', 'password' => 'password'],
            ['nip' => '197603032005031003', 'department' => 'Teknik Informatika', 'jabatan' => 'Guru Besar']
        );

        // ── Mahasiswa (5 akun) ──
        $studentFactory = UserFactoryManager::getFactory('mahasiswa');

        $studentFactory->createUser(
            ['name' => 'Francisco Valentino', 'email' => 'francisco@student.ac.id', 'password' => 'password'],
            ['nim' => '2472040', 'program_studi' => 'Teknik Informatika', 'semester' => 4, 'angkatan' => '2024']
        );

        $studentFactory->createUser(
            ['name' => 'Teofilus Juan P.', 'email' => 'juan@student.ac.id', 'password' => 'password'],
            ['nim' => '2472053', 'program_studi' => 'Teknik Informatika', 'semester' => 4, 'angkatan' => '2024']
        );

        $studentFactory->createUser(
            ['name' => 'Calvin Yohanis', 'email' => 'calvin@student.ac.id', 'password' => 'password'],
            ['nim' => '2272017', 'program_studi' => 'Teknik Informatika', 'semester' => 8, 'angkatan' => '2022']
        );

        $studentFactory->createUser(
            ['name' => 'Dave Andrew', 'email' => 'dave@student.ac.id', 'password' => 'password'],
            ['nim' => '2172015', 'program_studi' => 'Teknik Informatika', 'semester' => 10, 'angkatan' => '2021']
        );

        $studentFactory->createUser(
            ['name' => 'Andi Pratama', 'email' => 'andi@student.ac.id', 'password' => 'password'],
            ['nim' => '2472099', 'program_studi' => 'Sistem Informasi', 'semester' => 4, 'angkatan' => '2024']
        );
    }
}
