<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Tampilkan halaman pusat notifikasi.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Ambil filter status: all, read, unread
        $status = $request->query('status', 'all');
        
        $query = Notification::where('user_id', $user->id)
            ->with('assignment.course')
            ->latest();

        if ($status === 'unread') {
            $query->where('is_read', false);
        } elseif ($status === 'read') {
            $query->where('is_read', true);
        }

        $notifications = $query->paginate(15)->withQueryString();

        return view('notifications.index', compact('notifications', 'status'));
    }

    /**
     * Mendapatkan 5 notifikasi teratas yang belum dibaca (untuk dropdown topbar AJAX).
     */
    public function getUnread()
    {
        $user = Auth::user();
        
        $notifications = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->with('assignment.course')
            ->latest()
            ->take(5)
            ->get();

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai terbaca.
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $notification->update(['is_read' => true]);

        return response()->json(['success' => true, 'message' => 'Notifikasi ditandai sebagai terbaca.']);
    }

    /**
     * Tandai seluruh notifikasi user aktif sebagai terbaca.
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['success' => true, 'message' => 'Semua notifikasi ditandai sebagai terbaca.']);
    }

    /**
     * Hapus notifikasi tertentu.
     */
    public function destroy($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $notification->delete();

        return redirect()->back()->with('success', 'Notifikasi berhasil dihapus.');
    }
}
