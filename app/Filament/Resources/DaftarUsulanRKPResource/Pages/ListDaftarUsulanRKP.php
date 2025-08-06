<?php

namespace App\Filament\Resources\DaftarUsulanRKPResource\Pages;

use App\Filament\Resources\DaftarUsulanRKPResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDaftarUsulanRKP extends ListRecords
{
    protected static string $resource = DaftarUsulanRKPResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Usulan RKP')
        ];
    }
}
