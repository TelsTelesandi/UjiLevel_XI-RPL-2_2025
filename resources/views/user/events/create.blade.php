@extends('layouts.app')

@section('content')
<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">Ajukan Event Baru</h1>
            <p class="text-gray-400">Lengkapi form berikut untuk mengajukan event baru</p>
        </div>
        <a href="{{ route('user.dashboard') }}" class="btn-ghost">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('user.events.store') }}" method="POST" enctype="multipart/form-data" class="bg-white/10 backdrop-blur-md border border-white/20 rounded-lg p-6 space-y-6">
            @csrf

            @if($errors->any())
                <div class="bg-red-500/20 border border-red-500/50 text-red-200 px-4 py-2 rounded">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2" for="judul_event">
                        Judul Event <span class="text-red-400">*</span>
                    </label>
                    <input type="text" 
                        id="judul_event" 
                        name="judul_event" 
                        value="{{ old('judul_event') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none"
                        placeholder="Masukkan judul event"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2" for="jenis_kegiatan">
                        Jenis Kegiatan <span class="text-red-400">*</span>
                    </label>
                    <select id="jenis_kegiatan" 
                        name="jenis_kegiatan" 
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white focus:border-blue-500 focus:outline-none appearance-none"
                        required>
                        <option value="" class="bg-slate-900 text-gray-400">Pilih jenis kegiatan</option>
                        <option value="Lomba" class="bg-slate-900 text-white" {{ old('jenis_kegiatan') === 'Lomba' ? 'selected' : '' }}>Lomba</option>
                        <option value="Workshop" class="bg-slate-900 text-white" {{ old('jenis_kegiatan') === 'Workshop' ? 'selected' : '' }}>Workshop</option>
                        <option value="Seminar" class="bg-slate-900 text-white" {{ old('jenis_kegiatan') === 'Seminar' ? 'selected' : '' }}>Seminar</option>
                        <option value="Pentas" class="bg-slate-900 text-white" {{ old('jenis_kegiatan') === 'Pentas' ? 'selected' : '' }}>Pentas</option>
                        <option value="Pentas" class="bg-slate-900 text-white" {{ old('jenis_kegiatan') === 'Pentas' ? 'selected' : '' }}>Pameran / Expo</option>
                        <option value="Pentas" class="bg-slate-900 text-white" {{ old('jenis_kegiatan') === 'Pentas' ? 'selected' : '' }}>Kunjungan Edukasi</option>
                        <option value="Pentas" class="bg-slate-900 text-white" {{ old('jenis_kegiatan') === 'Pentas' ? 'selected' : '' }}>Bakti Sosial / Donasi</option>
                        <option value="Pentas" class="bg-slate-900 text-white" {{ old('jenis_kegiatan') === 'Pentas' ? 'selected' : '' }}>Latihan Gabungan</option>
                        <option value="Pentas" class="bg-slate-900 text-white" {{ old('jenis_kegiatan') === 'Pentas' ? 'selected' : '' }}>Peringatan Hari Besar</option>
                        <option value="Pentas" class="bg-slate-900 text-white" {{ old('jenis_kegiatan') === 'Pentas' ? 'selected' : '' }}>Kegiatan Lingkungan</option>
                        <option value="Pentas" class="bg-slate-900 text-white" {{ old('jenis_kegiatan') === 'Pentas' ? 'selected' : '' }}>Pertandingan / Olahraga</option>
                        <option value="Pentas" class="bg-slate-900 text-white" {{ old('jenis_kegiatan') === 'Pentas' ? 'selected' : '' }}>Pertandingan / Olahraga</option>
                        <option value="Pentas" class="bg-slate-900 text-white" {{ old('jenis_kegiatan') === 'Pentas' ? 'selected' : '' }}>Rapat Umum / Musyawarah</option>
                        <option value="Lainnya" class="bg-slate-900 text-white" {{ old('jenis_kegiatan') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2" for="deskripsi">
                    Deskripsi Event <span class="text-red-400">*</span>
                </label>
                <textarea id="deskripsi" 
                    name="deskripsi" 
                    rows="4" 
                    class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none"
                    placeholder="Jelaskan detail event yang akan dilaksanakan"
                    required>{{ old('deskripsi') }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2" for="tanggal_pengajuan">
                        Tanggal Pengajuan <span class="text-red-400">*</span>
                    </label>
                    <input type="date" 
                        id="tanggal_pengajuan" 
                        name="tanggal_pengajuan" 
                        value="{{ old('tanggal_pengajuan') }}"
                        class="w-full bg-white/5 border border-white/10 rounded-lg px-4 py-2.5 text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none"
                        required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2" for="total_pembiayaan">
                        Total Pembiayaan <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-4 top-2.5 text-gray-400">Rp</span>
                        <input type="number" 
                            id="total_pembiayaan" 
                            name="total_pembiayaan" 
                            value="{{ old('total_pembiayaan') }}"
                            class="w-full bg-white/5 border border-white/10 rounded-lg pl-12 pr-4 py-2.5 text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none"
                            placeholder="0"
                            required>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-300 mb-2" for="proposal">
                    Upload Proposal (PDF) <span class="text-red-400">*</span>
                </label>
                <div class="flex items-center justify-center w-full">
                    <label for="proposal" class="flex flex-col items-center justify-center w-full h-32 border-2 border-white/10 border-dashed rounded-lg cursor-pointer bg-white/5 hover:bg-white/10 transition-colors">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-3"></i>
                            <p class="mb-2 text-sm text-gray-400" id="file-name">Klik untuk upload proposal</p>
                            <p class="text-xs text-gray-500">PDF (Maks. 5MB)</p>
                        </div>
                        <input id="proposal" name="proposal" type="file" class="hidden" accept=".pdf" required />
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-4">
                <a href="{{ route('user.dashboard') }}" class="btn-ghost">
                    Batal
                </a>
                <button type="submit" class="btn-gradient">
                    <i class="fas fa-paper-plane mr-2"></i>Ajukan Event
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('proposal').addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name || 'Klik untuk upload proposal';
    document.getElementById('file-name').textContent = fileName;
});
</script>
@endsection 