<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EventController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route untuk halaman login
Route::get('/', function () {
    return view('auth.login');
})->name('login');

// Auth Routes
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Routes untuk User (Ketua Ekskul)
Route::middleware(['auth'])->group(function () {
    // Dashboard User
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    
    // Pengajuan Event
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');
    Route::put('/events/{event}/close', [EventController::class, 'close'])->name('events.close');
});

// Routes untuk Admin
Route::middleware(['auth'])->group(function () {
    // Dashboard Admin
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    
    // Manajemen User
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::post('/admin/users', [AdminController::class, 'storeUser'])->name('admin.users.store');
    Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])->name('admin.users.update');
    Route::delete('/admin/users/{user}', [AdminController::class, 'deleteUser'])->name('admin.users.delete');
    
    // Approval Event
    Route::get('/admin/events', [AdminController::class, 'events'])->name('admin.events');
    Route::get('/admin/events/{event}/detail', [AdminController::class, 'eventDetail'])->name('admin.events.detail');
    Route::put('/admin/events/{event}/approve', [AdminController::class, 'approveEvent'])->name('admin.events.approve');
    Route::put('/admin/events/{event}/reject', [AdminController::class, 'rejectEvent'])->name('admin.events.reject');
    Route::post('/admin/events/{event}/comment', [AdminController::class, 'addComment'])->name('admin.events.comment');
    Route::put('/admin/events/{event}/close', [AdminController::class, 'closeEvent'])->name('admin.events.close');
    
    // Laporan
    Route::get('/admin/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::post('/admin/reports/export', [AdminController::class, 'exportReport'])->name('admin.reports.export');
});

// Fallback route
Route::fallback(function () {
    return redirect()->route('login');
});
