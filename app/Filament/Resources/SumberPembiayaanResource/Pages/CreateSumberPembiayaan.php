<?php

namespace App\Filament\Resources\SumberPembiayaanResource\Pages;

use App\Filament\Resources\SumberPembiayaanResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSumberPembiayaan extends CreateRecord
{
    protected static string $resource = SumberPembiayaanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
