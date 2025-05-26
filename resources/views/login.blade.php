@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-50 p-4 pt-0 flex-col">
    <div class="w-full max-w-md">
        <!-- Login Card -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
            <!-- Card Header -->
            <div class="px-8 py-6 bg-gradient-to-r from-blue-600 to-indigo-600">
                <h1 class="text-2xl font-bold text-white">Selamat Datang Kembali</h1>
                <p class="mt-1 text-blue-100">Masukkan kredensial Anda untuk mengakses akun</p>
            </div>
            
            <!-- Card Body -->
            <form method="POST" action="{{ route('login') }}" class="p-8 space-y-6">
                @csrf
                
                <!-- Error Message -->
                @if(session('error'))
                <div class="p-4 rounded-lg bg-red-50 border-l-4 border-red-500 flex items-start">
                    <svg class="h-5 w-5 text-red-500 mt-0.5 flex-shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
                @endif
                
                <!-- Username Field -->
                <div class="space-y-1">
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" name="username" id="username" value="{{ old('username') }}" 
                            class="block w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('username') border-red-300 @enderror"
                            placeholder="username"
                            value="{{ old('username') }}">
                    </div>
                    @error('username')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Password Field -->
                <div class="space-y-1">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    </div>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="password" name="password" id="password" 
                            class="block w-full pl-10 pr-3 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('password') border-red-300 @enderror"
                            placeholder="••••••••"
                            value="{{ old('password') }}">
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="w-full flex justify-center items-center py-3 px-4 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-medium shadow-md hover:from-blue-700 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 transform hover:-translate-y-0.5">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                    Masuk
                </button>
                
                <!-- Register Link -->
                <div class="text-center pt-4">
                    <p class="text-sm text-gray-600">
                        Belum memiliki akun? 
                        <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-500 transition-colors duration-200">
                            Daftar sekarang
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </div>
    @include('layouts.footer')
</div>
@endsection