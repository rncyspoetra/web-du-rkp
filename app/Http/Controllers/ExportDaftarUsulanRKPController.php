<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\DaftarUsulanRKPExport;
use Maatwebsite\Excel\Facades\Excel;

class ExportDaftarUsulanRKPController extends Controller
{
    public function export(Request $request)
    {
        $validated = $request->validate([
            'sumber_pembiayaan' => 'required|string',
            'tahun' => 'required|string',
        ]);

        $sumberPembiayaan = $validated['sumber_pembiayaan'];
        $tahun = $validated['tahun'];

        $filename = 'du-rkp_' . str_replace(' ', '_', $sumberPembiayaan)
            . '_tahun_' . $tahun
            . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(
            new DaftarUsulanRKPExport($sumberPembiayaan, $tahun),
            $filename
        );
    }
}
