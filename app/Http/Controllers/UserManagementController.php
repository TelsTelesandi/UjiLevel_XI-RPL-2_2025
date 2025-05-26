<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::where('role', 'user')->latest()->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
            'nama_lengkap' => 'required',
            'ekskul' => 'required'
        ], [
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'nama_lengkap.required' => 'Nama lengkap harus diisi',
            'ekskul.required' => 'Ekstrakurikuler harus diisi'
        ]);

        User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'nama_lengkap' => $request->nama_lengkap,
            'ekskul' => $request->ekskul,
            'role' => 'user'
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'username' => ['required', Rule::unique('users')->ignore($user->user_id, 'user_id')],
            'nama_lengkap' => 'required',
            'ekskul' => 'required'
        ], [
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'nama_lengkap.required' => 'Nama lengkap harus diisi',
            'ekskul.required' => 'Ekstrakurikuler harus diisi'
        ]);

        $data = [
            'username' => $request->username,
            'nama_lengkap' => $request->nama_lengkap,
            'ekskul' => $request->ekskul
        ];

        // Update password jika diisi
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:6'
            ], [
                'password.min' => 'Password minimal 6 karakter'
            ]);
            
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'Data user berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak dapat menghapus akun admin');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User berhasil dihapus');
    }
} 