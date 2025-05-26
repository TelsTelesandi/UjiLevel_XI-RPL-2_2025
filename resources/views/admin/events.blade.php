@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Kelola Event Ekstrakurikuler</h2>
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

    <!-- Search and Filter -->
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
            <select class="rounded-lg border border-gray-300 px-4 py-2 focus:border-blue-500 focus:outline-none">
                <option value="">Semua Ekskul</option>
                @foreach($uniqueEkskul as $ekskul)
                <option value="{{ $ekskul }}">{{ $ekskul }}</option>
                @endforeach
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
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Pengaju</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Ekskul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Tanggal Pengajuan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse($events as $event)
                    <tr>
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">{{ $event->judul_event }}</div>
                            <div class="text-xs text-gray-500">{{ $event->jenis_kegiatan }}</div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="flex items-center">
                                <div class="h-8 w-8 flex-shrink-0">
                                    <img class="h-8 w-8 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($event->user->nama_lengkap) }}" alt="{{ $event->user->nama_lengkap }}">
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $event->user->nama_lengkap }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <div class="text-sm text-gray-500">{{ $event->user->ekskul }}</div>
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
                            <div class="flex space-x-2">
                                <button class="text-blue-600 hover:text-blue-900" onclick="openDetailModal({{ $event->event_id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if($event->status == 'Menunggu')
                                <form action="{{ route('admin.events.approve', $event->event_id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="text-green-600 hover:text-green-900" onclick="return confirm('Apakah Anda yakin ingin menyetujui event ini?')">
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
                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                            Tidak ada data event
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

<!-- Event Detail Modal -->
<div id="detailModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-end justify-center px-4 pb-20 pt-4 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>
        <div class="inline-block transform overflow-hidden rounded-lg bg-white text-left align-bottom shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl sm:align-middle">
            <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mt-3 w-full text-center sm:ml-4 sm:mt-0 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                            Detail Event
                        </h3>
                        <div id="eventDetail" class="mt-4">
                            <!-- Detail event akan dimuat di sini -->
                            <div class="flex justify-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div>
                            </div>
                        </div>
                        
                        <!-- Form Komentar -->
                        <div id="commentForm" class="mt-6 hidden border-t pt-4">
                            <h4 class="font-medium text-gray-900">Tambahkan Komentar</h4>
                            <form id="addCommentForm" method="POST" action="" class="mt-2">
                                @csrf
                                <div>
                                    <textarea name="komentar" rows="3" class="mt-1 block w-full rounded-md border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-blue-500 sm:text-sm" placeholder="Tulis komentar atau catatan untuk event ini..." required></textarea>
                                </div>
                                <div class="mt-3 text-right">
                                    <button type="submit" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500">
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        Kirim Komentar
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Komentar yang ada -->
                        <div id="existingComment" class="mt-6 hidden border-t pt-4">
                            <h4 class="font-medium text-gray-900">Komentar Admin</h4>
                            <div id="commentContent" class="mt-2 rounded-lg bg-gray-50 p-3">
                                <!-- Isi komentar akan dimuat di sini -->
                            </div>
                            <div id="commentMeta" class="mt-1 text-right text-xs text-gray-500">
                                <!-- Info komentar akan dimuat di sini -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                <button type="button" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto" onclick="closeDetailModal()">
                    Tutup
                </button>
            </div>
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
function openDetailModal(eventId) {
    document.getElementById('detailModal').classList.remove('hidden');
    document.getElementById('eventDetail').innerHTML = '<div class="flex justify-center"><div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-blue-500"></div></div>';
    document.getElementById('commentForm').classList.add('hidden');
    document.getElementById('existingComment').classList.add('hidden');
    
    // Ambil detail event dari server
    fetch(`/admin/events/${eventId}/detail`)
        .then(response => response.json())
        .then(data => {
            let html = `
                <div class="space-y-4">
                    <div class="border-b pb-4">
                        <h4 class="text-xl font-semibold">${data.judul_event}</h4>
                        <p class="text-sm text-gray-500">${data.jenis_kegiatan}</p>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Pengaju</p>
                            <p class="font-medium">${data.user.nama_lengkap}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Ekstrakurikuler</p>
                            <p class="font-medium">${data.user.ekskul}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Tanggal Pengajuan</p>
                            <p class="font-medium">${data.tanggal_pengajuan_formatted}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Pembiayaan</p>
                            <p class="font-medium">Rp ${data.total_pembiayaan_formatted}</p>
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Deskripsi</p>
                        <div class="mt-1 rounded-lg bg-gray-50 p-3">
                            <p>${data.deskripsi}</p>
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-sm text-gray-500">Proposal</p>
                        <a href="/storage/proposals/${data.proposal}" target="_blank" class="mt-1 inline-flex items-center rounded-lg bg-blue-50 px-3 py-1 text-blue-700 hover:bg-blue-100">
                            <i class="fas fa-file-pdf mr-2"></i>
                            Lihat Proposal
                        </a>
                    </div>
                </div>
            `;
            
            if (data.status === 'Menunggu') {
                html += `
                    <div class="mt-6 flex space-x-4">
                        <form action="/admin/events/${data.event_id}/approve" method="POST" class="inline w-1/2">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="inline-flex w-full justify-center items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500">
                                <i class="fas fa-check w-4 mr-2 text-center"></i>
                                <span>Setujui</span>
                            </button>
                        </form>
                        <button type="button" class="inline-flex w-1/2 justify-center items-center rounded-md bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500" onclick="closeDetailModal(); openRejectModal(${data.event_id})">
                            <i class="fas fa-times w-4 mr-2 text-center"></i>
                            <span>Tolak</span>
                        </button>
                    </div>
                `;
            }
            
            document.getElementById('eventDetail').innerHTML = html;
            
            // Set up form komentar
            document.getElementById('addCommentForm').action = `/admin/events/${data.event_id}/comment`;
            document.getElementById('commentForm').classList.remove('hidden');
            
            // Tampilkan komentar jika ada
            if (data.verifikasi && data.verifikasi.komentar) {
                document.getElementById('commentContent').innerHTML = data.verifikasi.komentar;
                
                let commentDate = new Date(data.verifikasi.komentar_at);
                let formattedDate = commentDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' });
                
                document.getElementById('commentMeta').innerHTML = `
                    <span>Oleh: ${data.verifikasi.admin.nama_lengkap} - ${formattedDate}</span>
                `;
                
                document.getElementById('existingComment').classList.remove('hidden');
            }
            
            // Tambahkan tombol close jika belum ditutup
            if (data.status !== 'Closed') {
                let closeForm = document.createElement('form');
                closeForm.action = `/admin/events/${data.event_id}/close`;
                closeForm.method = 'POST';
                closeForm.className = 'mt-4';
                closeForm.innerHTML = `
                    @csrf
                    @method('PUT')
                    <button type="submit" class="inline-flex w-full justify-center items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500" onclick="return confirm('Apakah Anda yakin ingin menutup event ini? Event yang sudah ditutup tidak dapat dibuka kembali.')">
                        <i class="fas fa-times-circle w-4 mr-2 text-center"></i>
                        <span>Tutup Event</span>
                    </button>
                `;
                document.getElementById('commentForm').appendChild(closeForm);
            }
        })
        .catch(error => {
            document.getElementById('eventDetail').innerHTML = '<p class="text-red-500">Terjadi kesalahan saat memuat data.</p>';
            console.error('Error:', error);
        });
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.add('hidden');
}

function openRejectModal(eventId) {
    document.getElementById('rejectForm').action = `/admin/events/${eventId}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endsection 