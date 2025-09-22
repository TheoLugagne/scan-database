<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\UserScanProgressController;
use App\Http\Controllers\GenderController;

Route::get('/', function () {
    return view('welcome');
});
Route::resource('scan', ScanController::class);

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    Route::get('/userScanProgress/fetch', [UserScanProgressController::class, 'fetch'])->name('userScanProgress.fetch');
    Route::resource('userScanProgress', UserScanProgressController::class);
    Route::get('/userScanProgress/create/{scan}', [UserScanProgressController::class, 'create'])->name('userScanProgress.create');
    Route::post('/userScanProgress/{userScanProgress}/update-chapter', [UserScanProgressController::class, 'updateChapter'])->name('userScanProgress.update-chapter');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/scan/{scan}/update-chapter', [ScanController::class, 'updateChapter'])->name('scan.update-chapter');
    Route::get('/scans/fetch', [ScanController::class, 'fetch'])->name('scans.fetch');
    Route::resource('gender', GenderController::class);
});

require __DIR__.'/auth.php';
