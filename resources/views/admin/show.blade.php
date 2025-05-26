@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 max-w-6xl">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ url()->previous() }}" class="inline-flex items-center max-md:text-sm text-blue-600 hover:text-blue-800 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5 max-md:h-3 max-md:w-3 max-md:mt-0.5 max-md:ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            @if (url()->previous() == route('admin.index'))
                Kembali ke Daftar Pengajuan
            @else
                Kembali ke Riwayat Pengajuan
            @endif
        </a>
    </div>

    <!-- Main Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden mb-8 border border-gray-100">
        <!-- Card Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b flex flex-col sm:flex-row justify-between sm:items-center">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $eventPengajuan->judul_event }}</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Diajukan pada {{ \Carbon\Carbon::parse($eventPengajuan->tanggal_pengajuan)->isoFormat('dddd, D MMMM YYYY') }}
                </p>
            </div>
            <div class="mt-2 sm:mt-0">
                @switch($eventPengajuan->status)
                    @case('menunggu')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                            <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            Menunggu Verifikasi
                        </span>
                        @break
                    @case('disetujui')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 border border-green-200">
                            <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            Disetujui
                        </span>
                        @break
                    @case('ditolak')
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 border border-red-200">
                            <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                <circle cx="4" cy="4" r="3" />
                            </svg>
                            Ditolak
                        </span>
                        @break
                    @default
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                            {{ $eventPengajuan->status }}
                        </span>
                @endswitch
            </div>
        </div>
        
        <!-- Card Body -->
        <div class="p-6">
            <div class="space-y-8">
                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Pengaju -->
                    <div class="flex flex-col p-5 border rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                        <div class="flex items-center mb-2">
                            <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-500">Pengaju</span>
                        </div>
                        <div class="text-lg font-semibold text-gray-800">{{ $eventPengajuan->user->nama_lengkap }}</div>
                        <div class="text-sm text-gray-500">{{ $eventPengajuan->user->ekskul }}</div>
                    </div>
                    
                    <!-- Jenis Kegiatan -->
                    <div class="flex flex-col p-5 border rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                        <div class="flex items-center mb-2">
                            <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            <span class="text-sm font-medium text-gray-500">Jenis Kegiatan</span>
                        </div>
                        <div class="text-lg font-semibold text-gray-800">{{ $eventPengajuan->jenis_kegiatan }}</div>
                    </div>
                    
                    <!-- Total Pembiayaan -->
                    <div class="flex flex-col p-5 border rounded-xl bg-gray-50 hover:bg-gray-100 transition-colors duration-200">
                        <div class="flex items-center mb-2">
                            <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-medium text-gray-500">Total Pembiayaan</span>
                        </div>
                        <div class="text-lg font-semibold text-gray-800">Rp {{ number_format((int)$eventPengajuan->total_pembiayaan, 0, ',', '.') }}</div>
                    </div>
                </div>
                
                <!-- Deskripsi Kegiatan -->
                <div class="border-t pt-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Deskripsi Kegiatan
                    </h3>
                    <div class="prose max-w-none text-gray-600 bg-gray-50 p-4 pt-0 rounded-lg whitespace-pre-line">
                        {{ $eventPengajuan->deskripsi }}
                    </div>
                </div>
                
                <!-- File Proposal -->
                <div class="border-t pt-6">
                    <h3 class="text-xl font-semibold text-gray-800 mb-3 flex items-center">
                        <svg class="h-5 w-5 text-gray-500 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        File Proposal
                    </h3>
                    <div class="flex items-center justify-between bg-gray-50 p-4 rounded-lg">
                        <div class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 max-md:h-4 max-md:w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <div class="ml-3">
                                <p class="text-sm max-md:text-xs max-md:flex hidden font-medium text-gray-900">{{ \Illuminate\Support\Str::limit(basename($eventPengajuan->proposal), 10, '...') }}</p>
                                <p class="text-sm max-md:text-xs max-md:hidden flex font-medium text-gray-900">{{ basename($eventPengajuan->proposal) }}</p>
                            </div>
                        </div>
                        <a href="{{ route('admin.download-file', $eventPengajuan->event_id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm max-md:text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            Unduh
                        </a>
                    </div>
                </div>
                
                <!-- Catatan Verifikasi -->
                @if($eventPengajuan->verifikasiEvent)
                <div class="border-t pt-6">
                    <h3 class="text-xl font-semibold mb-3 flex items-center {{ $eventPengajuan->status === 'disetujui' ? 'text-green-700' : ($eventPengajuan->status === 'menunggu' ? 'text-orange-700' : 'text-red-700') }}">
                        @if ($eventPengajuan->status === 'disetujui')
                            <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @elseif ($eventPengajuan->status === 'menunggu')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-clock-icon lucide-clock h-5 w-5 mr-2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        @elseif ($eventPengajuan->status === 'ditolak')
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-x-icon lucide-circle-x h-5 w-5 mr-2"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>
                        @endif
                        Catatan Verifikasi
                    </h3>
                    <div class="p-4 rounded-lg {{ $eventPengajuan->status === 'disetujui' ? 'bg-green-50 border border-green-200' : ($eventPengajuan->status === 'menunggu' ? 'bg-orange-50 border border-orange-200' : 'bg-red-50 border border-red-200') }}">
                        <p class="{{ $eventPengajuan->status === 'disetujui' ? 'text-green-600' : ($eventPengajuan->status === 'menunggu' ? 'text-orange-600' : 'text-red-600') }} whitespace-pre-line">{{ $eventPengajuan->verifikasiEvent->catatan_admin ?: 'Tidak ada catatan.' }}</p>
                        <p class="text-sm {{ $eventPengajuan->status === 'disetujui' ? 'text-green-500' : ($eventPengajuan->status === 'menunggu' ? 'text-orange-600' : 'text-red-500') }} mt-3">
                            Diverifikasi pada: {{ $eventPengajuan->verifikasiEvent->tanggal_verifikasi ? \Carbon\Carbon::parse($eventPengajuan->verifikasiEvent->tanggal_verifikasi)->isoFormat('dddd, D MMMM YYYY [pukul] HH:mm') : 'Belum Diverifikasi' }}
                        </p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Approval/Rejection Forms -->
    @if($eventPengajuan->status === 'menunggu')
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Approve Form -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-green-100">
            <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b">
                <h2 class="text-xl font-semibold text-green-700 flex items-center">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Setujui Pengajuan
                </h2>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('admin.approve', $eventPengajuan->event_id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui pengajuan ini?')">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-4">
                        <label for="approve_catatan" class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                        <textarea id="approve_catatan" name="catatan_admin" rows="4" class="block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Tambahkan catatan untuk pengajuan ini..."></textarea>
                    </div>
                    
                    <button type="submit" class="w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
                        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Setujui Pengajuan
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Reject Form -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden border border-red-100">
            <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-rose-50 border-b">
                <h2 class="text-xl font-semibold text-red-700 flex items-center">
                    <svg class="h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    Tolak Pengajuan
                </h2>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('admin.reject', $eventPengajuan->event_id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menolak pengajuan ini?')">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-4">
                        <label for="reject_catatan" class="block text-sm font-medium text-gray-700 mb-2">Alasan Penolakan <span class="text-red-500">*</span></label>
                        <textarea id="reject_catatan" name="catatan_admin" rows="4" class="block w-full px-3 py-2 border rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('catatan_admin')border-red-300 @enderror" placeholder="Berikan alasan penolakan pengajuan ini..." required></textarea>
                        @error('catatan_admin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <button type="submit" class="w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200">
                        <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Tolak Pengajuan
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
@include('layouts.footer')
@endsection