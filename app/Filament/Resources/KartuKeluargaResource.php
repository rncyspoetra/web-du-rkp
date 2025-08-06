<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KartuKeluargaResource\Pages;
use App\Models\KartuKeluarga;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;

class KartuKeluargaResource extends Resource
{
    protected static ?string $model = KartuKeluarga::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Data Warga';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nomor_kk')
                    ->label('Nomor KK')
                    ->numeric()
                    ->required()
                    ->maxLength(16)
                    ->minLength(16)
                    ->rule('digits:16')
                    ->unique(ignoreRecord: true),

                Textarea::make('alamat')
                    ->label('Alamat')
                    ->required()
                    ->rows(2),

                TextInput::make('rt')
                    ->label('RT')
                    ->numeric()
                    ->required()
                    ->maxLength(3),

                TextInput::make('rw')
                    ->label('RW')
                    ->numeric()
                    ->required()
                    ->maxLength(3),

                TextInput::make('desa_kelurahan')
                    ->label('Desa / Kelurahan')
                    ->required()
                    ->maxLength(50),

                TextInput::make('kecamatan')
                    ->label('Kecamatan')
                    ->required()
                    ->maxLength(50),

                TextInput::make('kabupaten_kota')
                    ->label('Kabupaten / Kota')
                    ->required()
                    ->maxLength(50),

                TextInput::make('kode_pos')
                    ->label('Kode Pos')
                    ->numeric()
                    ->required()
                    ->maxLength(5),

                TextInput::make('provinsi')
                    ->label('Provinsi')
                    ->required()
                    ->maxLength(50),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->label('No')
                    ->rowIndex(),
                TextColumn::make('nomor_kk')->label('Nomor KK')->searchable(),
                TextColumn::make('alamat')->limit(20),
                TextColumn::make('rt')
                    ->label('RT / RW')
                    ->formatStateUsing(fn($state, $record) => 'RT ' . $record->rt . ' / RW ' . $record->rw),
                TextColumn::make('desa_kelurahan')->label('Desa / Kelurahan'),
            ])
            ->filters([])
            ->actions([
                EditAction::make(),
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKartuKeluargas::route('/'),
            'create' => Pages\CreateKartuKeluarga::route('/create'),
            'edit' => Pages\EditKartuKeluarga::route('/{record}/edit'),
        ];
    }
}
