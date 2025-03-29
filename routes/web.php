<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('scan.index');
});

Route::resource('scan', \App\Http\Controllers\ScanController::class);
