@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-[url('/placeholder.svg?height=1080&width=1920')] bg-cover bg-center opacity-10"></div>

    <div class="w-full max-w-2xl bg-white/10 backdrop-blur-md border border-white/20 shadow-2xl rounded-xl p-6 relative">
        <a href="/" class="absolute top-4 left-4 text-white hover:bg-white/20 p-2 rounded">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </a>

        <div class="text-center space-y-4">
            <div class="flex justify-center">
                <div class="relative">
                    <img src="{{ asset('assets/logo.png') }}" alt="X-Cool Event Logo" width="80" height="80" class="rounded-lg mx-auto" />
                    <div class="absolute -inset-1 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg blur opacity-30"></div>
                </div>
            </div>
            <h1 class="text-2xl font-bold text-white">Bergabung dengan X-Cool Event</h1>
            <p class="text-gray-300">Buat akun baru untuk memulai perjalanan event Anda</p>
        </div>

        <form action="{{ route('register') }}" method="POST" class="space-y-4 mt-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="nama_lengkap" class="text-white block mb-1">Nama Lengkap *</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required value="{{ old('nama_lengkap') }}"
                        class="w-full bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-blue-500 rounded px-4 py-2"
                        placeholder="Masukkan nama lengkap">
                    @error('nama_lengkap')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="username" class="text-white block mb-1">Username *</label>
                    <input type="text" id="username" name="username" required value="{{ old('username') }}"
                        class="w-full bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-blue-500 rounded px-4 py-2"
                        placeholder="Masukkan username">
                    @error('username')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="text-white block mb-1">Password *</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required
                            class="w-full bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-blue-500 rounded px-4 py-2"
                            placeholder="Minimal 6 karakter">
                        <button type="button" onclick="togglePassword('password', 'eye-icon-1')"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white">
                            <svg id="eye-icon-1" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password_confirmation" class="text-white block mb-1">Konfirmasi Password *</label>
                    <div class="relative">
                        <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-blue-500 rounded px-4 py-2"
                            placeholder="Ulangi password">
                        <button type="button" onclick="togglePassword('password_confirmation', 'eye-icon-2')"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white">
                            <svg id="eye-icon-2" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <input type="hidden" name="role" value="user">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="school" class="text-white block mb-1">Nama Sekolah</label>
                    <input type="text" id="school" name="school" value="{{ old('school') }}"
                        class="w-full bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-blue-500 rounded px-4 py-2"
                        placeholder="Contoh: SMA Negeri 1 Jakarta">
                </div>
                <div>
                    <label for="ekskul" class="text-white block mb-1">Ekstrakurikuler *</label>
                    <input type="text" id="ekskul" name="ekskul" required value="{{ old('ekskul') }}"
                        class="w-full bg-white/10 border border-white/20 text-white placeholder:text-gray-400 focus:border-blue-500 rounded px-4 py-2"
                        placeholder="Contoh: Basket, Musik, Teater">
                    @error('ekskul')
                        <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <input type="checkbox" id="terms" name="terms" class="border-white/20 text-blue-500" required>
                <label for="terms" class="text-sm text-gray-300">
                    Saya menyetujui <a href="#" class="text-blue-400 hover:text-blue-300">syarat dan ketentuan</a> serta
                    <a href="#" class="text-blue-400 hover:text-blue-300">kebijakan privasi</a>
                </label>
            </div>

            @if ($errors->any())
                <div class="bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-2 rounded">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <button type="submit"
                class="w-full bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-semibold py-3 rounded">
                Daftar Sekarang
            </button>

            <div class="text-center pt-4">
                <p class="text-gray-300 text-sm">
                    Sudah punya akun? <a href="{{ route('login') }}"
                        class="text-blue-400 hover:text-blue-300 font-semibold">Masuk di sini</a>
                </p>
            </div>
        </form>
    </div>
</div>

<script>
    function togglePassword(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
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