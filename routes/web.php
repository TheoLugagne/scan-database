<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('layouts.app');
});

Route::resource('scan', \App\Http\Controllers\ScanController::class);

Route::get('/scan/check-title', [\App\Http\Controllers\ScanController::class, 'checkTitle'])->name('scan.check-title');
