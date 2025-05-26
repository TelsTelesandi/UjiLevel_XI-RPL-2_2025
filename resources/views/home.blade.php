@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900">
    {{-- Navigation --}}
    <nav class="bg-white/10 backdrop-blur-md border-b border-white/20 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('assets/logo.png') }}" alt="X-Cool Event Logo" width="40" height="40" class="rounded-lg" />
                    <span class="text-xl font-bold text-white hidden md:block">X-Cool Event</span>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('login') }}" class="btn-ghost text-white hover:bg-white/20">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="btn-gradient text-white">
                        Daftar Sekarang
                    </a>
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="relative min-h-screen flex items-center justify-center p-4 md:p-8">
        <div class="max-w-7xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-4 md:mb-6">
                Manajemen Event Ekstrakurikuler
            </h1>
            <p class="text-lg md:text-xl text-gray-300 mb-8 max-w-2xl mx-auto">
                Platform manajemen event yang memudahkan pengelolaan kegiatan ekstrakurikuler di sekolah Anda
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('register') }}" class="btn-lg-gradient">
                    Mulai Sekarang
                    <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
                <a href="{{ route('login') }}" class="btn-outline text-white hover:bg-white/10">
                    Sudah Punya Akun!
                </a>
            </div>
        </div>

        {{-- Floating Elements --}}
        <div class="absolute top-20 left-10 w-20 h-20 bg-blue-500/20 rounded-full blur-xl animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-32 h-32 bg-blue-500/20 rounded-full blur-xl animate-pulse delay-1000"></div>
    </section>

    {{-- Stats Section --}}
    <section class="py-16 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                @foreach ($stats as $stat)
                    <div class="bg-white/10 backdrop-blur-md border-white/20 text-center p-6 rounded-lg">
                        <div class="flex justify-center mb-3 text-blue-400">{!! $stat['icon'] !!}</div>
                        <div class="text-3xl font-bold text-white mb-2">{{ $stat['number'] }}</div>
                        <div class="text-gray-300 text-sm">{{ $stat['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section class="py-20 px-4">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <div class="inline-flex items-center gap-2 bg-blue-500/20 text-blue-300 border border-blue-500/30 rounded-full px-4 py-2 mb-4">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    <span>Fitur Unggulan</span>
                </div>
                <h2 class="text-4xl md:text-5xl font-bold text-white mb-6">Mengapa Memilih X-Cool Event?</h2>
                <p class="text-xl text-gray-300 max-w-3xl mx-auto">
                    Dilengkapi dengan fitur-fitur canggih yang memudahkan pengelolaan event ekstrakurikuler
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach ($features as $feature)
                    <div class="bg-white/5 backdrop-blur-md border border-white/10 hover:bg-white/10 transition-all duration-300 group rounded-lg p-6">
                        <div class="mb-4 group-hover:scale-110 transition-transform duration-300">{!! $feature['icon'] !!}</div>
                        <h3 class="text-xl font-semibold text-white mb-3">{{ $feature['title'] }}</h3>
                        <p class="text-gray-300">{{ $feature['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-20 px-4">
        <div class="max-w-4xl mx-auto text-center">
            <div class="bg-gradient-to-r from-blue-600/20 to-blue-600/20 backdrop-blur-md border border-white/20 rounded-lg p-12">
                <h2 class="text-4xl font-bold text-white mb-6">Siap Memulai Perjalanan Event Anda?</h2>
                <p class="text-xl text-gray-300 mb-8">
                    Bergabunglah dengan ribuan pengguna yang telah merasakan kemudahan X-Cool Event
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('register') }}" class="btn-lg-gradient text-lg">
                        <svg class="mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Daftar Gratis
                    </a>
                    <a href="{{ route('login') }}" class="btn-outline text-white hover:bg-white/10 text-lg">
                        Masuk Sekarang
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-black/20 backdrop-blur-md border-t border-white/10 py-12 px-4">
        <div class="max-w-7xl mx-auto text-center">
            <div class="flex items-center justify-center gap-3 mb-6">
                <img src="{{ asset('assets/logo.png') }}" alt="X-Cool Event Logo" width="40" height="40" class="rounded-lg" />
                <span class="text-xl font-bold text-white">X-Cool Event</span>
            </div>
            <p class="text-gray-400 mb-6">Platform manajemen event ekstrakurikuler terdepan untuk sekolah modern</p>
            <div class="text-gray-500 text-sm">© 2025 X-Cool Event. All rights reserved.</div>
        </div>
    </footer>
</div>
@endsection 