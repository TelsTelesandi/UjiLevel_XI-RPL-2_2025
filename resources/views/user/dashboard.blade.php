@extends('layouts.app')

@section('content')
<div class="p-4 md:p-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Dashboard {{ auth()->user()->ekskul }}</h1>
            <p class="text-gray-400">Selamat datang, {{ auth()->user()->nama_lengkap }}</p>
        </div>
        <a href="{{ route('user.events.create') }}" class="btn-gradient">
            <i class="fas fa-plus-circle mr-2"></i>Buat Event
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-8">
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-4 md:p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-500/20 rounded-lg">
                    <i class="fas fa-calendar-alt text-blue-400 w-6 h-6"></i>
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
                    <i class="fas fa-clock text-yellow-400 w-6 h-6"></i>
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
                    <i class="fas fa-check-circle text-green-400 w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Event Disetujui</p>
                    <h3 class="text-2xl font-bold text-white">{{ $stats['disetujui'] }}</h3>
                </div>
            </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-4 md:p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-red-500/20 rounded-lg">
                    <i class="fas fa-times-circle text-red-400 w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Event Ditolak</p>
                    <h3 class="text-2xl font-bold text-white">{{ $stats['ditolak'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Event List --}}
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-4 md:p-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h2 class="text-xl font-semibold text-white">Riwayat Event</h2>
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
                                        @if($event->status === 'ditolak' && $event->verifikasi)
                                            <span class="text-sm text-red-400">
                                                <i class="fas fa-info-circle mr-1"></i>
                                                {{ $event->verifikasi->catatan_admin }}
                                            </span>
                                        @endif
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
                                        <a href="{{ route('user.events.show', $event) }}" class="btn-ghost text-sm py-1">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($event->status === 'menunggu')
                                            <a href="{{ route('user.events.edit', $event) }}" class="btn-ghost text-sm py-1 text-blue-400 hover:text-blue-300">
                                                <i class="fas fa-edit"></i>
                                            </a>
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

@push('scripts')
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
@endpush

@endsection 