@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Pengajuan Event Baru</h2>
        <a href="{{ route('dashboard') }}" class="flex items-center text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>

    <!-- Error Message -->
    @if ($errors->any())
    <div class="rounded-lg bg-red-50 p-4 text-red-700">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Ada beberapa kesalahan pada pengajuan Anda:</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Form Card -->
    <div class="rounded-xl bg-white p-6 shadow-sm">
        <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <!-- Judul Event -->
            <div>
                <label for="judul_event" class="mb-2 block text-sm font-medium text-gray-700">Judul Event</label>
                <input type="text" name="judul_event" id="judul_event" value="{{ old('judul_event') }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('judul_event') border-red-500 @enderror" required>
                @error('judul_event')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Jenis Kegiatan -->
            <div>
                <label for="jenis_kegiatan" class="mb-2 block text-sm font-medium text-gray-700">Jenis Kegiatan</label>
                <select name="jenis_kegiatan" id="jenis_kegiatan" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('jenis_kegiatan') border-red-500 @enderror" required>
                    <option value="">Pilih Jenis Kegiatan</option>
                    <option value="Penampilan Musik" {{ old('jenis_kegiatan') == 'Penampilan Musik' ? 'selected' : '' }}>Penampilan Musik</option>
                    <option value="Pameran" {{ old('jenis_kegiatan') == 'Pameran' ? 'selected' : '' }}>Pameran</option>
                    <option value="Turnamen" {{ old('jenis_kegiatan') == 'Turnamen' ? 'selected' : '' }}>Turnamen</option>
                    <option value="Kompetisi" {{ old('jenis_kegiatan') == 'Kompetisi' ? 'selected' : '' }}>Kompetisi</option>
                    <option value="Workshop" {{ old('jenis_kegiatan') == 'Workshop' ? 'selected' : '' }}>Workshop</option>
                    <option value="Lainnya" {{ old('jenis_kegiatan') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
                @error('jenis_kegiatan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Total Pembiayaan -->
            <div>
                <label for="total_pembiayaan" class="mb-2 block text-sm font-medium text-gray-700">Total Pembiayaan (Rp)</label>
                <input type="text" name="total_pembiayaan" id="total_pembiayaan" value="{{ old('total_pembiayaan') }}" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('total_pembiayaan') border-red-500 @enderror" required>
                @error('total_pembiayaan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="deskripsi" class="mb-2 block text-sm font-medium text-gray-700">Deskripsi Event</label>
                <textarea name="deskripsi" id="deskripsi" rows="4" class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500 @error('deskripsi') border-red-500 @enderror" required>{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Upload Proposal -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Upload Proposal</label>
                <div class="mt-1 flex justify-center rounded-lg border-2 border-dashed border-gray-300 px-6 py-10 @error('proposal') border-red-500 @enderror">
                    <div class="text-center">
                        <i class="fas fa-cloud-upload-alt mb-3 text-3xl text-gray-400"></i>
                        <div class="flex text-sm text-gray-600">
                            <label for="proposal" class="relative cursor-pointer rounded-md font-medium text-blue-600 hover:text-blue-500">
                                <span>Upload file</span>
                                <input id="proposal" name="proposal" type="file" class="sr-only" accept=".pdf,.doc,.docx">
                            </label>
                            <p class="pl-1">atau drag and drop</p>
                        </div>
                        <p class="text-xs text-gray-500">PDF, DOC up to 10MB</p>
                        <p class="mt-2 text-sm text-gray-500" id="file-selected">Belum ada file yang dipilih</p>
                    </div>
                </div>
                @error('proposal')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Buttons -->
            <div class="flex justify-end space-x-4">
                <button type="button" onclick="window.location.href='{{ route('dashboard') }}'" class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Batal
                </button>
                <button type="submit" class="rounded-lg bg-blue-500 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                    Ajukan Event
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Show file name when selected
    document.getElementById('proposal').addEventListener('change', function(e) {
        const fileName = e.target.files[0]?.name || 'Belum ada file yang dipilih';
        document.getElementById('file-selected').textContent = fileName;
    });

    // Format currency for total pembiayaan
    document.getElementById('total_pembiayaan').addEventListener('input', function(e) {
        // Remove non-numeric characters
        let value = e.target.value.replace(/\D/g, '');
        
        // Format with thousands separator
        if (value) {
            value = parseInt(value).toLocaleString('id-ID');
            e.target.value = value;
        }
    });
</script>
@endsection 