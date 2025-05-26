@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white/10 backdrop-blur-md border border-white/20 shadow-2xl rounded-xl">
        <div class="relative text-center space-y-4 px-6 pt-10 pb-4">
            <a href="{{ url('/') }}" class="absolute top-4 left-4 text-white hover:bg-white/20 p-2 rounded-full">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>

            <div class="flex justify-center">
                <div class="relative">
                    <img src="{{ asset('assets/logo.png') }}" alt="X-Cool Event Logo" class="w-20 h-20 rounded-lg mx-auto" />
                    <div class="absolute -inset-1 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg blur opacity-30"></div>
                </div>
            </div>

            <h2 class="text-2xl font-bold text-white">Selamat Datang Kembali</h2>
            <p class="text-gray-300">Masuk ke akun X-Cool Event Anda</p>
        </div>

        <div class="px-6 pb-8">
            @if($errors->any())
                <div class="bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-2 rounded mb-4">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form x-data="{ submitting: false }" 
                  @submit.prevent="if(!submitting) { submitting = true; $el.submit(); }"
                  action="{{ route('login') }}" 
                  method="POST" 
                  class="space-y-4" 
                  id="loginForm">
                @csrf

                <div class="space-y-2">
                    <label for="username" class="text-white">Username</label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           value="{{ old('username') }}"
                           class="w-full bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-blue-500 rounded px-4 py-2"
                           placeholder="Masukkan username Anda" 
                           required 
                           autofocus>
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-white">Password</label>
                    <div class="relative">
                        <input type="password" 
                               id="password" 
                               name="password"
                               class="w-full bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-blue-500 rounded px-4 py-2 pr-10"
                               placeholder="Masukkan password Anda" 
                               required>
                        <button type="button" 
                                @click="togglePassword()" 
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white">
                            <svg id="eye-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <label class="flex items-center">
                        <input type="checkbox" name="remember" class="bg-white/10 border border-white/20 text-blue-500 rounded">
                        <span class="ml-2 text-sm text-gray-300">Ingat saya</span>
                    </label>
                </div>

                <button type="submit" 
                        id="loginButton"
                        x-bind:disabled="submitting"
                        x-text="submitting ? 'Memproses...' : 'Masuk'"
                        class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-3 rounded transition-all duration-200 ease-in-out transform hover:scale-[1.02] active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">
                </button>

                <div class="text-center pt-4">
                    <p class="text-gray-300 text-sm">
                        Belum punya akun?
                        <a href="{{ route('register') }}" 
                           class="text-blue-400 hover:text-blue-300 font-semibold transition-colors duration-200">
                            Daftar di sini
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById("password");
        const icon = document.getElementById("eye-icon");
        if (input.type === "password") {
            input.type = "text";
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.27-2.943-9.544-7a10.05 10.05 0 012.13-3.368m3.252-2.29A9.964 9.964 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.956 9.956 0 01-4.243 5.133M15 12a3 3 0 11-6 0 3 3 0 016 0z" />`;
        } else {
            input.type = "password";
            icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
        }
    }
</script>
@endsection 