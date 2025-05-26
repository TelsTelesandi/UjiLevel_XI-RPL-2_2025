@extends('layouts.app')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <a href="{{ route('user.dashboard') }}" class="text-gray-400 hover:text-white mb-2 inline-flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                Kembali ke Dashboard
            </a>
            <h1 class="text-2xl font-bold text-white">Detail Event</h1>
        </div>
        @if($event->status === 'disetujui' && (!$event->verifikasi || $event->verifikasi->status !== 'closed'))
            <button onclick="showCloseModal()" class="btn-gradient">
                <i class="fas fa-check-circle mr-2"></i>Close Event
            </button>
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
                        @if($event->verifikasi)
                            <div class="mt-2 p-3 bg-red-500/20 border border-red-500/50 rounded-lg">
                                <p class="text-sm text-red-400">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Alasan Penolakan:
                                </p>
                                <p class="text-sm text-white mt-1">{{ $event->verifikasi->catatan_admin }}</p>
                            </div>
                        @endif
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
            <h2 class="text-xl font-semibold text-white mb-4">Deskripsi Event</h2>
            <p class="text-gray-300 whitespace-pre-line">{{ $event->deskripsi }}</p>
        </div>
    </div>

    {{-- Proposal --}}
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-white">Proposal</h2>
            <a href="{{ route('user.events.proposal', $event) }}" class="btn-ghost text-white" target="_blank">
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
                            $photoExists = Storage::disk('public')->exists($photo->photo_path);
                            $fullPath = Storage::disk('public')->path($photo->photo_path);
                        @endphp
                        <!-- Debug info -->
                        <div class="text-xs text-gray-400 mb-2">
                            Path: {{ $photo->photo_path }}<br>
                            URL: {{ $photoUrl }}<br>
                            Exists: {{ $photoExists ? 'Yes' : 'No' }}<br>
                            Full Path: {{ $fullPath }}
                        </div>
                        @if($photoExists)
                            <img src="{{ $photoUrl }}" alt="{{ $photo->photo_name }}" 
                                class="object-cover rounded-lg hover:opacity-75 transition-opacity cursor-pointer"
                                onclick="window.open('{{ $photoUrl }}', '_blank')"
                                onerror="this.onerror=null; this.src=''; this.alt='Error loading image';">
                        @else
                            <div class="bg-red-500/20 text-red-400 p-4 rounded-lg text-center">
                                File tidak ditemukan
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

{{-- Modal Close Event --}}
<div id="closeModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm hidden items-center justify-center">
    <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6 max-w-xl w-full mx-4">
        <h3 class="text-xl font-semibold text-white mb-4">Close Event</h3>
        <p class="text-gray-400 mb-4">Upload foto dokumentasi untuk menyelesaikan event ini.</p>
        
        <form id="closeForm" action="{{ route('user.events.close', $event) }}" method="POST" class="space-y-4" enctype="multipart/form-data">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2">
                    Foto Dokumentasi <span class="text-red-400">*</span>
                </label>
                <div class="flex items-center justify-center w-full">
                    <label for="photos" class="flex flex-col items-center justify-center w-full h-32 border-2 border-white/10 border-dashed rounded-lg cursor-pointer bg-white/5 hover:bg-white/10 transition-colors">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3"></i>
                            <p class="mb-2 text-sm text-gray-400">Klik untuk upload foto</p>
                            <p class="text-xs text-gray-500">JPG atau PNG (Maks. 5MB)</p>
                        </div>
                        <input id="photos" name="photos[]" type="file" class="hidden" accept="image/*" multiple required />
                    </label>
                </div>
                <div id="preview" class="grid grid-cols-2 gap-4 mt-4"></div>
            </div>

            <div class="flex justify-end gap-4 mt-6">
                <button type="button" onclick="hideCloseModal()" class="btn-ghost">
                    Batal
                </button>
                <button type="submit" class="btn-gradient">
                    <i class="fas fa-check-circle mr-2"></i>Selesaikan Event
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function showCloseModal() {
    document.getElementById('closeModal').classList.remove('hidden');
    document.getElementById('closeModal').classList.add('flex');
}

function hideCloseModal() {
    document.getElementById('closeModal').classList.add('hidden');
    document.getElementById('closeModal').classList.remove('flex');
    document.getElementById('preview').innerHTML = '';
    document.getElementById('photos').value = '';
}

// Close modal when clicking outside
document.getElementById('closeModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideCloseModal();
    }
});

// Preview images
document.getElementById('photos').addEventListener('change', function(e) {
    const preview = document.getElementById('preview');
    preview.innerHTML = '';
    
    [...e.target.files].forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const div = document.createElement('div');
            div.className = 'aspect-w-16 aspect-h-9';
            div.innerHTML = `
                <img src="${e.target.result}" class="object-cover rounded-lg">
            `;
            preview.appendChild(div);
        }
        reader.readAsDataURL(file);
    });
});

document.getElementById('closeForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const form = e.target;
    const formData = new FormData(form);
    
    try {
        const response = await fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const result = await response.json();
        
        if (result.success) {
            hideCloseModal();
            window.location.href = '{{ route("user.reports") }}';
        } else {
            alert(result.error || 'Terjadi kesalahan saat menutup event');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat mengirim data');
    }
});
</script>
@endsection 