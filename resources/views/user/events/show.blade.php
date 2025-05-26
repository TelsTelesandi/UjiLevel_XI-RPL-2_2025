@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-4xl space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Detail Event</h2>
        <a href="{{ route('events.index') }}" class="flex items-center text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="rounded-lg bg-green-100 p-4 text-green-700">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">{{ session('success') }}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button type="button" class="inline-flex rounded-md bg-green-100 p-1.5 text-green-500 hover:bg-green-200" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                        <span class="sr-only">Dismiss</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="rounded-lg bg-red-100 p-4 text-red-700">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium">{{ session('error') }}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button type="button" class="inline-flex rounded-md bg-red-100 p-1.5 text-red-500 hover:bg-red-200" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                        <span class="sr-only">Dismiss</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Event Status Banner -->
    <div class="rounded-xl p-4 shadow-sm 
        @if($event->status == 'Disetujui') bg-green-50 border border-green-200
        @elseif($event->status == 'Ditolak') bg-red-50 border border-red-200
        @elseif($event->status == 'Closed') bg-gray-50 border border-gray-200
        @else bg-yellow-50 border border-yellow-200 @endif">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="rounded-full p-2 
                    @if($event->status == 'Disetujui') bg-green-100 text-green-500
                    @elseif($event->status == 'Ditolak') bg-red-100 text-red-500
                    @elseif($event->status == 'Closed') bg-gray-100 text-gray-500
                    @else bg-yellow-100 text-yellow-500 @endif">
                    <i class="fas 
                        @if($event->status == 'Disetujui') fa-check-circle
                        @elseif($event->status == 'Ditolak') fa-times-circle
                        @elseif($event->status == 'Closed') fa-check-double
                        @else fa-clock @endif text-lg"></i>
                </div>
                <div>
                    <h3 class="text-lg font-medium 
                        @if($event->status == 'Disetujui') text-green-800
                        @elseif($event->status == 'Ditolak') text-red-800
                        @elseif($event->status == 'Closed') text-gray-800
                        @else text-yellow-800 @endif">
                        Status: {{ $event->status }}
                    </h3>
                    <p class="text-sm 
                        @if($event->status == 'Disetujui') text-green-600
                        @elseif($event->status == 'Ditolak') text-red-600
                        @elseif($event->status == 'Closed') text-gray-600
                        @else text-yellow-600 @endif">
                        Tanggal pengajuan: {{ \Carbon\Carbon::parse($event->tanggal_pengajuan)->format('d M Y') }}
                    </p>
                </div>
            </div>
            @if($event->status != 'Closed')
            <form action="{{ route('events.close', $event->event_id) }}" method="POST" class="inline">
                @csrf
                @method('PUT')
                <button type="submit" class="rounded-lg bg-blue-500 px-4 py-2 text-white hover:bg-blue-600" onclick="return confirm('Apakah Anda yakin ingin menutup event ini? Event yang sudah ditutup tidak dapat dibuka kembali.')">
                    <i class="fas fa-times-circle mr-2"></i>
                    Tutup Event
                </button>
            </form>
            @endif
        </div>
    </div>

    <!-- Event Details Card -->
    <div class="rounded-xl bg-white p-6 shadow-sm">
        <div class="mb-6 border-b pb-6">
            <h3 class="mb-1 text-xl font-bold text-gray-800">{{ $event->judul_event }}</h3>
            <p class="text-sm text-gray-500">{{ $event->jenis_kegiatan }}</p>
        </div>
        
        <div class="mb-6 grid grid-cols-1 gap-6 md:grid-cols-2">
            <div>
                <h4 class="mb-2 text-sm font-medium text-gray-500">Total Pembiayaan</h4>
                <p class="text-lg font-semibold text-gray-800">Rp {{ number_format($event->total_pembiayaan, 0, ',', '.') }}</p>
            </div>
        </div>
        
        <div class="mb-6">
            <h4 class="mb-2 text-sm font-medium text-gray-500">Deskripsi Event</h4>
            <div class="rounded-lg bg-gray-50 p-4 text-gray-700">
                <p>{{ $event->deskripsi }}</p>
            </div>
        </div>
        
        <div class="mb-6">
            <h4 class="mb-2 text-sm font-medium text-gray-500">Dokumen Proposal</h4>
            <a href="{{ asset('storage/proposals/' . $event->proposal) }}" target="_blank" class="inline-flex items-center rounded-lg bg-blue-50 px-4 py-2 text-blue-700 hover:bg-blue-100">
                <i class="fas fa-file-pdf mr-2"></i>
                {{ $event->proposal }}
            </a>
        </div>
        
        @if($event->status == 'Ditolak' && isset($event->verifikasi) && $event->verifikasi->catatan_admin)
        <div class="rounded-lg border border-red-200 bg-red-50 p-4">
            <h4 class="mb-2 text-sm font-medium text-red-800">Catatan Penolakan</h4>
            <p class="text-red-700">{{ $event->verifikasi->catatan_admin }}</p>
        </div>
        @endif
    </div>
</div>
@endsection 