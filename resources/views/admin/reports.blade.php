@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Laporan Event</h2>
        <div class="flex items-center space-x-2">
            <button type="button" onclick="openFilterModal()" class="rounded-lg bg-blue-500 px-4 py-2 text-white hover:bg-blue-600">
                <i class="fas fa-filter mr-2"></i>
                Filter & Export
            </button>
        </div>
    </div>

    <!-- Flash Message -->
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

    <!-- Error Message -->
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
            </select>
            <select class="rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none">
                <option value="">Semua Periode</option>
                <option value="7">7 Hari Terakhir</option>
                <option value="30">30 Hari Terakhir</option>
                <option value="90">90 Hari Terakhir</option>
            </select>
        </div>
    </div>

    <!-- Report Summary -->
    <div class="grid grid-cols-1 gap-6 md:grid-cols-4">
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <div class="flex items-center space-x-4">
                <div class="rounded-full bg-blue-100 p-3">
                    <i class="fas fa-calendar-alt text-xl text-blue-500"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Total Event</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ count($events) }}</h3>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <div class="flex items-center space-x-4">
                <div class="rounded-full bg-green-100 p-3">
                    <i class="fas fa-check-circle text-xl text-green-500"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Disetujui</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $events->where('status', 'Disetujui')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <div class="flex items-center space-x-4">
                <div class="rounded-full bg-red-100 p-3">
                    <i class="fas fa-times-circle text-xl text-red-500"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Ditolak</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $events->where('status', 'Ditolak')->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="rounded-xl bg-white p-6 shadow-sm">
            <div class="flex items-center space-x-4">
                <div class="rounded-full bg-yellow-100 p-3">
                    <i class="fas fa-clock text-xl text-yellow-500"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Menunggu</p>
                    <h3 class="text-2xl font-bold text-gray-800">{{ $events->where('status', 'Menunggu')->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Events Table -->
    <div class="rounded-xl bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Judul Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Pengaju</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Jenis Kegiatan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tanggal Pengajuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Biaya</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Verifikator</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($events as $event)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $event->judul_event }}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-8 w-8 flex-shrink-0">
                                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($event->user->nama_lengkap) }}" alt="{{ $event->user->nama_lengkap }}">
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $event->user->nama_lengkap }}</div>
                                    <div class="text-xs text-gray-500">{{ $event->user->ekskul }}</div>
                                </div>
                            </div>
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
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ $event->status }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            @if(isset($event->verifikasi) && isset($event->verifikasi->admin))
                                <div class="text-sm text-gray-500">{{ $event->verifikasi->admin->nama_lengkap }}</div>
                                @if($event->verifikasi->tanggal_verifikasi)
                                    <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($event->verifikasi->tanggal_verifikasi)->format('d M Y') }}</div>
                                @endif
                            @else
                                <div class="text-sm text-gray-400">-</div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data event
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div id="filterModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
        <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle">
            <form id="exportForm" method="POST" action="{{ route('admin.reports.export') }}">
                @csrf
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 w-full text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                                Filter & Export Laporan
                            </h3>
                            <div class="mt-4 space-y-4">
                                <!-- Filter Status -->
                                <div>
                                    <label for="status" class="block text-sm font-medium text-gray-700">Status Event</label>
                                    <select id="status" name="status" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm">
                                        <option value="all">Semua Status</option>
                                        <option value="Menunggu">Menunggu</option>
                                        <option value="Disetujui">Disetujui</option>
                                        <option value="Ditolak">Ditolak</option>
                                        <option value="Closed">Closed</option>
                                    </select>
                                </div>
                                
                                <!-- Filter Periode -->
                                <div>
                                    <label for="period" class="block text-sm font-medium text-gray-700">Periode</label>
                                    <select id="period" name="period" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm" onchange="toggleCustomDate()">
                                        <option value="all">Semua Waktu</option>
                                        <option value="7">7 Hari Terakhir</option>
                                        <option value="30">30 Hari Terakhir</option>
                                        <option value="90">90 Hari Terakhir</option>
                                        <option value="custom">Kustom</option>
                                    </select>
                                </div>
                                
                                <!-- Custom Date Range (tersembunyi secara default) -->
                                <div id="customDateRange" class="hidden space-y-4">
                                    <div>
                                        <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                                        <input type="date" id="start_date" name="start_date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                                        <input type="date" id="end_date" name="end_date" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm">
                                    </div>
                                </div>
                                
                                <!-- Filter Ekskul -->
                                <div>
                                    <label for="ekskul" class="block text-sm font-medium text-gray-700">Ekstrakurikuler</label>
                                    <select id="ekskul" name="ekskul" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm">
                                        <option value="all">Semua Ekskul</option>
                                        @foreach($uniqueEkskul as $ekskul)
                                        <option value="{{ $ekskul }}">{{ $ekskul }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                    <button type="submit" class="inline-flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 sm:ml-3 sm:w-auto">
                        <i class="fas fa-download mr-2"></i>
                        Download PDF
                    </button>
                    <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" onclick="closeFilterModal()">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openFilterModal() {
    document.getElementById('filterModal').classList.remove('hidden');
}

function closeFilterModal() {
    document.getElementById('filterModal').classList.add('hidden');
}

function toggleCustomDate() {
    var period = document.getElementById('period').value;
    var customDateRange = document.getElementById('customDateRange');
    
    if (period === 'custom') {
        customDateRange.classList.remove('hidden');
    } else {
        customDateRange.classList.add('hidden');
    }
}

// Set default dates untuk custom range
document.addEventListener('DOMContentLoaded', function() {
    var today = new Date();
    var thirtyDaysAgo = new Date();
    thirtyDaysAgo.setDate(today.getDate() - 30);
    
    var endDateInput = document.getElementById('end_date');
    var startDateInput = document.getElementById('start_date');
    
    if (endDateInput && startDateInput) {
        endDateInput.valueAsDate = today;
        startDateInput.valueAsDate = thirtyDaysAgo;
    }
});
</script>
@endsection 