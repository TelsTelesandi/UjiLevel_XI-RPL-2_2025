@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900">
    {{-- Sidebar --}}
    <aside class="fixed inset-y-0 left-0 w-64 bg-white/10 backdrop-blur-md border-r border-white/20">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-white/20">
            <img src="{{ asset('assets/logo.png') }}" alt="Logo" class="w-8 h-8 rounded-lg">
            <span class="text-lg font-bold text-white">X-Cool Event</span>
        </div>
        
        <nav class="p-4 space-y-2">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2 text-gray-300 hover:bg-white/10 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Dashboard
            </a>
            <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 px-4 py-2 text-white bg-white/20 rounded-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Laporan
            </a>
        </nav>
    </aside>

    {{-- Main Content --}}
    <main class="ml-64 p-8">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-bold text-white">Laporan Event</h1>
                    <p class="text-gray-400">Rekap event yang telah selesai dilaksanakan</p>
                </div>
                <button onclick="downloadReport()" class="btn-gradient">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Download Laporan
                    </div>
                </button>
            </div>

            {{-- Filter --}}
            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6 mb-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="ekskul" class="block text-white mb-2">Ekstrakurikuler</label>
                        <select id="ekskul" class="w-full bg-white/10 border border-white/20 text-white rounded px-4 py-2 focus:border-blue-500">
                            <option value="">Semua Ekstrakurikuler</option>
                            @foreach($completedEvents->pluck('user.ekskul')->unique() as $ekskul)
                                <option value="{{ $ekskul }}">{{ $ekskul }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="start_date" class="block text-white mb-2">Tanggal Mulai</label>
                        <input type="date" id="start_date" class="w-full bg-white/10 border border-white/20 text-white rounded px-4 py-2 focus:border-blue-500">
                    </div>
                    <div>
                        <label for="end_date" class="block text-white mb-2">Tanggal Akhir</label>
                        <input type="date" id="end_date" class="w-full bg-white/10 border border-white/20 text-white rounded px-4 py-2 focus:border-blue-500">
                    </div>
                </div>
            </div>

            {{-- Statistics --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Total Ekstrakurikuler</p>
                            <h3 class="text-2xl font-bold text-white">{{ $completedEvents->pluck('user.ekskul')->unique()->count() }}</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-green-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Total Event</p>
                            <h3 class="text-2xl font-bold text-white">{{ $completedEvents->count() }}</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-yellow-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Total Pembiayaan</p>
                            <h3 class="text-2xl font-bold text-white">Rp {{ number_format($completedEvents->sum('total_pembiayaan'), 0, ',', '.') }}</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-purple-500/20 rounded-lg">
                            <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-400 text-sm">Total Dokumentasi</p>
                            <h3 class="text-2xl font-bold text-white">{{ $completedEvents->flatMap->photos->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Event List --}}
            <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-gray-400 border-b border-white/20">
                                <th class="pb-3">Judul Event</th>
                                <th class="pb-3">Ekstrakurikuler</th>
                                <th class="pb-3">Tanggal</th>
                                <th class="pb-3">Pembiayaan</th>
                                <th class="pb-3">Dokumentasi</th>
                                <th class="pb-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/20">
                            @foreach($completedEvents as $event)
                                <tr class="text-white event-row" data-ekskul="{{ $event->user->ekskul }}" data-date="{{ $event->tanggal_pengajuan }}">
                                    <td class="py-3">{{ $event->judul_event }}</td>
                                    <td class="py-3">{{ $event->user->ekskul }}</td>
                                    <td class="py-3">{{ $event->tanggal_pengajuan->format('d M Y') }}</td>
                                    <td class="py-3">Rp {{ number_format($event->total_pembiayaan, 0, ',', '.') }}</td>
                                    <td class="py-3">{{ $event->photos->count() }} foto</td>
                                    <td class="py-3">
                                        <a href="{{ route('admin.events.show', $event) }}" class="text-blue-400 hover:text-blue-300">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
function filterEvents() {
    const ekskul = document.getElementById('ekskul').value;
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    document.querySelectorAll('.event-row').forEach(row => {
        let show = true;

        if (ekskul && row.dataset.ekskul !== ekskul) {
            show = false;
        }

        if (startDate && row.dataset.date < startDate) {
            show = false;
        }

        if (endDate && row.dataset.date > endDate) {
            show = false;
        }

        row.style.display = show ? '' : 'none';
    });
}

document.getElementById('ekskul').addEventListener('change', filterEvents);
document.getElementById('start_date').addEventListener('change', filterEvents);
document.getElementById('end_date').addEventListener('change', filterEvents);

function downloadReport() {
    // Implementasi download report (bisa menggunakan window.open ke endpoint yang menghasilkan file)
    alert('Fitur download laporan akan segera tersedia');
}
</script>
@endsection 