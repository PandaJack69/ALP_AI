<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StudyController; // Semua import diletakkan di atas
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Halaman depan (Landing Page)
Route::get('/', function () {
    return view('welcome');
});

// Grup Route yang Membutuhkan Login (Auth)
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard Utama (Hasil AI)
    Route::get('/dashboard', [StudyController::class, 'index'])->name('dashboard');
    
    // Fitur Simpan Data (Data Acquisition)
    Route::post('/sleep-store', [StudyController::class, 'storeSleep'])->name('sleep.store');
    Route::post('/schedule-store', [StudyController::class, 'storeSchedule'])->name('schedule.store');

    // Route Profil (Bawaan Breeze - Sebaiknya jangan dihapus agar user bisa logout/edit profil)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Tambahkan ini:
    Route::get('/history', [StudyController::class, 'history'])->name('history.index');
    Route::get('/schedules', [StudyController::class, 'schedules'])->name('schedules.index');
});

require __DIR__.'/auth.php';