<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AuthController extends \Illuminate\Routing\Controller
{
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    public function loginForm()
    {
        return view('login');
    }
    
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ],[
            'username.required' => 'Username tidak boleh kosong.',
            'password.required' => 'Password tidak boleh kosong.',
        ]);
        
        $credentials = $request->only('username', 'password');
        
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            if (Auth::user()->role === 'admin') {
                return redirect()->intended(route('admin.index'));
            } else {
                return redirect()->intended(route('user.index'));
            }
        }
        
        return back()->with('error', 'Username atau password tidak valid.')->withInput();
    }
    
    public function registerForm()
    {
        return view('register');
    }
    
    public function register(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'ekskul' => 'required|string|max:255',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
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
        ]);
        
        $user = User::create([
            'nama_lengkap' => $request->nama_lengkap,
            'username' => $request->username,
            'ekskul' => $request->ekskul,
            'password' => Hash::make($request->password),
            'role' => 'user', // Default role is user
        ]);
        
        Auth::login($user);
        
        return redirect()->route('user.index');
    }
    
    public function logout(Request $request)
    {
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('home');
    }
}