<?php

namespace App\Filament\Resources\SumberPembiayaanResource\Pages;

use App\Filament\Resources\SumberPembiayaanResource;
use App\Models\DaftarUsulanRKP;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditSumberPembiayaan extends EditRecord
{
    protected static string $resource = SumberPembiayaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ambil ID dari sumber pembiayaan yang sedang diedit
        $sumberId = $this->record->id;

        // Hitung total biaya yang sudah digunakan
        $totalTerpakai = DaftarUsulanRKP::where('sumber_pembiayaan_id', $sumberId)->sum('prakiraan_biaya_jumlah');

        if ($data['total_anggaran'] < $totalTerpakai) {
            Notification::make()
                ->title('Total anggaran terlalu kecil')
                ->body('Total anggaran tidak boleh lebih kecil dari total yang sudah digunakan: Rp ' . number_format($totalTerpakai, 0, ',', '.'))
                ->danger()
                ->send();

            $this->halt();
        }

        return $data;
    }
}
