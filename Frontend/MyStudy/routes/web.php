<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudyController;


Route::get('/', function () {
    return view('welcome');
});

// Route::get('/', [StudyController::class, 'index']);

Route::get('/', [StudyController::class, 'index'])->name('dashboard');
Route::post('/store-sleep', [StudyController::class, 'storeSleep'])->name('sleep.store');
Route::post('/store-schedule', [StudyController::class, 'storeSchedule'])->name('schedule.store');
