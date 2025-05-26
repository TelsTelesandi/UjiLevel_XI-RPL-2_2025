@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 sm:px-6 lg:px-8 flex flex-col items-center justify-center min-h-[calc(100vh-94px)] py-12 text-center">
    <h1 class="text-4xl font-bold tracking-tight sm:text-5xl md:text-6xl">Sistem Manajemen Acara Kegiatan</h1>
    <p class="mt-6 text-lg text-gray-600 max-w-3xl">
        Sistem komprehensif untuk mengajukan, meninjau, dan mengelola proposal acara kegiatan <br> Daftar atau masuk untuk memulai!
    </p>
    
    <div class="mt-16 grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl">
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-lg flex items-center justify-center mb-4 mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium mb-2">Ajukan Kegiatan</h3>
            <p class="text-gray-600">Ajukan proposal kegiatan dengan mudah melalui sistem online yang terintegrasi.</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="w-12 h-12 bg-green-100 text-green-600 rounded-lg flex items-center justify-center mb-4 mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
            </div>
            <h3 class="text-lg font-medium mb-2">Pantau Status</h3>
            <p class="text-gray-600">Pantau status pengajuan kegiatan Anda secara real-time dengan notifikasi terintegrasi.</p>
        </div>
        
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-lg flex items-center justify-center mb-4 mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                </svg>
            </div>
            <h3 class="text-lg font-medium mb-2">Komunikasi Efektif</h3>
            <p class="text-gray-600">Komunikasi langsung dengan admin untuk revisi dan klarifikasi pengajuan kegiatan.</p>
        </div>
    </div>
</div>
@endsection