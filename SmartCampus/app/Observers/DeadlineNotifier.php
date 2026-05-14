<?php

namespace App\Observers;

use App\Contracts\ObserverInterface;
use App\Models\Assignment;
use App\Models\Student;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;

class DeadlineNotifier implements ObserverInterface
{
    public function update(Assignment $assignment, array $targetStudentIds): void
    {
        // 1. Logika pengiriman notifikasi berjalan di sini
        Log::info("Menerima pemberitahuan deadline H-1 untuk Tugas ID: {$assignment->id}");

        // 2. Loop ke setiap mahasiswa yang belum mengumpulkan tugas
        foreach ($targetStudentIds as $studentId) {
            // Cari data user_id dari mahasiswa tersebut
            $student = Student::find($studentId);

            if ($student) {
                // Simpan notifikasi ke database
                Notification::create([
                    'user_id' => $student->user_id,
                    'assignment_id' => $assignment->id,
                    'channel' => 'dashboard',
                    'message' => "PENGINGAT: Tugas '{$assignment->title}' dari mata kuliah {$assignment->course->name} akan ditutup besok!",
                    'is_read' => false,
                ]);

                Log::info("--> Mengirim notifikasi H-1 ke User ID: {$student->user_id}");
            }
            // TODO: Integrasi dengan modul NotifFactory buatan Juan.
            // Contoh implementasinya nanti akan seperti ini:
            // $notification = NotifFactory::create('dashboard');
            // $notification->send($studentId, "Jangan lupa! Tugas {$assignment->title} deadline besok.");
        }
    }
}