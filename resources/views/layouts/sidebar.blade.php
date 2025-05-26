@auth
    <aside x-data="{ isOpen: false }" class="fixed inset-y-0 left-0 z-50">
        <!-- Mobile Menu Toggle -->
        <button @click="isOpen = !isOpen" class="lg:hidden fixed top-4 left-4 z-50 p-2 rounded-md bg-white/10 backdrop-blur-md text-white hover:bg-white/20">
            <svg x-show="!isOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
            <svg x-show="isOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Sidebar Content -->
        <div x-show="isOpen" @click.away="isOpen = false" class="lg:hidden fixed inset-0 bg-black/50 backdrop-blur-sm"></div>
        <div :class="{'translate-x-0': isOpen, '-translate-x-full': !isOpen}" class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-blue-500/20 to-blue-600/20 backdrop-blur-md border-r border-white/20 transform lg:translate-x-0 transition-transform duration-300">
            <div class="flex flex-col h-full">
                <div class="flex items-center justify-between p-4 border-b border-white/20">
                    <div class="flex items-center space-x-3">
                        <span class="text-xl font-bold text-white">X-Cool Event</span>
                    </div>
                </div>
                
                @if(auth()->user()->role === 'admin')
                <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center px-4 py-2 text-gray-100 hover:text-white hover:bg-white/20 rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-white/20 text-white' : '' }}">
                        <i class="fas fa-home w-5 h-5 mr-3"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-2 text-gray-100 hover:text-white hover:bg-white/20 rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-white/20 text-white' : '' }}">
                        <i class="fas fa-users w-5 h-5 mr-3"></i>
                        <span>Kelola User</span>
                    </a>

                    <a href="{{ route('admin.reports') }}" class="flex items-center px-4 py-2 text-gray-100 hover:text-white hover:bg-white/20 rounded-lg transition-colors {{ request()->routeIs('admin.reports') ? 'bg-white/20 text-white' : '' }}">
                        <i class="fas fa-chart-bar w-5 h-5 mr-3"></i>
                        <span>Laporan Event</span>
                    </a>
                </nav>
                @else
                <nav class="flex-1 p-4 space-y-2 overflow-y-auto">
                    <a href="{{ route('user.dashboard') }}" class="flex items-center px-4 py-2 text-gray-100 hover:text-white hover:bg-white/20 rounded-lg transition-colors {{ request()->routeIs('user.dashboard') ? 'bg-white/20 text-white' : '' }}">
                        <i class="fas fa-home w-5 h-5 mr-3"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('user.events.create') }}" class="flex items-center px-4 py-2 text-gray-100 hover:text-white hover:bg-white/20 rounded-lg transition-colors {{ request()->routeIs('user.events.create') ? 'bg-white/20 text-white' : '' }}">
                        <i class="fas fa-plus-circle w-5 h-5 mr-3"></i>
                        <span>Buat Event</span>
                    </a>

                    <a href="{{ route('user.reports') }}" class="flex items-center px-4 py-2 text-gray-100 hover:text-white hover:bg-white/20 rounded-lg transition-colors {{ request()->routeIs('user.reports') ? 'bg-white/20 text-white' : '' }}">
                        <i class="fas fa-chart-bar w-5 h-5 mr-3"></i>
                        <span>Laporan Event</span>
                    </a>
                </nav>
                @endif

                <div class="p-4 border-t border-white/20">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="flex items-center w-full px-4 py-2 text-gray-100 hover:text-white hover:bg-white/20 rounded-lg transition-colors">
                            <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>
@endauth 