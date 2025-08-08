<?php

namespace App\Exports;

use App\Models\DaftarUsulanRKP;
use App\Models\SumberPembiayaan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Events\BeforeSheet;
use Illuminate\Support\Facades\Log;

class DaftarUsulanRKPExport implements FromCollection, WithEvents, WithStyles, ShouldAutoSize
{
    protected $sumberPembiayaan;
    protected $tahun;
    protected $pagu;

    protected function getDataBidang($bidang)
    {
        $data = DaftarUsulanRKP::with('sumberPembiayaan')
            ->where('bidang', $bidang)
            ->whereHas('sumberPembiayaan', function ($q) {
                $q->where('tahun', $this->tahun)
                    ->where('sumber_pembiayaan', $this->sumberPembiayaan);
            })
            ->get()
            ->map(function ($item, $key) {
                return [
                    'No' => 1,
                    'Bidang' => $item->bidang,
                    'nomor' => $key + 1,
                    'Jenis Kegiatan' => $item->jenis_kegiatan,
                    'Lokasi' => $item->lokasi,
                    'Volume' => $item->volume,
                    'Perkiraan Waktu Pelaksanaan' => $item->perkiraan_waktu_pelaksanaan,
                    'Prakiraan Biaya Jumlah (Rp)' => number_format($item->prakiraan_biaya_jumlah, 0, ',', '.'),
                    'Sumber Pembiayaan' => $item->sumberPembiayaan->sumber_pembiayaan ?? '-',
                ];
            });
        return $data;
    }

    protected function renderBidang(Worksheet $sheet, int $startRow, string $bidang, string $labelJumlah, int $no): int
    {
        $data = $this->getDataBidang($bidang);
        $rowCount = count($data);

        if ($rowCount === 0) {
            for ($i = 0; $i < 2; $i++) {
                $currentRow = $startRow + $i;
                // Kolom A isi $no, kolom lainnya isi '-'
                $sheet->fromArray([$no, $bidang, '-', '-', '-', '-', '-', '-', '-'], null, 'A' . $currentRow);
                $sheet->getStyle("D{$currentRow}")->getAlignment()->setHorizontal('center');
            }

            $endRow = $startRow + 1;
            $total = '-';
        } else {
            foreach ($data as $i => $row) {
                // Ubah array_values agar kolom A selalu $no
                $rowArray = array_values($row);
                $rowArray[0] = $no; // Pastikan kolom pertama = nomor bidang
                $sheet->fromArray($rowArray, null, 'A' . ($startRow + $i));
            }

            $endRow = $startRow + $rowCount - 1;
            $total = number_format($data->sum(function ($item) {
                return (int) str_replace('.', '', $item['Prakiraan Biaya Jumlah (Rp)']);
            }), 0, ',', '.');
        }

        if ($endRow > $startRow) {
            $sheet->mergeCells("A{$startRow}:A{$endRow}");
            $sheet->mergeCells("B{$startRow}:B{$endRow}");
        }

        $sheet->getStyle("A{$startRow}:B{$endRow}")->applyFromArray([
            'alignment' => [
                'horizontal' => 'center',
                'vertical' => 'center',
            ],
        ]);

        $jumlahRow = $endRow + 1;

        $sheet->setCellValue("A{$jumlahRow}", $labelJumlah);
        $sheet->mergeCells("A{$jumlahRow}:G{$jumlahRow}");
        $sheet->setCellValue("H{$jumlahRow}", $total);

        $sheet->getStyle("A{$jumlahRow}")->applyFromArray([
            'font' => ['italic' => true],
            'alignment' => ['horizontal' => 'right'],
        ]);

        $sheet->getStyle("H{$jumlahRow}")->applyFromArray([
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'right'],
        ]);

        return $jumlahRow + 1;
    }



    public function __construct($sumberPembiayaan, $tahun)
    {
        $this->sumberPembiayaan = $sumberPembiayaan;
        $this->tahun = $tahun;

        // Ambil hanya 1 data sumber pembiayaan yang cocok
        $sumber = SumberPembiayaan::where('tahun', $tahun)
            ->where('sumber_pembiayaan', $sumberPembiayaan)
            ->first();

        $this->pagu = $sumber?->total_anggaran ?? 0;

        Log::info("Tahun: {$tahun}, Sumber Pembiayaan: {$sumberPembiayaan}, Pagu: {$this->pagu}");
    }

    public function collection()
    {
        return collect([]);
    }

    protected function getJudul(): array
    {
        return [
            'judul' => "DAFTAR USULAN RKP DESA (DU-RKP DESA) TAHUN {$this->tahun}",
            'desa' => 'REJOSARI',
            'kecamatan' => 'DEKET',
            'kabupaten' => 'LAMONGAN',
            'provinsi' => 'JAWA TIMUR',
            'sumber_pembiayaan' => $this->sumberPembiayaan,
            'pagu' => $this->pagu,
        ];
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $info = $this->getJudul();

                // Insert judul info 7 baris
                $sheet->insertNewRowBefore(1, 8);
                $sheet->setCellValue('A1', $info['judul']);
                $sheet->setCellValue('A2', 'DESA');
                $sheet->setCellValue('B2', ': ' . $info['desa']);
                $sheet->setCellValue('A3', 'KECAMATAN');
                $sheet->setCellValue('B3', ': ' . $info['kecamatan']);
                $sheet->setCellValue('A4', 'KABUPATEN');
                $sheet->setCellValue('B4', ': ' . $info['kabupaten']);
                $sheet->setCellValue('A5', 'PROVINSI');
                $sheet->setCellValue('B5', ': ' . $info['provinsi']);
                $sheet->setCellValue('A6', 'SUMBER PEMBIAYAAN');
                $sheet->setCellValue('B6', ': ' . $info['sumber_pembiayaan']);
                $sheet->setCellValue('A7', 'PAGU');
                $sheet->setCellValue('B7', ': ' . number_format($info['pagu'], 0, ',', '.'));

                // Tulis heading (baris 9 dan 10)
                $sheet->setCellValue('A9', 'No');
                $sheet->setCellValue('B9', 'Bidang');
                $sheet->setCellValue('C9', 'Jenis Kegiatan');
                $sheet->setCellValue('E9', 'Lokasi');
                $sheet->setCellValue('F9', 'Volume');
                $sheet->setCellValue('G9', 'Perkiraan Waktu Pelaksanaan');
                $sheet->setCellValue('H9', 'Prakiraan Biaya Jumlah (Rp)');
                $sheet->setCellValue('I9', 'Sumber Pembiayaan');

                $sheet->mergeCells('C9:D9');

                // Abjad (baris 10)
                $sheet->fromArray(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i'], null, 'A10');

                // Data mulai dari baris 11
                $nextRow = $this->renderBidang($sheet, 11, 'Penyelenggaraan Pemerintahan Desa', 'Jumlah per Bidang 1', '1');
                $nextRow = $this->renderBidang($sheet, $nextRow, 'Pembangunan Desa', 'Jumlah per Bidang 2', '2');
                $nextRow = $this->renderBidang($sheet, $nextRow, 'Pembinaan Masyarakat', 'Jumlah per Bidang 3', '3');
                $nextRow = $this->renderBidang($sheet, $nextRow, 'Pemberdayaan Masyarakat', 'Jumlah per Bidang 4', '4');
            },
        ];
    }


    public function styles(Worksheet $sheet)
    {
        $headingRow1 = 9;
        $headingRow2 = 10;
        $dataStartRow = 11;

        $lastColumn = $sheet->getHighestColumn();
        $highestRow = $sheet->getHighestRow();

        // Judul utama
        $sheet->mergeCells("A1:{$lastColumn}1");
        $sheet->getStyle("A1")->applyFromArray([
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => 'center'],
        ]);

        // Header Baris 1 (row 9)
        $sheet->mergeCells("C{$headingRow1}:D{$headingRow1}");
        $sheet->getStyle("A{$headingRow1}:{$lastColumn}{$headingRow1}")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'CCFFCC'],
            ],
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Header Baris 2 (row 10 - abjad)
        $sheet->getStyle("A{$headingRow2}:{$lastColumn}{$headingRow2}")->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFFFF2CC'],
            ],
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => 'center'],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        $sheet->mergeCells("C{$headingRow1}:D{$headingRow1}");

        // Data style
        if ($highestRow >= $dataStartRow) {
            $dataRange = "A{$dataStartRow}:{$lastColumn}{$highestRow}";
            $sheet->getStyle($dataRange)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

            // Rata tengah semua kecuali kolom 'Jenis Kegiatan'
            foreach (range('A', $lastColumn) as $col) {
                if ($col === 'D') continue;
                $sheet->getStyle("{$col}{$dataStartRow}:{$col}{$highestRow}")
                    ->getAlignment()->setHorizontal('center');
            }

            // Format kolom biaya sebagai angka
            $sheet->getStyle("G{$dataStartRow}:G{$highestRow}")
                ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
        }

        return [];
    }
}
