<?php

use App\Http\Controllers\EventPengajuanController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Models\EventPengajuan;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::prefix('/login')->group(function () {
    Route::get('/', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/', [AuthController::class, 'login']);
});

Route::prefix('/register')->group(function () {
    Route::get('/', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/', [AuthController::class, 'register']);
});

Route::middleware('auth')->post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'user'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [EventPengajuanController::class, 'index'])->name('index');
    Route::get('/riwayat-pengajuan', [EventPengajuanController::class, 'indexRiwayatProposal'])->name('riwayat');

    Route::prefix('/event-pengajuan')->group(function () {
        Route::get('/create', [EventPengajuanController::class, 'create'])->name('create');
        Route::post('/create', [EventPengajuanController::class, 'store'])->name('store');
        Route::get('/{eventPengajuan}', [EventPengajuanController::class, 'show'])->name('show');
        Route::patch('/{eventPengajuan}/cancel', [EventPengajuanController::class, 'cancel'])->name('cancel');
        Route::get('/{eventPengajuan}/download', [EventPengajuanController::class, 'downloadFile'])->name('download-file');
    });
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/download', [AdminController::class, 'downloadPDF'])->name('index.download');

    Route::get('/riwayat-pengajuan', [AdminController::class, 'index2'])->name('riwayat');
    Route::get('/riwayat/download', [AdminController::class, 'downloadRiwayatPDF'])->name('riwayat.download');

    Route::prefix('/event-pengajuan')->group(function () {
        Route::get('/{eventPengajuan}', [AdminController::class, 'show'])->name('show');
        Route::patch('/{eventPengajuan}/approve', [AdminController::class, 'approve'])->name('approve');
        Route::patch('/{eventPengajuan}/reject', [AdminController::class, 'reject'])->name('reject');
        Route::get('/{eventPengajuan}/download', [AdminController::class, 'downloadFile'])->name('download-file');
    });

    Route::prefix('/users')->group(function () {
        Route::get('/', [AdminController::class, 'users'])->name('users');
        Route::get('/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/', [UserController::class, 'store'])->name('users.store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    });
});