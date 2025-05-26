@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 max-w-6xl">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ url()->previous() }}" class="inline-flex items-center max-md:text-sm text-blue-600 hover:text-blue-800 transition-colors duration-200">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5 max-md:h-3 max-md:w-3 max-md:mt-0.5 max-md:ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            @if (url()->previous() == route('user.index'))
                Kembali ke Dasbor
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
                        <a href="{{ route('user.download-file', $eventPengajuan->event_id) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm max-md:text-xs font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
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
    
    <!-- Cancel Button -->
    @if($eventPengajuan->status === 'menunggu')
    <div class="flex justify-end">
        <button type="button" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200" 
            onclick="document.getElementById('cancel-modal').classList.remove('hidden')">
            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            Batalkan Pengajuan
        </button>
    </div>
    @endif
</div>

<!-- Cancel Modal -->
<div id="cancel-modal" class="fixed inset-0 !z-[200] overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Batalkan Pengajuan
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Apakah Anda yakin ingin membatalkan pengajuan kegiatan ini? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <form method="POST" action="{{ route('user.cancel', $eventPengajuan->event_id) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Ya, Batalkan
                    </button>
                </form>
                <button type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" 
                    onclick="document.getElementById('cancel-modal').classList.add('hidden')">
                    Tidak
                </button>
            </div>
        </div>
    </div>
</div>
@include('layouts.footer')
@endsection