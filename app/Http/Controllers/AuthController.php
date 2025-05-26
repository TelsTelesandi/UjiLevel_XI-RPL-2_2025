<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required'
        ], [
            'username.required' => 'Username harus diisi',
            'password.required' => 'Password harus diisi'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            return redirect()->intended(
                Auth::user()->role === 'admin'
                    ? route('admin.dashboard')
                    : route('user.dashboard')
            );
        }

        return back()->withErrors([
            'username' => 'Username atau password salah',
        ])->onlyInput('username');
    }

    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users',
            'password' => 'required|min:6|confirmed',
            'nama_lengkap' => 'required',
            'ekskul' => 'required'
        ], [
            'username.required' => 'Username harus diisi',
            'username.unique' => 'Username sudah digunakan',
            'password.required' => 'Password harus diisi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
            'nama_lengkap.required' => 'Nama lengkap harus diisi',
            'ekskul.required' => 'Ekstrakurikuler harus diisi'
        ]);

        $user = User::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'nama_lengkap' => $request->nama_lengkap,
            'ekskul' => $request->ekskul
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('user.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
} 