<?php

namespace App\Observers;

use App\Contracts\ObserverInterface;
use App\Models\Assignment;
use App\Models\Student;
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
            $student = Student::with('user')->find($studentId);

            if ($student && $student->user) {
                // Gunakan Factory Method Pattern untuk mengirim notifikasi
                $sender = new \App\Services\Notification\DashboardNotificationSender();
                $sender->sendNotification(
                    $student->user,
                    "PENGINGAT: Tugas '{$assignment->title}' dari mata kuliah {$assignment->course->name} akan ditutup besok!",
                    ['assignment_id' => $assignment->id]
                );

                Log::info("--> Mengirim notifikasi H-1 ke User ID: {$student->user_id}");
            }
        }
    }
}