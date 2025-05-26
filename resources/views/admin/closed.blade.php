@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header Section -->
    <form method="GET" action="{{ route('admin.riwayat') }}" class="mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="w-full md:w-auto">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Kelola Pengajuan Acara Kegiatan</h1>
                <p class="text-gray-600 mt-1">Kelola semua riwayat pengajuan kegiatan dari berbagai ekstrakurikuler</p>
            </div>
            
            <div class="w-full md:w-auto">
                <div class="flex items-center">
                    <div class="relative flex-grow">
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="Cari pengajuan..." 
                            class="block w-full px-4 py-2.5 max-md:text-sm rounded-l-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200"
                        >
                    </div>
                    
                    <button 
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 max-md:text-xs text-white font-medium py-2.5 px-6 rounded-r-lg transition duration-200 flex items-center"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 max-md:mr-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <p class="max-md:hidden">Cari</p>
                    </button>
                    
                    @if(request()->has('search'))
                    <a 
                        href="{{ route('admin.riwayat') }}" 
                        class="ml-2 text-gray-500 hover:text-gray-700 transition duration-200 flex items-center"
                        title="Reset pencarian"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-1 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </form>

    @if(session('success'))
    <div id="flash-message" class="mb-8 p-4 rounded-lg bg-green-100 border-l-4 border-green-500 shadow-sm transition-opacity duration-500">
        <div class="flex items-center">
            <svg class="h-6 w-6 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="ml-3">
                <p class="text-sm md:text-base font-medium text-green-800">
                    {{ session('success') }}
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <!-- Card Header -->
        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div class="mb-3 md:mb-0">
                    <h2 class="text-lg font-semibold text-gray-800">Daftar Riwayat Pengajuan Kegiatan</h2>
                    <p class="text-sm text-gray-500 mt-1">{{ count($eventPengajuans) }} riwayat pengajuan (closed) ditemukan</p>
                </div>

                <div class="mt-3 md:mt-0 max-md:w-full">
                    <a href="{{ route('admin.riwayat.download') }}" class="inline-flex max-md:w-full items-center justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export PDF
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Empty State -->
        @if(count($eventPengajuans) === 0)
        <div class="p-8 text-center">
            <div class="max-w-md mx-auto p-6 rounded-xl bg-blue-50 border border-blue-100">
                <svg class="h-12 w-12 mx-auto text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-3 text-lg font-medium text-blue-800">Tidak ada riwayat pengajuan</h3>
                <p class="mt-2 text-sm text-blue-700">
                    Saat ini belum ada riwayat pengajuan kegiatan yang berstatus tutup. Silakan periksa kembali nanti.
                </p>
            </div>
        </div>
        @else
        <!-- Table Section -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Kegiatan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengaju</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Pengajuan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Verifikasi</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pengajuan</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($eventPengajuans as $eventPengajuan)
                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-md bg-blue-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $eventPengajuan->judul_event }}</div>
                                    <div class="text-sm text-gray-500">Rp {{ number_format((int)$eventPengajuan->total_pembiayaan, 0, ',', '.') }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-purple-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $eventPengajuan->user->nama_lengkap }}</div>
                                    <div class="text-sm text-gray-500">{{ $eventPengajuan->user->ekskul }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                <span class="px-2 py-1 bg-gray-100 rounded-full text-xs">{{ $eventPengajuan->jenis_kegiatan }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($eventPengajuan->tanggal_pengajuan)->format('d M Y') }}</div>
                            <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($eventPengajuan->tanggal_pengajuan)->diffForHumans() }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @switch($eventPengajuan->status)
                                @case('menunggu')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        Menunggu
                                    </span>
                                    @break
                                @case('disetujui')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        Disetujui
                                    </span>
                                    @break
                                @case('ditolak')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        Ditolak
                                    </span>
                                    @break
                                @default
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        {{ $eventPengajuan->status }}
                                    </span>
                            @endswitch
                        </td>
                        <td class="px-6 py-4">
                            @switch($eventPengajuan->verifikasiEvent->status)
                                @case('unclosed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        Unclosed
                                    </span>
                                    @break
                                @case('closed')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        Closed
                                    </span>
                                    @break
                                @default
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        {{ $eventPengajuan->verifikasiEvent->status }}
                                    </span>
                            @endswitch
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.show', $eventPengajuan->event_id) }}" 
                                   class="text-blue-600 hover:text-blue-900 transition-colors duration-200 flex items-center">
                                    <svg class="h-4 w-4 mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Detail
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50 overflow-x-auto">
            {{ $eventPengajuans->links('pagination::tailwind') }}
        </div>
        @endif
    </div>
    @include('layouts.footer')
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        const tableRows = document.querySelectorAll('tbody tr');
        
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Auto-hide flash message after 5 seconds
        const flashMessage = document.getElementById('flash-message');
        if (flashMessage) {
            setTimeout(() => {
                flashMessage.style.opacity = '0';
                setTimeout(() => flashMessage.remove(), 500);
            }, 5000);
        }
    });
</script>
@endsection