<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $currentRole = $request->role ?? 'mahasiswa';
        if (!in_array($currentRole, ['dosen', 'mahasiswa'])) {
            $currentRole = 'mahasiswa';
        }

        $query = User::with(['student', 'lecturer'])
                     ->latest()
                     ->where('role', $currentRole);

        $users = $query->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users', 'currentRole'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:dosen,mahasiswa',
            'angkatan' => 'required_if:role,mahasiswa|nullable|string|max:10',
            'department' => 'required_if:role,dosen|nullable|string|max:255',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        if ($request->role === 'mahasiswa') {
            $nim = $request->nim;
            if (!$nim) {
                // Format: {YY}72{XXX}
                // YY = 2 digit terakhir angkatan, 72 = kode prodi, XXX = urutan
                $yearPrefix = substr($request->angkatan, -2);
                $prefix = $yearPrefix . '72';
                
                $latestStudent = \App\Models\Student::where('nim', 'like', $prefix . '%')
                                    ->orderBy('nim', 'desc')
                                    ->first();
                
                if ($latestStudent) {
                    $lastSequence = (int) substr($latestStudent->nim, -3);
                    $newSequence = str_pad($lastSequence + 1, 3, '0', STR_PAD_LEFT);
                } else {
                    $newSequence = '001';
                }
                $nim = $prefix . $newSequence;
            }

            $user->student()->create([
                'nim' => $nim,
                'program_studi' => 'Teknik Informatika', // Hardcode karena scope 1 prodi
                'angkatan' => $request->angkatan,
            ]);
        } elseif ($request->role === 'dosen') {
            $user->lecturer()->create([
                'nip' => $request->nip ?: 'NIP' . rand(10000, 99999),
                'department' => $request->department,
            ]);
        }

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dibuat.');
    }

    public function show(string $id)
    {
        // Not used right now
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|in:admin,dosen,mahasiswa',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menghapus akun sendiri.');
        }
        
        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Pengguna berhasil dihapus.');
    }

    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak bisa menonaktifkan akun sendiri.');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Akun pengguna berhasil {$status}.");
    }
}
