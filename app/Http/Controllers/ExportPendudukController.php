<?php

namespace App\Http\Controllers;

use App\Exports\PendudukExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ExportPendudukController extends Controller
{
    public function export(Request $request)
    {
        $rt = $request->get('rt');
        $rw = $request->get('rw');

        $filename = 'penduduk';

        if ($rt || $rw) {
            if ($rt) $filename .= '_rt' . $rt;
            if ($rw) $filename .= '_rw' . $rw;
        }

        $filename .= '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new PendudukExcelExport($rt, $rw), $filename);
    }
}
