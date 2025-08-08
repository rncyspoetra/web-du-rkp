<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BackupDatabaseController extends Controller
{
    public function backup()
    {
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port');
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');

        $fileName = 'backup-' . date('Y-m-d_H-i-s') . '.sql';
        $backupPath = storage_path("app/{$fileName}");

        $mysqldumpPath = env('MYSQLDUMP_PATH');

        $command = "\"{$mysqldumpPath}\" --user={$dbUser} --password={$dbPass} --host={$dbHost} --port={$dbPort} {$dbName} > \"{$backupPath}\"";

        system($command);

        return response()->download($backupPath)->deleteFileAfterSend(true);
    }
}
