<?php

namespace App\Filament\Resources\DaftarUsulanRKPResource\Pages;

use App\Filament\Resources\DaftarUsulanRKPResource;
use App\Models\DaftarUsulanRKP;
use App\Models\SumberPembiayaan;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditDaftarUsulanRKP extends EditRecord
{
    protected static string $resource = DaftarUsulanRKPResource::class;

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
        $sumber = SumberPembiayaan::find($data['sumber_pembiayaan_id']);

        if (!$sumber) {
            Notification::make()
                ->title('Sumber pembiayaan tidak ditemukan.')
                ->danger()
                ->send();

            $this->halt();
        }

        $totalTerpakai = DaftarUsulanRKP::where('sumber_pembiayaan_id', $sumber->id)
            ->where('id', '!=', $this->record->id)
            ->sum('prakiraan_biaya_jumlah');

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
