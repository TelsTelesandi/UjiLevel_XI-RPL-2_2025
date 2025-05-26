@extends('layouts.app')

@section('content')
<div class="p-8">
    {{-- Header --}}
    <div class="flex justify-between items-center mb-8">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="text-gray-400 hover:text-white mb-2 inline-flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Dashboard
            </a>
            <h1 class="text-2xl font-bold text-white">Detail Event</h1>
        </div>

        @if($event->status === 'menunggu')
            <div class="flex gap-3">
                <button onclick="rejectEvent({{ $event->event_id }})" class="btn-ghost text-red-400 hover:text-red-300">
                    <i class="fas fa-times-circle mr-2"></i>Tolak Event
                </button>
                <button onclick="approveEvent({{ $event->event_id }})" class="btn-gradient">
                    <i class="fas fa-check-circle mr-2"></i>Setujui Event
                </button>
            </div>
        @endif
    </div>

    {{-- Event Details --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-white mb-4">Informasi Event</h2>
            <div class="space-y-4">
                <div>
                    <p class="text-gray-400 text-sm">Status Event</p>
                    @if($event->status === 'menunggu')
                        <span class="px-2 py-1 text-xs bg-yellow-500/20 text-yellow-400 rounded-full">Menunggu</span>
                    @elseif($event->status === 'disetujui')
                        @if($event->verifikasi?->status === 'closed')
                            <span class="px-2 py-1 text-xs bg-green-500/20 text-green-400 rounded-full">Selesai</span>
                        @else
                            <span class="px-2 py-1 text-xs bg-green-500/20 text-green-400 rounded-full">Disetujui</span>
                        @endif
                    @else
                        <span class="px-2 py-1 text-xs bg-red-500/20 text-red-400 rounded-full">Ditolak</span>
                    @endif
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Judul Event</p>
                    <p class="text-white">{{ $event->judul_event }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Jenis Kegiatan</p>
                    <p class="text-white">{{ $event->jenis_kegiatan }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Total Pembiayaan</p>
                    <p class="text-white">Rp {{ number_format($event->total_pembiayaan, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Tanggal Pengajuan</p>
                    <p class="text-white">{{ $event->tanggal_pengajuan->format('d M Y') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-white mb-4">Informasi Pengaju</h2>
            <div class="space-y-4">
                <div>
                    <p class="text-gray-400 text-sm">Nama Lengkap</p>
                    <p class="text-white">{{ $event->user->nama_lengkap }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Ekstrakurikuler</p>
                    <p class="text-white">{{ $event->user->ekskul }}</p>
                </div>
                <div>
                    <p class="text-gray-400 text-sm">Username</p>
                    <p class="text-white">{{ $event->user->username }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Deskripsi --}}
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6 mb-8">
        <h2 class="text-xl font-semibold text-white mb-4">Deskripsi Event</h2>
        <p class="text-gray-300 whitespace-pre-line">{{ $event->deskripsi }}</p>
    </div>

    {{-- Proposal --}}
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-white">Proposal</h2>
            <a href="{{ route('admin.events.proposal', $event) }}" class="btn-ghost text-white" target="_blank">
                <i class="fas fa-download mr-2"></i>Download Proposal
            </a>
        </div>
    </div>

    {{-- Dokumentasi --}}
    @if($event->photos->isNotEmpty())
        <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-white mb-4">Dokumentasi Event</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach($event->photos as $photo)
                    <div class="aspect-w-16 aspect-h-9">
                        @php
                            $photoUrl = asset('storage/' . $photo->photo_path);
                        @endphp
                        <!-- Debug info -->
                        <div class="text-xs text-gray-400 mb-2">
                            Path: {{ $photo->photo_path }}<br>
                            URL: {{ $photoUrl }}
                        </div>
                        <img src="{{ $photoUrl }}" alt="{{ $photo->photo_name }}" 
                            class="object-cover rounded-lg hover:opacity-75 transition-opacity cursor-pointer"
                            onclick="window.open('{{ $photoUrl }}', '_blank')">
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

{{-- Modal Reject Event --}}
<div id="rejectModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center">
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6 max-w-xl w-full mx-4">
        <h3 class="text-xl font-semibold text-white mb-4">Tolak Event</h3>
        <p class="text-gray-400 mb-4">Berikan alasan penolakan event ini.</p>
        
        <form id="rejectForm" class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    Alasan Penolakan <span class="text-red-400">*</span>
                </label>
                <textarea id="rejectReason" rows="3" class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2 text-white placeholder-gray-400 focus:outline-none focus:border-blue-500" required></textarea>
            </div>

            <div class="flex justify-end gap-4 mt-6">
                <button type="button" onclick="hideRejectModal()" class="btn-ghost">
                    Batal
                </button>
                <button type="submit" class="btn-gradient">
                    <i class="fas fa-times-circle mr-2"></i>Tolak Event
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showRejectModal() {
    document.getElementById('rejectModal').classList.remove('hidden');
    document.getElementById('rejectModal').classList.add('flex');
}

function hideRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
    document.getElementById('rejectModal').classList.remove('flex');
}

async function approveEvent(eventId) {
    if (!confirm('Apakah Anda yakin ingin menyetujui event ini?')) return;

    try {
        const response = await fetch(`/admin/events/${eventId}/approve`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        if (response.ok) {
            window.location.reload();
        } else {
            alert('Gagal menyetujui event');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    }
}

function rejectEvent(eventId) {
    showRejectModal();
    
    document.getElementById('rejectForm').onsubmit = async function(e) {
        e.preventDefault();
        
        const reason = document.getElementById('rejectReason').value;
        if (!reason) return;

        try {
            const response = await fetch(`/admin/events/${eventId}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ reason })
            });

            if (response.ok) {
                window.location.reload();
            } else {
                alert('Gagal menolak event');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        }
    };
}

// Close modal when clicking outside
document.getElementById('rejectModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideRejectModal();
    }
});
</script>
@endsection 