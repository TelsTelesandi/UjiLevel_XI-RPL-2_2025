@extends('layouts.app')

@section('content')
<div class="p-4 md:p-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Dashboard Admin</h1>
            <p class="text-gray-400">Selamat datang, {{ auth()->user()->nama_lengkap }}</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-4 md:p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-500/20 rounded-lg">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Total Event</p>
                    <h3 class="text-2xl font-bold text-white">{{ $stats['total'] }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-4 md:p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-yellow-500/20 rounded-lg">
                    <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Menunggu</p>
                    <h3 class="text-2xl font-bold text-white">{{ $stats['menunggu'] }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-4 md:p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-500/20 rounded-lg">
                    <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Disetujui</p>
                    <h3 class="text-2xl font-bold text-white">{{ $stats['disetujui'] }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-4 md:p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-red-500/20 rounded-lg">
                    <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Ditolak</p>
                    <h3 class="text-2xl font-bold text-white">{{ $stats['ditolak'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Event List --}}
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-4 md:p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h2 class="text-xl font-semibold text-white">Daftar Event</h2>
            <div class="flex flex-wrap gap-2">
                <button type="button" class="btn-ghost text-sm" onclick="filterEvents('all')">Semua</button>
                <button type="button" class="btn-ghost text-sm" onclick="filterEvents('menunggu')">Menunggu</button>
                <button type="button" class="btn-ghost text-sm" onclick="filterEvents('disetujui')">Disetujui</button>
                <button type="button" class="btn-ghost text-sm" onclick="filterEvents('ditolak')">Ditolak</button>
            </div>
        </div>

        @if($events->isEmpty())
            <div class="text-center py-8">
                <svg class="w-16 h-16 text-gray-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-gray-400">Belum ada event yang diajukan</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-gray-400 border-b border-white/20">
                            <th class="pb-3 whitespace-nowrap">Judul Event</th>
                            <th class="pb-3 whitespace-nowrap">Tanggal</th>
                            <th class="pb-3 whitespace-nowrap">Status</th>
                            <th class="pb-3 whitespace-nowrap">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($events as $event)
                            <tr class="border-b border-white/10 event-row" data-status="{{ $event->status }}">
                                <td class="py-4">
                                    <div class="flex flex-col">
                                        <span class="font-medium text-white">{{ $event->judul_event }}</span>
                                        <span class="text-sm text-gray-400">{{ $event->user->ekskul }}</span>
                                    </div>
                                </td>
                                <td class="py-4 whitespace-nowrap text-white">{{ $event->tanggal_pengajuan ? $event->tanggal_pengajuan->format('d M Y') : '-' }}</td>
                                <td class="py-4">
                                    @if($event->status === 'menunggu')
                                        <span class="px-2 py-1 text-xs bg-yellow-500/20 text-yellow-400 rounded-full">Menunggu</span>
                                    @elseif($event->status === 'disetujui')
                                        <span class="px-2 py-1 text-xs bg-green-500/20 text-green-400 rounded-full">Disetujui</span>
                                    @else
                                        <span class="px-2 py-1 text-xs bg-red-500/20 text-red-400 rounded-full">Ditolak</span>
                                    @endif
                                </td>
                                <td class="py-4">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('admin.events.show', $event) }}" class="btn-ghost text-sm py-1">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($event->status === 'menunggu')
                                            <form action="{{ route('admin.events.approve', $event) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="btn-ghost text-sm py-1 text-green-400 hover:text-green-300">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.events.reject', $event) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="btn-ghost text-sm py-1 text-red-400 hover:text-red-300">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<script>
function filterEvents(status) {
    const rows = document.querySelectorAll('.event-row');
    rows.forEach(row => {
        if (status === 'all' || row.dataset.status === status) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
@endsection 