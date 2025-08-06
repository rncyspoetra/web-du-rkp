<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExportPendudukController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/penduduk/export', [ExportPendudukController::class, 'export'])->name('penduduk.export');
