<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pengajuan Event Ekstrakurikuler</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        [x-cloak] { display: none !important; }
    </style>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-50">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen">
        <!-- Sidebar -->
        <div x-cloak :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed top-0 left-0 z-40 h-screen w-64 transform bg-white shadow-lg transition-transform duration-200 ease-in-out md:translate-x-0">
            <div class="flex h-16 items-center justify-center border-b">
                <h1 class="text-xl font-bold text-gray-800">Event Management</h1>
            </div>
            
            <!-- User Profile Section -->
            <div class="border-b p-4">
                <div class="flex items-center space-x-3">
                    <div class="h-12 w-12 flex-shrink-0">
                        <img class="h-12 w-12 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->nama_lengkap) }}" alt="{{ auth()->user()->nama_lengkap }}">
                    </div>
                    <div>
                        <p class="font-semibold text-gray-800">{{ auth()->user()->nama_lengkap }}</p>
                        @if(auth()->user()->role === 'user')
                            <p class="text-xs text-gray-600">Ketua {{ auth()->user()->ekskul }}</p>
                        @else
                            <p class="text-xs text-gray-600">{{ ucfirst(auth()->user()->role) }}</p>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Navigation Menu -->
            <nav class="mt-4 space-y-2 px-4">
                @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.dashboard') }}" class="flex items-center rounded-lg px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-700' : '' }}">
                    <i class="fas fa-home mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users') }}" class="flex items-center rounded-lg px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.users') ? 'bg-blue-50 text-blue-700' : '' }}">
                    <i class="fas fa-users mr-3"></i>
                    <span>Manajemen User</span>
                </a>
                <a href="{{ route('admin.events') }}" class="flex items-center rounded-lg px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.events') ? 'bg-blue-50 text-blue-700' : '' }}">
                    <i class="fas fa-tasks mr-3"></i>
                    <span>Approval Event</span>
                </a>
                <a href="{{ route('admin.reports') }}" class="flex items-center rounded-lg px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('admin.reports') ? 'bg-blue-50 text-blue-700' : '' }}">
                    <i class="fas fa-file-alt mr-3"></i>
                    <span>Laporan</span>
                </a>
                @else
                <a href="{{ route('dashboard') }}" class="flex items-center rounded-lg px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700' : '' }}">
                    <i class="fas fa-home mr-3"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('events.create') }}" class="flex items-center rounded-lg px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('events.create') ? 'bg-blue-50 text-blue-700' : '' }}">
                    <i class="fas fa-calendar-plus mr-3"></i>
                    <span>Pengajuan Event</span>
                </a>
                <a href="{{ route('events.index') }}" class="flex items-center rounded-lg px-4 py-2 text-gray-700 hover:bg-gray-100 {{ request()->routeIs('events.index') ? 'bg-blue-50 text-blue-700' : '' }}">
                    <i class="fas fa-history mr-3"></i>
                    <span>Riwayat Event</span>
                </a>
                @endif
            </nav>
            
            <!-- Logout Button -->
            <div class="absolute bottom-0 w-full border-t p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center rounded-lg bg-red-500 px-4 py-2 text-white hover:bg-red-600">
                        <i class="fas fa-sign-out-alt mr-3"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex flex-col md:pl-64">
            <!-- Top Navigation -->
            <header class="flex h-16 items-center justify-between border-b bg-white px-6">
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-500 focus:outline-none md:hidden">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex items-center">
                    <h2 class="text-xl font-semibold text-gray-800">
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
            <main class="flex-1 p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html> 