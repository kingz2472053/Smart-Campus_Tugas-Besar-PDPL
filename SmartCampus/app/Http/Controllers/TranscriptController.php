<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TranscriptController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if (!$user->student) {
            abort(403, 'Profil mahasiswa tidak ditemukan.');
        }

        // Ambil semua submissions milik mahasiswa beserta tugas & nilainya
        $submissions = $user->student->submissions()
            ->with(['assignment.course', 'latestGrade'])
            ->latest('submitted_at')
            ->get();

        // Kelompokkan berdasarkan mata kuliah
        $groupedSubmissions = $submissions->groupBy(function($sub) {
            return $sub->assignment->course->name ?? 'Lainnya';
        });

        return view('transcript.index', compact('groupedSubmissions'));
    }
}
