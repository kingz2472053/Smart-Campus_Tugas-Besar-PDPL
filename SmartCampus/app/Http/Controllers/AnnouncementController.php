<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') abort(403);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        \App\Models\Announcement::create([
            'title' => $request->title,
            'content' => $request->content,
            'user_id' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Pengumuman berhasil dipublikasikan!');
    }

    public function destroy(\App\Models\Announcement $announcement)
    {
        if (auth()->user()->role !== 'admin') abort(403);
        $announcement->delete();
        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus!');
    }
}
