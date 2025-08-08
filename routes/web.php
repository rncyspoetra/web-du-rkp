<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExportPendudukController;
use App\Http\Controllers\ExportDaftarUsulanRKPController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/penduduk/export', [ExportPendudukController::class, 'export'])->name('penduduk.export');
Route::get('/du-rkp/export', [ExportDaftarUsulanRKPController::class, 'export'])->name('du-rkp.export');
