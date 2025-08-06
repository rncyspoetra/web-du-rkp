<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SumberPembiayaanResource\Pages;
use App\Filament\Resources\SumberPembiayaanResource\RelationManagers;
use App\Models\SumberPembiayaan;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ViewAction;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;

class SumberPembiayaanResource extends Resource
{
    protected static ?string $model = SumberPembiayaan::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = 'RKP Desa';

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('sumber_pembiayaan')->required(),
            TextInput::make('tahun')->numeric()->minValue(2000)->maxValue(2100)->required(),
            TextInput::make('total_anggaran')->numeric()->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sumber_pembiayaan')->searchable(),
                TextColumn::make('tahun'),
                TextColumn::make('total_anggaran')->money('IDR', true),
                TextColumn::make('sisa_anggaran')
                    ->label('Sisa Anggaran')
                    ->money('IDR', true)
                    ->getStateUsing(function ($record) {
                        $terpakai = $record->usulanRkp()->sum('prakiraan_biaya_jumlah');
                        return $record->total_anggaran - $terpakai;
                    }),

            ])
            ->filters([
                SelectFilter::make('tahun')
                    ->label('Filter Tahun')
                    ->options(
                        fn() => SumberPembiayaan::query()
                            ->select('tahun')
                            ->distinct()
                            ->pluck('tahun', 'tahun')
                            ->sort()
                    )
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSumberPembiayaans::route('/'),
            'create' => Pages\CreateSumberPembiayaan::route('/create'),
            'edit' => Pages\EditSumberPembiayaan::route('/{record}/edit'),
        ];
    }
}
