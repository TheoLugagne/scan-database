<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('scan.index');
});

Route::resource('scan', \App\Http\Controllers\ScanController::class);

Route::post('/scan/{scan}/update-chapter', [App\Http\Controllers\ScanController::class, 'updateChapter'])->name('scan.update-chapter');
