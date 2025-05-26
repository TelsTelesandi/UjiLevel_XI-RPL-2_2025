<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'Sistem Manajemen Acara Kegiatan') }}</title>
    <link rel="icon" href="{{ asset('src/proposal icon.png') }}" type="image/x-icon">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                            950: '#082f49',
                        }
                    },
                }
            }
        }
    </script>
</head>
<body class="font-sans antialiased @if(request()->routeIs('login') || request()->routeIs('register'))bg-gradient-to-br from-blue-50 to-indigo-50 @else bg-gray-50 @endif text-gray-900 min-h-screen">
    <header class="@if(request()->routeIs('login') || request()->routeIs('register'))!hidden @else fixed top-0 left-0 right-0 z-50 bg-white border-b shadow-sm @endif">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center">
                    <img src="{{ asset('src/proposal icon.png') }}" alt="" class="h-12 w-13 mt-0.5 mr-1">
                    <a href="{{ route('home') }}" class="font-bold text-xl max-md:hidden">
                        Manajemen Acara Kegiatan
                    </a>
                </div>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-6">
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.index') }}" class="text-sm font-medium {{ request()->routeIs('admin.index') ? 'text-blue-600' : 'text-gray-500 hover:text-gray-900' }}">
                                Dasbor
                            </a>
                            <a href="{{ route('admin.users') }}" class="text-sm font-medium {{ request()->routeIs('admin.users') ? 'text-blue-600' : 'text-gray-500 hover:text-gray-900' }}">
                                Kelola Pengguna
                            </a>
                            <a href="{{ route('admin.riwayat') }}" class="text-sm font-medium {{ request()->routeIs('admin.riwayat') ? 'text-blue-600' : 'text-gray-500 hover:text-gray-900' }}">
                                Riwayat Pengajuan
                            </a>
                        @elseif(auth()->user()->role === 'user')
                            <a href="{{ route('user.index') }}" class="text-sm font-medium {{ request()->routeIs('user.index') ? 'text-blue-600' : 'text-gray-500 hover:text-gray-900' }}">
                                Dasbor
                            </a>
                            <a href="{{ route('user.riwayat') }}" class="text-sm font-medium {{ request()->routeIs('user.riwayat') ? 'text-blue-600' : 'text-gray-500 hover:text-gray-900' }}">
                                Riwayat Pengajuan
                            </a>
                        @endif

                        <div class="flex items-center space-x-2 text-sm font-medium text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>{{ auth()->user()->nama_lengkap }}</span>
                        </div>

                        <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah kamu yakin ingin keluar?')">
                            @csrf
                            <button type="submit" class="p-2 text-gray-500 hover:text-gray-900">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium {{ request()->routeIs('login') ? 'text-blue-600' : 'text-gray-500 hover:text-gray-900' }}">
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            Daftar
                        </a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button" id="mobile-menu-button" class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500">
                        <span class="sr-only">Open main menu</span>
                        <svg class="block h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="hidden h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-white border-t">
            <div class="container mx-auto px-4 py-3 space-y-2">
                @auth
                    @if(auth()->user()->role === 'admin')
                        <a href="{{ route('admin.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
                            Kelola Kegiatan
                        </a>
                        <a href="{{ route('admin.users') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.users') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
                            Kelola Pengguna
                        </a>
                        <a href="{{ route('admin.riwayat') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('admin.riwayat') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
                            Riwayat Pengajuan
                        </a>
                    @elseif(auth()->user()->role === 'user')
                        <a href="{{ route('user.index') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('user.index') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
                            Dasbor
                        </a>
                        <a href="{{ route('user.riwayat') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('user.riwayat') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
                            Riwayat Pengajuan
                        </a>
                    @endif

                    <div class="flex items-center px-3 pb-2 pt-5 text-gray-700">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        {{ auth()->user()->nama_lengkap }}
                    </div>

                    <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Apakah kamu yakin ingin keluar?')">
                        @csrf
                        <button type="submit" class="w-full text-left flex items-center bg-red-600 px-3 py-2 rounded-md text-base font-medium text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 ml-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Keluar
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('login') ? 'bg-blue-50 text-blue-600' : 'text-gray-700 hover:bg-gray-100' }}">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="block px-3 py-2 rounded-md text-base font-medium bg-blue-50 text-blue-600">
                        Daftar
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="pt-16 min-h-screen">
        @yield('content')
    </main>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mobileMenuButton = document.getElementById('mobile-menu-button');
            const mobileMenu = document.getElementById('mobile-menu');
            
            mobileMenuButton.addEventListener('click', function() {
                const isExpanded = mobileMenuButton.getAttribute('aria-expanded') === 'true';
                mobileMenuButton.setAttribute('aria-expanded', !isExpanded);
                
                mobileMenuButton.querySelectorAll('svg').forEach(icon => {
                    icon.classList.toggle('hidden');
                });
                
                mobileMenu.classList.toggle('hidden');
            });

            const flashMessage = document.getElementById('flash-message');
            if (flashMessage) {
                setTimeout(() => {
                    flashMessage.style.opacity = '0';
                    setTimeout(() => {
                        flashMessage.style.display = 'none';
                    }, 500);
                }, 5000);
            }
        });
    </script>
</body>
</html>