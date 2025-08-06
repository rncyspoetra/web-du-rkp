<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use App\Models\Penduduk;
use App\Models\KartuKeluarga;

class StatistikDashboard extends BaseWidget
{
    protected function getCards(): array
    {
        return [
            Card::make('Jumlah Penduduk', Penduduk::count())
                ->description('Total seluruh penduduk')
                ->icon('heroicon-o-user'), // ikon orang

            Card::make('Jumlah KK', KartuKeluarga::count())
                ->description('Total kartu keluarga')
                ->icon('heroicon-o-rectangle-stack'), // ikon seperti dokumen/kotak
        ];
    }
}
