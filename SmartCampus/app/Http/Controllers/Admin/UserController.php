<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            'role' => 'required|in:dosen,mahasiswa',
            'users' => 'required|array|min:1',
            'users.*.name' => 'required|string|max:255',
            'users.*.email' => 'required|string|email|max:255|unique:users,email',
            'users.*.attribute' => 'required|string|max:255', // Angkatan for Mahasiswa, Dept for Dosen
        ]);

        $generatedUsers = [];

        foreach ($request->users as $userData) {
            $plainPassword = Str::random(8); // Auto-generate password

            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make($plainPassword),
                'role' => $request->role,
            ]);

            if ($request->role === 'mahasiswa') {
                $nim = $userData['identifier'] ?? null;
                $angkatan = $userData['attribute'];

                if (!$nim) {
                    $yearPrefix = substr($angkatan, -2);
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
                    'program_studi' => 'Teknik Informatika',
                    'angkatan' => $angkatan,
                ]);
            } elseif ($request->role === 'dosen') {
                $user->lecturer()->create([
                    'nip' => !empty($userData['identifier']) ? $userData['identifier'] : 'NIP' . rand(10000, 99999),
                    'department' => $userData['attribute'],
                ]);
            }

            $generatedUsers[] = [
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => $plainPassword
            ];
        }

        return redirect()->route('admin.users.index', ['role' => $request->role])
                         ->with('success', count($generatedUsers) . ' pengguna berhasil dibuat.')
                         ->with('generated_users', $generatedUsers);
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
