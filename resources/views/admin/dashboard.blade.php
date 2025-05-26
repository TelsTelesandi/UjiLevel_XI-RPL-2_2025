@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Dashboard Admin</h2>
        <div class="flex space-x-4">
            <a href="{{ route('admin.reports') }}" class="rounded-lg bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">
                <i class="fas fa-download mr-2"></i>
                Lihat Laporan
            </a>
            <a href="{{ route('admin.users') }}" class="rounded-lg bg-green-500 px-4 py-2 text-white hover:bg-green-600">
                <i class="fas fa-user-plus mr-2"></i>
                Manajemen User
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <div class="flex items-center space-x-4">
                <div class="rounded-full bg-blue-100 p-3">
                    <i class="fas fa-users text-xl text-blue-500"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total User</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $totalUsers }}</h3>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <div class="flex items-center space-x-4">
                <div class="rounded-full bg-purple-100 p-3">
                    <i class="fas fa-calendar-alt text-xl text-purple-500"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Event</p>
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
                    <p class="text-sm text-gray-500">Pending</p>
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
                    <p class="text-sm text-gray-500">Approved</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $approvedEvents }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Events Table -->
    <div class="rounded-xl bg-white p-6 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Pengajuan Event Terbaru</h3>
            <a href="{{ route('admin.events') }}" class="text-blue-500 hover:text-blue-700">
                Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Nama Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Pengaju</th>
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
                            <div class="text-sm text-gray-500">{{ $event->user->nama_lengkap }}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($event->tanggal_pengajuan)->format('d M Y') }}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold 
                                @if($event->status == 'Disetujui') bg-green-100 text-green-800
                                @elseif($event->status == 'Ditolak') bg-red-100 text-red-800
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ $event->status }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.events') }}" class="text-blue-600 hover:text-blue-900">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($event->status == 'Menunggu')
                                <form action="{{ route('admin.events.approve', $event->event_id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="text-green-600 hover:text-green-900">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <button class="text-red-600 hover:text-red-900" onclick="openRejectModal({{ $event->event_id }})">
                                    <i class="fas fa-times"></i>
                                </button>
                                @endif
                                @if($event->status != 'Closed')
                                <form action="{{ route('admin.events.close', $event->event_id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menutup event ini? Event yang sudah ditutup tidak dapat dibuka kembali.')">
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
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data event terbaru
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
        <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
            <form id="rejectForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-times text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                Tolak Event
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Apakah Anda yakin ingin menolak event ini? Berikan alasan penolakan.
                                </p>
                                <div class="mt-4">
                                    <label for="catatan_admin" class="block text-sm font-medium text-gray-700">Catatan Penolakan</label>
                                    <textarea id="catatan_admin" name="catatan_admin" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-red-500 focus:outline-none focus:ring-red-500 sm:text-sm" required></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="submit" class="inline-flex w-full justify-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto">
                        Tolak
                    </button>
                    <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" onclick="closeRejectModal()">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openRejectModal(eventId) {
    document.getElementById('rejectForm').action = `/admin/events/${eventId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endsection 