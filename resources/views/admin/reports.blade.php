@extends('layouts.app')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Laporan Event</h1>
            <p class="text-gray-400">Daftar event yang telah selesai dilaksanakan</p>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-500/20 rounded-lg">
                    <i class="fas fa-calendar-check text-blue-400 w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Total Event Selesai</p>
                    <h3 class="text-2xl font-bold text-white">{{ $stats['total_closed'] }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-500/20 rounded-lg">
                    <i class="fas fa-money-bill-wave text-green-400 w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Total Pembiayaan</p>
                    <h3 class="text-2xl font-bold text-white">Rp {{ number_format($stats['total_biaya'], 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
        
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 bg-purple-500/20 rounded-lg">
                    <i class="fas fa-users text-purple-400 w-6 h-6"></i>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Total Ekstrakurikuler</p>
                    <h3 class="text-2xl font-bold text-white">{{ $stats['total_ekskul'] }}</h3>
                </div>
            </div>
        </div>
    </div>

    {{-- Event List --}}
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-semibold text-white">Daftar Event Selesai</h2>
        </div>

        @if($events->isEmpty())
            <div class="text-center py-8">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-lg bg-blue-500/20 text-blue-400 mb-4">
                    <i class="fas fa-calendar-check text-3xl"></i>
                </div>
                <p class="text-gray-400">Belum ada event yang selesai</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-gray-400 border-b border-white/20">
                            <th class="pb-3">Judul Event</th>
                            <th class="pb-3">Ekstrakurikuler</th>
                            <th class="pb-3">Tanggal Selesai</th>
                            <th class="pb-3">Total Biaya</th>
                            <th class="pb-3">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/20">
                        @foreach($events as $event)
                            <tr class="text-white">
                                <td class="py-3">{{ $event->judul_event }}</td>
                                <td class="py-3">{{ $event->user->ekskul }}</td>
                                <td class="py-3">{{ $event->verifikasi->updated_at->format('d M Y') }}</td>
                                <td class="py-3">Rp {{ number_format($event->total_pembiayaan, 0, ',', '.') }}</td>
                                <td class="py-3">
                                    <a href="{{ route('admin.events.show', $event) }}" class="text-blue-400 hover:text-blue-300">
                                        <i class="fas fa-eye mr-1"></i>Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection 