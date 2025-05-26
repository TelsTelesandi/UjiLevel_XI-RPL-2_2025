<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserManagementController;

// Public routes (no middleware)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Guest routes (only for non-authenticated users)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Auth routes (for authenticated users)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Admin routes
    Route::middleware('role:admin')->group(function () {
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
            Route::get('/events/{event}', [AdminController::class, 'showEvent'])->name('events.show');
            Route::post('/events/{event}/approve', [AdminController::class, 'approveEvent'])->name('events.approve');
            Route::post('/events/{event}/reject', [AdminController::class, 'rejectEvent'])->name('events.reject');
            Route::get('/events/{event}/proposal', [AdminController::class, 'viewProposal'])->name('events.proposal');
            Route::get('/reports', [AdminController::class, 'eventReports'])->name('reports');
            
            // User Management Routes
            Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
            Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
            Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
            Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
            Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
            Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        });
    });

    // User routes
    Route::middleware('role:user')->group(function () {
        Route::prefix('user')->name('user.')->group(function () {
            Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
            Route::get('/events/create', [UserController::class, 'createEvent'])->name('events.create');
            Route::post('/events', [UserController::class, 'storeEvent'])->name('events.store');
            Route::get('/events/{event}', [UserController::class, 'showEvent'])->name('events.show');
            Route::post('/events/{event}/close', [UserController::class, 'closeEvent'])->name('events.close');
            Route::post('/events/{event}/photos', [UserController::class, 'uploadPhotos'])->name('events.photos');
            Route::get('/events/{event}/proposal', [UserController::class, 'viewProposal'])->name('events.proposal');
            Route::get('/reports', [UserController::class, 'reports'])->name('reports');
        });
    });
});
