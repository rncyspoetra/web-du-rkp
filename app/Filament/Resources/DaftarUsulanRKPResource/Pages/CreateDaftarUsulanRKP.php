<?php

namespace App\Filament\Resources\DaftarUsulanRKPResource\Pages;

use App\Filament\Resources\DaftarUsulanRKPResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\SumberPembiayaan;
use App\Models\DaftarUsulanRKP;
use Filament\Notifications\Notification;

class CreateDaftarUsulanRKP extends CreateRecord
{
    protected static string $resource = DaftarUsulanRKPResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $sumber = SumberPembiayaan::find($data['sumber_pembiayaan_id']);

        if (!$sumber) {
            Notification::make()
                ->title('Sumber pembiayaan tidak ditemukan.')
                ->danger()
                ->send();

            $this->halt();
        }

        // Hitung total yang sudah diusulkan sebelumnya
        $totalTerpakai = DaftarUsulanRKP::where('sumber_pembiayaan_id', $sumber->id)->sum('prakiraan_biaya_jumlah');

        $sisaAnggaran = $sumber->total_anggaran - $totalTerpakai;

        if ($data['prakiraan_biaya_jumlah'] > $sisaAnggaran) {
            Notification::make()
                ->title('Anggaran tidak mencukupi')
                ->body('Sisa anggaran hanya Rp ' . number_format($sisaAnggaran, 0, ',', '.'))
                ->danger()
                ->send();

            $this->halt();
        }

        return $data;
    }
}
