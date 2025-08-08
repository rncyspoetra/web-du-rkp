<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExportPendudukController;
use App\Http\Controllers\ExportDaftarUsulanRKPController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/penduduk/export', [ExportPendudukController::class, 'export'])->name('penduduk.export');
Route::get('/du-rkp/export', [ExportDaftarUsulanRKPController::class, 'export'])->name('du-rkp.export');
Route::get('/backup-database', function () {
    $dbHost = env('DB_HOST', '127.0.0.1');
    $dbPort = env('DB_PORT', '3306');
    $dbName = env('DB_DATABASE', 'db_du_rkp');
    $dbUser = env('DB_USERNAME', 'root');
    $dbPass = env('DB_PASSWORD', '');

    $fileName = 'backup-' . date('Y-m-d_H-i-s') . '.sql';
    $backupPath = storage_path("app/{$fileName}");

    // full path to mysqldump
    $mysqldumpPath = 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe';

    // build command
    $command = "\"{$mysqldumpPath}\" --user={$dbUser} --password={$dbPass} --host={$dbHost} --port={$dbPort} {$dbName} > \"{$backupPath}\"";

    system($command);

    return response()->download($backupPath)->deleteFileAfterSend(true);
})->name('backup.database');
