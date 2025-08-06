<?php

namespace App\Filament\Resources\SumberPembiayaanResource\Pages;

use App\Filament\Resources\SumberPembiayaanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSumberPembiayaans extends ListRecords
{
    protected static string $resource = SumberPembiayaanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Sumber Pembiayaan'),
        ];
    }
}
