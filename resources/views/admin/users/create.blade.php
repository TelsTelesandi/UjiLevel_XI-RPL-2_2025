@extends('layouts.app')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Tambah User</h1>
            <p class="text-gray-400">Tambah akun pengguna baru</p>
        </div>
        <a href="{{ route('admin.users.index') }}" class="btn-ghost">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="max-w-xl">
        <form action="{{ route('admin.users.store') }}" method="POST" class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6 space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2" for="username">
                    Username
                </label>
                <input type="text" 
                    name="username" 
                    id="username" 
                    value="{{ old('username') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:border-blue-500"
                    required>
                @error('username')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2" for="password">
                    Password
                </label>
                <input type="password" 
                    name="password" 
                    id="password"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:border-blue-500"
                    required>
                @error('password')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2" for="nama_lengkap">
                    Nama Lengkap
                </label>
                <input type="text" 
                    name="nama_lengkap" 
                    id="nama_lengkap" 
                    value="{{ old('nama_lengkap') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:border-blue-500"
                    required>
                @error('nama_lengkap')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2" for="ekskul">
                    Ekstrakurikuler
                </label>
                <input type="text" 
                    name="ekskul" 
                    id="ekskul" 
                    value="{{ old('ekskul') }}"
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:border-blue-500"
                    required>
                @error('ekskul')
                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-gradient">
                    <i class="fas fa-save mr-2"></i>Simpan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 