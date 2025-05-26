@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 max-w-4xl">
    @if($errors->any())
    <div class="mb-6 p-4 rounded-md bg-red-50 border-l-4 border-red-500">
        <div class="flex">
            <svg class="h-5 w-5 text-red-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
            </svg>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Terdapat {{ $errors->count() }} kesalahan dalam pengisian form</h3>
                <div class="mt-2 text-sm text-red-700">
                    <ul class="list-disc pl-5 space-y-1">
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
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100">
        <!-- Card Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b">
            <h1 class="text-2xl font-bold text-gray-800">Ajukan Acara Kegiatan Baru</h1>
            <p class="mt-1 text-sm text-gray-600">Isi formulir di bawah ini untuk mengajukan acara kegiatan baru</p>
        </div>
        
        <!-- Form Body -->
        <form method="POST" action="{{ route('user.store') }}" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            
            <!-- Title Field -->
            <div class="space-y-1">
                <label for="judul_event" class="block text-sm font-medium text-gray-700">Judul Kegiatan <span class="text-red-500">*</span></label>
                <input type="text" name="judul_event" id="judul_event" value="{{ old('judul_event') }}" 
                    class="block w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('judul_event') border-red-300 @enderror"
                    placeholder="Masukkan judul kegiatan">
                @error('judul_event')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Activity Type -->
            <div class="space-y-1">
                <label for="jenis_kegiatan" class="block text-sm font-medium text-gray-700">Jenis Kegiatan <span class="text-red-500">*</span></label>
                <input type="text" name="jenis_kegiatan" id="jenis_kegiatan" value="{{ old('jenis_kegiatan') }}" 
                    class="block w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('jenis_kegiatan') border-red-300 @enderror"
                    placeholder="Contoh: Seminar, Workshop, Lomba">
                @error('jenis_kegiatan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Budget Field -->
            <div class="space-y-1">
                <label for="total_pembiayaan" class="block text-sm font-medium text-gray-700">Total Pembiayaan <span class="text-red-500">*</span></label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">Rp</span>
                    <input type="text" name="total_pembiayaan" id="total_pembiayaan" value="{{ old('total_pembiayaan') }}" 
                        class="block w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('total_pembiayaan') border-red-300 @enderror"
                        placeholder="0">
                </div>
                @error('total_pembiayaan')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Description Field -->
            <div class="space-y-1">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi Kegiatan <span class="text-red-500">*</span></label>
                <textarea name="deskripsi" id="deskripsi" rows="6" 
                    class="block w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition duration-200 @error('deskripsi') border-red-300 @enderror"
                    placeholder="Berikan deskripsi detail tentang kegiatan yang diajukan (minimal 50 karakter)">{{ old('deskripsi') }}</textarea>
                <div class="flex justify-between mt-1">
                    <p class="text-xs text-gray-500">Format: teks minimal 50 karakter</p>
                    <p class="text-xs"><span id="char-count">0</span>/50 karakter</p>
                </div>
                @error('deskripsi')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- File Upload -->
            <div class="space-y-1">
                <label for="proposal_file" class="block text-sm font-medium text-gray-700">File Proposal <span class="text-red-500">*</span></label>
                <div class="mt-1 flex max-md:flex-col items-center gap-4">
                    <label class="cursor-pointer max-md:mt-3">
                        <span class="sr-only">Pilih file</span>
                        <input type="file" name="proposal_file" id="proposal_file" 
                            class="hidden" accept=".pdf,.doc,.docx">
                        <div class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            Pilih File
                        </div>
                    </label>
                    <span id="file-name" class="text-sm text-gray-500 max-md:mb-3">Belum ada file dipilih</span>
                </div>
                <p class="mt-1 text-xs text-gray-500">Format: PDF, DOC, DOCX (maks. 10MiB)</p>
                @error('proposal_file')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Important Notes -->
            <div class="p-4 rounded-lg bg-blue-50 border border-blue-200">
                <h4 class="font-medium text-blue-800 flex items-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Catatan Penting
                </h4>
                <ul class="text-sm text-blue-700 space-y-1.5">
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Pastikan semua informasi yang diisi sudah benar
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        File proposal harus berisi detail lengkap tentang kegiatan
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Proposal akan ditinjau dalam 3-5 hari kerja
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mt-0.5 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Anda akan mendapat notifikasi tentang status proposal
                    </li>
                </ul>
            </div>
            
            <!-- Form Actions -->
            <div class="flex justify-between pt-4">
                <div></div>
                <div class="flex flex-row gap-2">
                    <a href="{{ route('user.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        Kembali
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                        </svg>
                        Ajukan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Character counter for description
        const descriptionField = document.getElementById('deskripsi');
        const charCount = document.getElementById('char-count');
        
        function updateCharCount() {
            const count = descriptionField.value.length;
            charCount.textContent = count;
            charCount.classList.toggle('text-red-500', count < 50);
            charCount.classList.toggle('text-green-500', count >= 50);
        }
        
        descriptionField.addEventListener('input', updateCharCount);
        updateCharCount();
        
        // Currency formatting for budget
        const budgetField = document.getElementById('total_pembiayaan');
        budgetField.addEventListener('input', function(e) {
            let value = this.value.replace(/\D/g, '');
            if (value) {
                value = parseInt(value, 10).toLocaleString('id-ID');
            }
            this.value = value;
        });
        
        // File upload display
        const fileInput = document.getElementById('proposal_file');
        const fileNameDisplay = document.getElementById('file-name');
        
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const fileSize = (this.files[0].size / 1024 / 1024).toFixed(2);
                fileNameDisplay.textContent = `${this.files[0].name} (${fileSize} MB)`;
            } else {
                fileNameDisplay.textContent = 'Belum ada file dipilih';
            }
        });
    });
</script>
@endsection