<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DaftarUsulanRKPResource\Pages;
use App\Models\DaftarUsulanRKP;
use App\Models\SumberPembiayaan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;


class DaftarUsulanRKPResource extends Resource
{
    protected static ?string $model = DaftarUsulanRKP::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';
    protected static ?string $navigationGroup = 'RKP Desa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('bidang')
                    ->label('Bidang Kegiatan')
                    ->options([
                        'Penyelengaraan Pemerintahan Desa' => 'Penyelengaraan Pemerintahan Desa',
                        'Pembangunan Desa' => 'Pembangunan Desa',
                        'Pembinaan Masyarakat' => 'Pembinaan Masyarakat',
                        'Pemberdayaan Masyarakat' => 'Pemberdayaan Masyarakat',
                    ])
                    ->required()
                    ->searchable(),
                Forms\Components\TextInput::make('jenis_kegiatan')->required(),
                Forms\Components\TextInput::make('lokasi')->required(),
                Forms\Components\TextInput::make('volume')->required(),
                Forms\Components\TextInput::make('perkiraan_waktu_pelaksanaan')->required(),
                Forms\Components\TextInput::make('prakiraan_biaya_jumlah')
                    ->numeric()
                    ->required()
                    ->prefix('Rp'),
                Select::make('sumber_pembiayaan_id')
                    ->label('Sumber Pembiayaan')
                    ->options(function () {
                        return \App\Models\SumberPembiayaan::all()->pluck(
                            fn($item) => $item->sumber_pembiayaan . ' ' . $item->tahun,
                            'id'
                        );
                    })
                    ->searchable()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('bidang')->searchable(),
                Tables\Columns\TextColumn::make('jenis_kegiatan')->searchable(),
                Tables\Columns\TextColumn::make('prakiraan_biaya_jumlah')
                    ->money('IDR', true),
                Tables\Columns\TextColumn::make('sumberPembiayaan')
                    ->label('Sumber Pembiayaan')
                    ->formatStateUsing(
                        fn($record) =>
                        $record->sumberPembiayaan->sumber_pembiayaan . ' ' . $record->sumberPembiayaan->tahun
                    )
                    ->searchable(),

            ])
            ->filters([
                SelectFilter::make('sumber_pembiayaan')
                    ->label('Sumber Pembiayaan')
                    ->relationship('sumberPembiayaan', 'sumber_pembiayaan')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('sumber_pembiayaan_id')
                    ->label('Sumber Pembiayaan')
                    ->relationship('sumberPembiayaan', 'sumber_pembiayaan'),
                Tables\Filters\SelectFilter::make('sumber_pembiayaan_id')
                    ->label('Tahun')
                    ->relationship('sumberPembiayaan', 'tahun'),
            ])
            ->filters([
                SelectFilter::make('sumber_pembiayaan')
                    ->label('Sumber Pembiayaan')
                    ->relationship('sumberPembiayaan', 'sumber_pembiayaan')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('tahun')
                    ->label('Tahun')
                    ->relationship('sumberPembiayaan', 'tahun')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDaftarUsulanRKP::route('/'),
            'create' => Pages\CreateDaftarUsulanRKP::route('/create'),
            'edit' => Pages\EditDaftarUsulanRKP::route('/{record}/edit'),
        ];
    }
}
