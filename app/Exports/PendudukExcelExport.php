<?php

namespace App\Exports;

use App\Models\Penduduk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Maatwebsite\Excel\Events\BeforeSheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PendudukExcelExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithEvents,
    ShouldAutoSize
{
    protected $rt;
    protected $rw;

    public function __construct($rt = null, $rw = null)
    {
        $this->rt = $rt;
        $this->rw = $rw;
    }

    public function collection()
    {
        $query = Penduduk::with('kartuKeluarga');

        if ($this->rt) {
            $query->whereHas('kartuKeluarga', fn($q) => $q->where('rt', $this->rt));
        }

        if ($this->rw) {
            $query->whereHas('kartuKeluarga', fn($q) => $q->where('rw', $this->rw));
        }


        return $query->get()->map(function ($item, $key) {
            return [
                'No' => $key + 1,
                'Nama Lengkap' => $item->nama_lengkap,
                'Nomor Kartu Keluarga' => $item->kartuKeluarga->nomor_kk ?? '',
                'Alamat' => $item->kartuKeluarga->alamat ?? '',
                'RT' => $item->kartuKeluarga->rt ?? '',
                'RW' => $item->kartuKeluarga->rw ?? '',
                'Desa' => $item->kartuKeluarga->desa_kelurahan ?? '',
                'Kecamatan' => $item->kartuKeluarga->kecamatan ?? '',
                'Kabupaten' => $item->kartuKeluarga->kabupaten_kota ?? '',
                'Provinsi' => $item->kartuKeluarga->provinsi ?? '',
                'Kode Pos' => $item->kartuKeluarga->kode_pos ?? '',
                'NIK' => $item->nik,
                'Jenis Kelamin' => $item->jenis_kelamin,
                'Tempat Lahir' => $item->tempat_lahir,
                'Tanggal Lahir' => $item->tanggal_lahir,
                'Agama' => $item->agama,
                'Pendidikan' => $item->pendidikan,
                'Jenis Pekerjaan' => $item->jenis_pekerjaan,
                'Status Perkawinan' => $item->status_perkawinan,
                'Hubungan Dalam Keluarga' => $item->status_hubungan_dalam_keluarga,
                'Kewarganegaraan' => $item->kewarganegaraan,
                'Nama Ayah' => $item->nama_ayah,
                'Nama Ibu' => $item->nama_ibu,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Lengkap',
            'Nomor Kartu Keluarga',
            'Alamat',
            'RT',
            'RW',
            'Desa',
            'Kecamatan',
            'Kabupaten',
            'Provinsi',
            'Kode Pos',
            'NIK',
            'Jenis Kelamin',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Agama',
            'Pendidikan',
            'Jenis Pekerjaan',
            'Status Perkawinan',
            'Hubungan Dalam Keluarga',
            'Kewarganegaraan',
            'Nama Ayah',
            'Nama Ibu',
        ];
    }

    protected function getJudul(): string
    {
        $tahun = date('Y');
        $judul = "DATA PENDUDUK DESA REJOSARI";

        if ($this->rt && $this->rw) {
            $judul .= " RT {$this->rt} / RW {$this->rw}";
        } elseif ($this->rt) {
            $judul .= " RT {$this->rt}";
        } elseif ($this->rw) {
            $judul .= " RW {$this->rw}";
        }

        $judul .= " TAHUN {$tahun}";

        return $judul;
    }

    public function styles(Worksheet $sheet)
    {
        $headingRow = 3;
        $dataStartRow = 4;
        $lastColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();

        // Judul utama
        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->getStyle("A1")->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Heading baris ke-3
        $headingRange = "A{$headingRow}:{$lastColumn}{$headingRow}";
        $sheet->getStyle($headingRange)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'CCFFCC'],
            ],
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Border data
        if ($highestRow >= $dataStartRow) {
            $dataRange = "A{$dataStartRow}:{$lastColumn}{$highestRow}";
            $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        }

        // Format angka tanpa desimal
        foreach (['C', 'L'] as $col) {
            $sheet->getStyle("{$col}{$dataStartRow}:{$col}{$highestRow}")
                ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        }

        // Rata tengah semua kecuali kolom B
        foreach (range('A', $lastColumn) as $col) {
            if ($col === 'B') continue;
            $sheet->getStyle("{$col}{$dataStartRow}:{$col}{$highestRow}")
                ->getAlignment()->setHorizontal('center');
        }

        return [];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                // Tambahkan judul pada baris pertama
                $event->sheet->getDelegate()->insertNewRowBefore(1, 1);
                $event->sheet->getDelegate()->setCellValue('A1', $this->getJudul());
            },
        ];
    }
}
