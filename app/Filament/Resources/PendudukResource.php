<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PendudukResource\Pages;
use App\Models\Penduduk;
use App\Models\KartuKeluarga;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;

// Import komponen Forms
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Filters\SelectFilter;

// Import komponen Tables
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\ExportBulkAction;
use App\Exports\PendudukExcelExport;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Section;


class PendudukResource extends Resource
{
    protected static ?string $model = Penduduk::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Data Warga';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('kartu_keluarga_id')
                    ->label('Kartu Keluarga')
                    ->relationship('kartuKeluarga', 'nomor_kk')
                    ->required(),

                TextInput::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->required(),

                TextInput::make('nik')
                    ->numeric()
                    ->maxLength(16)
                    ->minLength(16)
                    ->rule('digits:16')
                    ->label('NIK')
                    ->required(),

                Select::make('jenis_kelamin')
                    ->options([
                        'Laki-laki' => 'Laki-laki',
                        'Perempuan' => 'Perempuan',
                    ])
                    ->label('Jenis Kelamin')
                    ->required(),

                TextInput::make('tempat_lahir')
                    ->label('Tempat Lahir')
                    ->required(),

                DatePicker::make('tanggal_lahir')
                    ->label('Tanggal Lahir')
                    ->required(),

                TextInput::make('agama')
                    ->label('Agama')
                    ->required(),

                TextInput::make('pendidikan')
                    ->label('Pendidikan')
                    ->required(),

                TextInput::make('jenis_pekerjaan')
                    ->label('Jenis Pekerjaan')
                    ->required(),

                TextInput::make('status_perkawinan')
                    ->label('Status Perkawinan')
                    ->required(),

                TextInput::make('status_hubungan_dalam_keluarga')
                    ->label('Status Hubungan Dalam Keluarga')
                    ->required(),

                TextInput::make('kewarganegaraan')
                    ->label('Kewarganegaraan')
                    ->required(),

                TextInput::make('nama_ayah')
                    ->label('Nama Ayah')
                    ->required(),

                TextInput::make('nama_ibu')
                    ->label('Nama Ibu')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no')
                    ->label('No')
                    ->rowIndex(),

                TextColumn::make('kartuKeluarga.nomor_kk')
                    ->label('Nomor KK')
                    ->searchable(),

                TextColumn::make('nik')
                    ->label('NIK')
                    ->searchable(),

                TextColumn::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->searchable(),

                TextColumn::make('kartuKeluarga.rt')
                    ->label('RT / RW')
                    ->formatStateUsing(fn($state, $record) => 'RT ' . $record->kartuKeluarga?->rt . ' / RW ' . $record->kartuKeluarga?->rw),

                TextColumn::make('kartuKeluarga.alamat')
                    ->label('Alamat'),
            ])
            ->defaultSort('id', 'asc')
            ->filters([
                SelectFilter::make('rt')
                    ->label('Filter RT')
                    ->relationship('kartuKeluarga', 'rt')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('rw')
                    ->label('Filter RW')
                    ->relationship('kartuKeluarga', 'rw')
                    ->searchable()
                    ->preload(),
            ])
            ->headerActions([
                Action::make('exportCustom')
                    ->label('Export Excel')
                    ->form([
                        Select::make('rt')
                            ->label('RT')
                            ->options(
                                KartuKeluarga::select('rt')->distinct()->pluck('rt', 'rt')->toArray()
                            )
                            ->placeholder('Pilih RT (opsional)')
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('rw', null);
                                }
                            })
                            ->disabled(fn(callable $get) => $get('rw')),

                        Select::make('rw')
                            ->label('RW')
                            ->options(
                                KartuKeluarga::select('rw')->distinct()->pluck('rw', 'rw')->toArray()
                            )
                            ->placeholder('Pilih RW (opsional)')
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('rt', null);
                                }
                            })
                            ->disabled(fn(callable $get) => $get('rt')),
                    ])

                    ->action(function (array $data) {
                        $rt = $data['rt'] ?? null;
                        $rw = $data['rw'] ?? null;

                        $queryParams = [];

                        if ($rt) {
                            $queryParams['rt'] = $rt;
                        }

                        if ($rw) {
                            $queryParams['rw'] = $rw;
                        }

                        $queryString = http_build_query($queryParams);

                        return redirect()->away(
                            route('penduduk.export') . ($queryString ? '?' . $queryString : '')
                        );
                    })
            ])
            ->actions([
                EditAction::make(),
                ViewAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ExportBulkAction::make()->exporter(PendudukExcelExport::class)
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
            'index' => Pages\ListPenduduks::route('/'),
            'create' => Pages\CreatePenduduk::route('/create'),
            'edit' => Pages\EditPenduduk::route('/{record}/edit'),
        ];
    }
}
