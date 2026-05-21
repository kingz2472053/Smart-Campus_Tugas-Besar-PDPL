<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Assignment;
use App\Models\Enrollment;
use App\Models\Submission;
use App\Observers\DeadlineNotifier;
use Carbon\Carbon;

class SendDeadlineReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:send-deadline';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengecek tugas yang H-1 dan memicu Observer untuk mengirim notifikasi';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Memulai pengecekan tugas H-1...");

        // 1. Cari tugas yang deadline-nya besok (H-1)
        $tomorrow = Carbon::tomorrow()->toDateString();
        $assignments = Assignment::whereDate('deadline', $tomorrow)->get();

        if ($assignments->isEmpty()) {
            $this->info("Tidak ada tugas yang deadline besok.");
            return;
        }

        // Siapkan Observer-nya
        $notifier = new DeadlineNotifier();

        foreach ($assignments as $assignment) {
            // 2. Ambil semua ID mahasiswa yang terdaftar (enrollments) di MK tugas ini
            $enrolledStudentIds = Enrollment::where('course_id', $assignment->course_id)
                ->where('status', 'active')
                ->pluck('student_id')
                ->toArray();

            // 3. Ambil ID mahasiswa yang sudah mengumpulkan (progress = 'completed')
            $completedStudentIds = Submission::where('assignment_id', $assignment->id)
                ->where('progress', 'completed')
                ->pluck('student_id')
                ->toArray();

            // 4. Filter: Targetkan hanya yang BELUM completed
            $targetStudentIds = array_diff($enrolledStudentIds, $completedStudentIds);

            if (!empty($targetStudentIds)) {
                // Attach observer ke assignment
                $assignment->attach($notifier);
                
                $this->info("Tugas '{$assignment->title}' H-1. Memicu Observer untuk " . count($targetStudentIds) . " mahasiswa.");
                
                // 5. Trigger observer!
                $assignment->notifyObservers($targetStudentIds);
            }
        }

        $this->info("Pengecekan selesai.");
    }
}
