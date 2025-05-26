@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Daftar Event Saya</h2>
        <a href="{{ route('events.create') }}" class="rounded-lg bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">
            <i class="fas fa-plus mr-2"></i>
            Ajukan Event Baru
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

    <!-- Filter -->
    <div class="flex items-center justify-between rounded-xl bg-white p-4 shadow-sm">
        <div class="relative flex-1 max-w-xs">
            <input type="text" placeholder="Cari event..." class="w-full rounded-lg border border-gray-300 pl-10 pr-4 py-2 focus:border-blue-500 focus:outline-none">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
        </div>
        <div class="flex items-center space-x-4">
            <select class="rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none">
                <option value="">Semua Status</option>
                <option value="Menunggu">Menunggu</option>
                <option value="Disetujui">Disetujui</option>
                <option value="Ditolak">Ditolak</option>
                <option value="Closed">Selesai</option>
            </select>
        </div>
    </div>

    <!-- Events Table -->
    <div class="rounded-xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Judul Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Jenis Kegiatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tanggal Pengajuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Total Biaya</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($events as $event)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $event->judul_event }}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm text-gray-500">{{ $event->jenis_kegiatan }}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($event->tanggal_pengajuan)->format('d M Y') }}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm text-gray-500">Rp {{ number_format($event->total_pembiayaan, 0, ',', '.') }}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold 
                                @if($event->status == 'Disetujui') bg-green-100 text-green-800
                                @elseif($event->status == 'Ditolak') bg-red-100 text-red-800
                                @elseif($event->status == 'Closed') bg-gray-100 text-gray-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ $event->status }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <div class="flex space-x-2">
                                <a href="{{ route('events.show', $event->event_id) }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($event->status != 'Closed')
                                <form action="{{ route('events.close', $event->event_id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menutup event ini? Event yang sudah ditutup tidak dapat dibuka kembali.')">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="text-gray-600 hover:text-gray-900" title="Tutup Event">
                                        <i class="fas fa-times-circle"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Belum ada event yang diajukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4">
            {{ $events->links() }}
        </div>
    </div>
</div>
@endsection 