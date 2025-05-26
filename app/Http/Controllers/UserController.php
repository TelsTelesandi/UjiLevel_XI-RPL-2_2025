<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends \Illuminate\Routing\Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'ekskul' => 'required|string|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:user,admin',
        ],[
            'username.unique' => 'Username sudah terdaftar.',
            'username.required' => 'Username tidak boleh kosong.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'ekskul.required' => 'Ekstrakurikuler tidak boleh kosong.',
            'ekskul.string' => 'Ekstrakurikuler harus berupa string.',
            'ekskul.max' => 'Ekstrakurikuler tidak boleh lebih dari 255 karakter.',
            'nama_lengkap.required' => 'Nama lengkap tidak boleh kosong.',
            'nama_lengkap.string' => 'Nama lengkap harus berupa string.',
            'nama_lengkap.max' => 'Nama lengkap tidak boleh lebih dari 255 karakter.',
            'password.required' => 'Password tidak boleh kosong.',
            'password.string' => 'Password harus berupa string.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.regex' => 'Password harus mengandung setidaknya satu huruf besar, satu huruf kecil, satu angka, dan satu karakter khusus.',
            'password.max' => 'Password tidak boleh lebih dari 255 karakter.',
            'role.required' => 'Peran pengguna tidak boleh kosong'
        ]);
        
        User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'ekskul' => $request->ekskul,
            'password' => Hash::make($request->password),
            'role' => $request->role ?? 'user',
        ]);
        
        return redirect()->route('admin.users')->with('success', 'Pengguna berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users,username,'.$user->user_id.',user_id',
            'nama_lengkap' => 'required|string|max:255',
            'ekskul' => 'required|string|max:255',
            'role' => 'required|in:user,admin',
            'password' => ['nullable', 'confirmed', Rules\Password::min(8)],
        ],[
            'username.unique' => 'Username sudah terdaftar.',
            'username.required' => 'Username tidak boleh kosong.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'ekskul.required' => 'Ekstrakurikuler tidak boleh kosong.',
            'ekskul.string' => 'Ekstrakurikuler harus berupa string.',
            'ekskul.max' => 'Ekstrakurikuler tidak boleh lebih dari 255 karakter.',
            'nama_lengkap.required' => 'Nama lengkap tidak boleh kosong.',
            'nama_lengkap.string' => 'Nama lengkap harus berupa string.',
            'nama_lengkap.max' => 'Nama lengkap tidak boleh lebih dari 255 karakter.',
            'password.string' => 'Password harus berupa string.',
            'password.min' => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.regex' => 'Password harus mengandung setidaknya satu huruf besar, satu huruf kecil, satu angka, dan satu karakter khusus.',
            'password.max' => 'Password tidak boleh lebih dari 255 karakter.',
            'role.required' => 'Peran pengguna tidak boleh kosong'
        ]);

        $updateData = [
            'username' => $validated['username'],
            'nama_lengkap' => $validated['nama_lengkap'],
            'ekskul' => $validated['ekskul'],
            'role' => $validated['role'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully.');
    }
}