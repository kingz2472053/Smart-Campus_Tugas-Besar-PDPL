<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Lecturer;
use App\Models\Course;
use App\Models\Assignment;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssignmentUndoRedoTest extends TestCase
{
    use RefreshDatabase;

    private User $dosenUser;
    private Course $course;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Buat User Dosen
        $this->dosenUser = User::factory()->create([
            'role' => 'dosen',
            'is_active' => true,
        ]);

        // 2. Buat Dosen Profile
        $lecturer = Lecturer::create([
            'user_id' => $this->dosenUser->id,
            'nip' => '12345678',
            'department' => 'Informatika',
        ]);

        // 3. Buat Course
        $this->course = Course::create([
            'lecturer_id' => $lecturer->id,
            'name' => 'Pemrograman Berorientasi Objek',
            'code' => 'IF201',
            'semester' => '4',
        ]);
    }

    /**
     * Test Undo dan Redo pada pembuatan tugas.
     */
    public function test_undo_and_redo_create_assignment(): void
    {
        $this->actingAs($this->dosenUser);

        // 1. Kirim request untuk membuat tugas baru
        $assignmentData = [
            'course_id' => $this->course->id,
            'title' => 'Tugas Command Pattern',
            'description' => 'Implementasikan command pattern',
            'deadline' => now()->addDays(5)->toDateTimeString(),
            'max_score' => 100,
            'file_format_allowed' => 'pdf,zip',
            'max_file_size_kb' => 5000,
        ];

        $response = $this->post(route('dosen.assignments.store'), $assignmentData);
        $response->assertStatus(302);

        // Verifikasi tugas berhasil dibuat di database
        $this->assertDatabaseHas('assignments', [
            'title' => 'Tugas Command Pattern',
        ]);

        $assignment = Assignment::where('title', 'Tugas Command Pattern')->first();
        $this->assertNotNull($assignment);

        // 2. Lakukan Undo Pembuatan Tugas
        $responseUndo = $this->post(route('dosen.assignments.undo'));
        $responseUndo->assertStatus(302);
        
        // Verifikasi tugas berhasil dihapus
        $this->assertDatabaseMissing('assignments', [
            'id' => $assignment->id,
        ]);

        // 3. Lakukan Redo Pembuatan Tugas
        $responseRedo = $this->post(route('dosen.assignments.redo'));
        $responseRedo->assertStatus(302);

        // Verifikasi tugas berhasil diciptakan kembali dengan ID yang sama
        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'title' => 'Tugas Command Pattern',
        ]);
    }

    /**
     * Test Undo dan Redo pada pembaruan tugas.
     */
    public function test_undo_and_redo_update_assignment(): void
    {
        $this->actingAs($this->dosenUser);

        // 1. Buat tugas awal
        $assignment = Assignment::create([
            'course_id' => $this->course->id,
            'title' => 'Tugas Sebelum Update',
            'description' => 'Deskripsi lama',
            'deadline' => now()->addDays(5)->toDateTimeString(),
            'max_score' => 80,
            'file_format_allowed' => 'pdf',
            'max_file_size_kb' => 2000,
            'created_by' => $this->dosenUser->id,
        ]);

        // 2. Jalankan request update
        $updatedData = [
            'course_id' => $this->course->id,
            'title' => 'Tugas Sesudah Update',
            'description' => 'Deskripsi baru',
            'deadline' => now()->addDays(7)->toDateTimeString(),
            'max_score' => 100,
            'file_format_allowed' => 'zip',
            'max_file_size_kb' => 5000,
        ];

        $response = $this->put(route('dosen.assignments.update', $assignment), $updatedData);
        $response->assertStatus(302);

        // Verifikasi tugas berhasil diupdate
        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'title' => 'Tugas Sesudah Update',
        ]);

        // 3. Lakukan Undo Pembaruan Tugas
        $responseUndo = $this->post(route('dosen.assignments.undo'));
        $responseUndo->assertStatus(302);

        // Verifikasi tugas kembali ke judul lama
        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'title' => 'Tugas Sebelum Update',
        ]);

        // 4. Lakukan Redo Pembaruan Tugas
        $responseRedo = $this->post(route('dosen.assignments.redo'));
        $responseRedo->assertStatus(302);

        // Verifikasi tugas kembali ke judul baru
        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'title' => 'Tugas Sesudah Update',
        ]);
    }

    /**
     * Test Undo dan Redo pada penghapusan tugas.
     */
    public function test_undo_and_redo_delete_assignment(): void
    {
        $this->actingAs($this->dosenUser);

        // 1. Buat tugas awal
        $assignment = Assignment::create([
            'course_id' => $this->course->id,
            'title' => 'Tugas Untuk Dihapus',
            'description' => 'Deskripsi tugas',
            'deadline' => now()->addDays(5)->toDateTimeString(),
            'max_score' => 100,
            'file_format_allowed' => 'pdf',
            'max_file_size_kb' => 3000,
            'created_by' => $this->dosenUser->id,
        ]);

        // 2. Jalankan request delete
        $response = $this->delete(route('dosen.assignments.destroy', $assignment));
        $response->assertStatus(302);

        // Verifikasi tugas terhapus dari database
        $this->assertDatabaseMissing('assignments', [
            'id' => $assignment->id,
        ]);

        // 3. Lakukan Undo Penghapusan Tugas
        $responseUndo = $this->post(route('dosen.assignments.undo'));
        $responseUndo->assertStatus(302);

        // Verifikasi tugas berhasil direstorasi dengan ID asli
        $this->assertDatabaseHas('assignments', [
            'id' => $assignment->id,
            'title' => 'Tugas Untuk Dihapus',
        ]);

        // 4. Lakukan Redo Penghapusan Tugas
        $responseRedo = $this->post(route('dosen.assignments.redo'));
        $responseRedo->assertStatus(302);

        // Verifikasi tugas terhapus kembali
        $this->assertDatabaseMissing('assignments', [
            'id' => $assignment->id,
        ]);
    }
}
