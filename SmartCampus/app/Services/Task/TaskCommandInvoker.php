<?php

namespace App\Services\Task;

use App\Contracts\TaskCommandInterface;
use App\Services\ActivityLogger;
use App\Models\ActivityLog;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Grade;
use Illuminate\Support\Facades\Session;

/**
 * TaskCommandInvoker — Invoker (Command Pattern)
 *
 * Bertanggung jawab menjalankan command dan mencatat aktivitas
 * secara otomatis ke ActivityLogger (Singleton Pattern).
 * Serta mengelola riwayat undo/redo stack di session.
 */
class TaskCommandInvoker
{
    /**
     * Menjalankan command, mencatat hasil, dan mengelola session stack.
     *
     * @param TaskCommandInterface $command Command yang akan dijalankan
     * @param int                  $userId  ID pengguna yang menjalankan command
     * @return mixed Hasil eksekusi command
     */
    public function execute(TaskCommandInterface $command, int $userId): mixed
    {
        // 1. Eksekusi command (operasi database)
        $result = $command->execute();

        // 2. Catat ke ActivityLogger (Singleton Pattern)
        $log = ActivityLogger::getInstance()->log(
            action: $command->getAction(),
            userId: $userId,
            targetTable: $command->getTargetTable(),
            targetId: $command->getTargetId(),
            detail: $command->getDetail()
        );

        if ($log) {
            // 3. Tambahkan ke Session Undo Stack
            $this->pushToUndoStack($log->id);

            // 4. Bersihkan Redo Stack karena ada aksi baru
            Session::forget('task_redo_stack');
            
            // Tandai untuk menampilkan tombol Undo di notifikasi flash
            Session::flash('show_undo', true);
        }

        // 5. Kembalikan hasil
        return $result;
    }

    /**
     * Memasukkan log ID ke undo stack.
     */
    private function pushToUndoStack(int $logId): void
    {
        $undoStack = Session::get('task_undo_stack', []);
        array_push($undoStack, $logId);
        
        // Batasi ukuran stack maksimal 15
        if (count($undoStack) > 15) {
            array_shift($undoStack);
        }
        
        Session::put('task_undo_stack', $undoStack);
    }

    /**
     * Undo aksi terakhir.
     */
    public function undo(int $userId): ?string
    {
        $undoStack = Session::get('task_undo_stack', []);
        if (empty($undoStack)) {
            return null;
        }

        $logId = array_pop($undoStack);
        Session::put('task_undo_stack', $undoStack);

        $log = ActivityLog::find($logId);
        if (!$log || $log->user_id !== $userId) {
            return null;
        }

        // Jalankan operasi pembatalan (undo)
        $message = $this->performUndo($log);

        if ($message) {
            // Masukkan log ke redo stack
            $redoStack = Session::get('task_redo_stack', []);
            array_push($redoStack, $logId);
            Session::put('task_redo_stack', $redoStack);
        }

        return $message;
    }

    /**
     * Redo aksi terakhir.
     */
    public function redo(int $userId): ?string
    {
        $redoStack = Session::get('task_redo_stack', []);
        if (empty($redoStack)) {
            return null;
        }

        $logId = array_pop($redoStack);
        Session::put('task_redo_stack', $redoStack);

        $log = ActivityLog::find($logId);
        if (!$log || $log->user_id !== $userId) {
            return null;
        }

        // Jalankan kembali operasi (redo)
        $message = $this->performRedo($log);

        if ($message) {
            // Masukkan kembali log ke undo stack
            $undoStack = Session::get('task_undo_stack', []);
            array_push($undoStack, $logId);
            Session::put('task_undo_stack', $undoStack);
        }

        return $message;
    }

    /**
     * Melakukan pembatalan aksi (undo) database.
     */
    private function performUndo(ActivityLog $log): ?string
    {
        $detail = $log->detail_json;
        $targetId = $log->target_id;

        switch ($log->action) {
            case 'CREATE_ASSIGNMENT':
                $assignment = Assignment::find($targetId);
                if ($assignment) {
                    $title = $assignment->title;
                    $assignment->delete();
                    return "Pembuatan tugas '{$title}' berhasil dibatalkan (Undo).";
                }
                break;

            case 'UPDATE_ASSIGNMENT':
                $assignment = Assignment::find($targetId);
                if ($assignment && isset($detail['before'])) {
                    $assignment->update($detail['before']);
                    return "Pembaruan tugas '{$assignment->title}' berhasil dibatalkan (Undo).";
                }
                break;

            case 'DELETE_ASSIGNMENT':
                if (isset($detail['deleted'])) {
                    $deletedData = $detail['deleted'];
                    
                    // Restore assignment
                    $fillableAssignment = array_intersect_key($deletedData, array_flip([
                        'course_id', 'title', 'description', 'deadline', 'max_score', 'file_format_allowed', 'max_file_size_kb', 'created_by'
                    ]));
                    $assignment = new Assignment($fillableAssignment);
                    $assignment->id = $targetId;
                    $assignment->save();

                    // Restore submissions
                    if (!empty($deletedData['submissions'])) {
                        foreach ($deletedData['submissions'] as $subData) {
                            $fillableSub = array_intersect_key($subData, array_flip([
                                'student_id', 'file_path', 'file_name', 'file_format', 'file_size_kb', 'submitted_at', 'status', 'progress'
                            ]));
                            $submission = new Submission($fillableSub);
                            $submission->assignment_id = $assignment->id;
                            $submission->id = $subData['id'];
                            $submission->save();

                            // Restore grades
                            if (!empty($subData['grades'])) {
                                foreach ($subData['grades'] as $gradeData) {
                                    $fillableGrade = array_intersect_key($gradeData, array_flip([
                                        'graded_by', 'grading_strategy', 'raw_score', 'result', 'graded_at'
                                    ]));
                                    $grade = new Grade($fillableGrade);
                                    $grade->submission_id = $submission->id;
                                    $grade->id = $gradeData['id'];
                                    $grade->save();
                                }
                            }
                        }
                    }
                    return "Penghapusan tugas '{$assignment->title}' berhasil dibatalkan (Undo).";
                }
                break;
        }

        return null;
    }

    /**
     * Melakukan eksekusi kembali aksi (redo) database.
     */
    private function performRedo(ActivityLog $log): ?string
    {
        $detail = $log->detail_json;
        $targetId = $log->target_id;

        switch ($log->action) {
            case 'CREATE_ASSIGNMENT':
                if (isset($detail['created'])) {
                    $createdData = $detail['created'];
                    $fillableAssignment = array_intersect_key($createdData, array_flip([
                        'course_id', 'title', 'description', 'deadline', 'max_score', 'file_format_allowed', 'max_file_size_kb', 'created_by'
                    ]));
                    $assignment = new Assignment($fillableAssignment);
                    $assignment->id = $targetId;
                    $assignment->save();
                    return "Pembuatan tugas '{$assignment->title}' berhasil dijalankan kembali (Redo).";
                }
                break;

            case 'UPDATE_ASSIGNMENT':
                $assignment = Assignment::find($targetId);
                if ($assignment && isset($detail['after'])) {
                    $assignment->update($detail['after']);
                    return "Pembaruan tugas '{$assignment->title}' berhasil dijalankan kembali (Redo).";
                }
                break;

            case 'DELETE_ASSIGNMENT':
                $assignment = Assignment::find($targetId);
                if ($assignment) {
                    $title = $assignment->title;
                    $assignment->delete();
                    return "Penghapusan tugas '{$title}' berhasil dijalankan kembali (Redo).";
                }
                break;
        }

        return null;
    }
}
