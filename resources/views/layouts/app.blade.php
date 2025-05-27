<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pengajuan Event Ekstrakurikuler</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        body {
            font-family: 'Outfit', sans-serif;
        }
        .transition-all {
            transition: all 0.3s ease-in-out;
        }
        .hover-scale {
            transition: transform 0.2s ease-in-out;
        }
        .hover-scale:hover {
            transform: scale(1.02);
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(8px);
        }
        .gradient-bg {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        }
        .nav-link {
            position: relative;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background: #3b82f6;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-50">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">
        <!-- Sidebar -->
        <div x-cloak :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
             class="fixed top-0 left-0 z-40 h-screen w-80 transform bg-white shadow-lg transition-all duration-300 ease-in-out md:translate-x-0">
            <div class="flex h-24 items-center justify-center border-b gradient-bg">
                <h1 class="text-2xl font-bold text-white tracking-tight">Event Management</h1>
            </div>
            
            <!-- User Profile Section -->
            <div class="border-b p-6 bg-gradient-to-br from-blue-50 to-slate-50">
                <div class="flex items-center space-x-4">
                    <div class="h-16 w-16 flex-shrink-0">
                        <img class="h-16 w-16 rounded-xl ring-2 ring-blue-500 shadow-md" 
                             src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nama_lengkap) }}&background=1e40af&color=fff" 
                             alt="{{ auth()->user()->nama_lengkap }}">
                    </div>
                    <div>
                        <p class="font-semibold text-slate-800 text-lg">{{ auth()->user()->nama_lengkap }}</p>
                        @if(auth()->user()->role === 'user')
                            <p class="text-sm text-blue-600 font-medium">Ketua {{ auth()->user()->ekskul }}</p>
                        @else
                            <p class="text-sm text-blue-600 font-medium">{{ ucfirst(auth()->user()->role) }}</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Navigation Menu -->
            <nav class="mt-8 space-y-2 px-6">
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" 
                   class="nav-link flex items-center rounded-xl px-5 py-4 text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
                    <i class="fas fa-home mr-4 text-lg"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users') }}" 
                   class="nav-link flex items-center rounded-xl px-5 py-4 text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-all {{ request()->routeIs('admin.users') ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
                    <i class="fas fa-users mr-4 text-lg"></i>
                    <span>Manajemen User</span>
                </a>
                <a href="{{ route('admin.events') }}" 
                   class="nav-link flex items-center rounded-xl px-5 py-4 text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-all {{ request()->routeIs('admin.events') ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
                    <i class="fas fa-tasks mr-4 text-lg"></i>
                    <span>Approval Event</span>
                </a>
                <a href="{{ route('admin.reports') }}" 
                   class="nav-link flex items-center rounded-xl px-5 py-4 text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-all {{ request()->routeIs('admin.reports') ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
                    <i class="fas fa-file-alt mr-4 text-lg"></i>
                    <span>Laporan</span>
                </a>
                @else
                <a href="{{ route('dashboard') }}" 
                   class="nav-link flex items-center rounded-xl px-5 py-4 text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-all {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
                    <i class="fas fa-home mr-4 text-lg"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('events.create') }}" 
                   class="nav-link flex items-center rounded-xl px-5 py-4 text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-all {{ request()->routeIs('events.create') ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
                    <i class="fas fa-calendar-plus mr-4 text-lg"></i>
                    <span>Pengajuan Event</span>
                </a>
                <a href="{{ route('events.index') }}" 
                   class="nav-link flex items-center rounded-xl px-5 py-4 text-slate-700 hover:bg-blue-50 hover:text-blue-700 transition-all {{ request()->routeIs('events.index') ? 'bg-blue-50 text-blue-700 font-medium' : '' }}">
                    <i class="fas fa-history mr-4 text-lg"></i>
                    <span>Riwayat Event</span>
                </a>
                @endif
            </nav>
            
            <!-- Logout Button -->
            <div class="absolute bottom-0 w-full border-t p-6 bg-gradient-to-br from-blue-50 to-slate-50">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="flex w-full items-center justify-center rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 px-5 py-4 text-white hover:from-blue-700 hover:to-blue-800 transition-all hover:shadow-md">
                        <i class="fas fa-sign-out-alt mr-4"></i>
                        <span class="font-medium">Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col md:pl-80">
            <!-- Top Navigation -->
            <header class="sticky top-0 z-30 flex h-24 items-center justify-between glass-effect px-8 shadow-sm">
                <button @click="sidebarOpen = !sidebarOpen" 
                        class="rounded-xl p-3 text-slate-600 hover:bg-blue-50 hover:text-blue-700 focus:outline-none md:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex items-center">
                    <h2 class="text-2xl font-bold text-slate-800">
                        @if(request()->routeIs('dashboard'))
                            Dashboard
                        @elseif(request()->routeIs('events.create'))
                            Pengajuan Event Baru
                        @elseif(request()->routeIs('events.index'))
                            Riwayat Event
                        @elseif(request()->routeIs('events.show'))
                            Detail Event
                        @elseif(request()->routeIs('admin.dashboard'))
                            Dashboard Admin
                        @elseif(request()->routeIs('admin.users'))
                            Manajemen User
                        @elseif(request()->routeIs('admin.events'))
                            Approval Event
                        @elseif(request()->routeIs('admin.reports'))
                            Laporan
                        @else
                            Sistem Pengajuan Event Ekstrakurikuler
                        @endif
                    </h2>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 p-8">
                <div class="mx-auto max-w-7xl">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
</body>
</html> 