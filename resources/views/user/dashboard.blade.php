@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Dashboard</h2>
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

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <div class="flex items-center space-x-4">
                <div class="rounded-full bg-blue-100 p-3">
                    <i class="fas fa-calendar-check text-xl text-blue-500"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Pengajuan</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $totalEvents }}</h3>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <div class="flex items-center space-x-4">
                <div class="rounded-full bg-yellow-100 p-3">
                    <i class="fas fa-clock text-xl text-yellow-500"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Menunggu Approval</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $pendingEvents }}</h3>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <div class="flex items-center space-x-4">
                <div class="rounded-full bg-green-100 p-3">
                    <i class="fas fa-check-circle text-xl text-green-500"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Event Disetujui</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $approvedEvents }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Events -->
    <div class="rounded-xl bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Event Terbaru</h3>
            <a href="{{ route('events.index') }}" class="text-blue-500 hover:text-blue-700">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nama Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($recentEvents as $event)
                    <tr>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $event->judul_event }}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($event->tanggal_pengajuan)->format('d M Y') }}</div>
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
                            <a href="{{ route('events.show', $event->event_id) }}" class="text-blue-600 hover:text-blue-900">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                            Belum ada event yang diajukan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection 