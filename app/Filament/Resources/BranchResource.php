<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BranchResource\Pages;
use App\Filament\Resources\BranchResource\RelationManagers;
use App\Models\Branch;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BranchResource extends Resource
{
    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Cabang';

    protected static ?string $modelLabel = 'Cabang';

    protected static ?string $pluralModelLabel = 'Cabang';

    protected static ?string $navigationGroup = 'Website Settings';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Cabang')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Cabang')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Klinik Hewan Cabang Bekasi'),

                        Forms\Components\Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->required()
                            ->rows(3)
                            ->placeholder('Jl. Contoh No. 123, Kota, Provinsi'),

                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->placeholder('021-12345678'),

                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->placeholder('cabang@klinikhewan.com'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Jam Operasional')
                    ->schema([
                        Forms\Components\Textarea::make('operational_hours')
                            ->label('Jam Operasional')
                            ->rows(4)
                            ->placeholder("Senin - Jumat: 08.00 - 20.00\nSabtu: 08.00 - 17.00\nMinggu: 09.00 - 15.00")
                            ->helperText('Masukkan jam operasional per baris'),
                    ]),

                Forms\Components\Section::make('Lokasi & Peta')
                    ->schema([
                        Forms\Components\Textarea::make('google_maps_iframe')
                            ->label('Google Maps Embed Code (Iframe)')
                            ->rows(6)
                            ->placeholder('<iframe src="https://www.google.com/maps/embed?pb=..." width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>')
                            ->columnSpanFull(),

                        Forms\Components\Placeholder::make('maps_instruction')
                            ->label('Panduan Lengkap')
                            ->content(new \Illuminate\Support\HtmlString('
                                <div class="text-sm space-y-2">
                                    <p class="font-semibold text-gray-700 dark:text-gray-300">Langkah-langkah mendapatkan Google Maps Iframe:</p>
                                    <ol class="list-decimal list-inside space-y-1 text-gray-600 dark:text-gray-400">
                                        <li>Buka <a href="https://www.google.com/maps" target="_blank" class="text-primary-600 hover:underline">Google Maps</a></li>
                                        <li>Cari dan pilih lokasi cabang Anda</li>
                                        <li>Klik tombol <strong>Share</strong> atau <strong>Bagikan</strong></li>
                                        <li>Pilih tab <strong>Embed a map</strong> atau <strong>Sematkan peta</strong></li>
                                        <li>Pilih ukuran peta (Small, Medium, Large, atau Custom)</li>
                                        <li>Klik <strong>COPY HTML</strong></li>
                                        <li>Paste kode HTML tersebut ke field diatas</li>
                                    </ol>
                                    <p class="mt-3 text-xs text-gray-500 dark:text-gray-500">
                                        💡 Tips: Gunakan iframe untuk tampilan peta yang lebih baik. Jika tidak ada iframe, sistem akan otomatis menggunakan koordinat latitude & longitude.
                                    </p>
                                </div>
                            '))
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Forms\Components\Section::make('Pengaturan')
                    ->schema([
                        Forms\Components\TextInput::make('order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0)
                            ->required()
                            ->helperText('Urutan tampilan cabang'),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true)
                            ->helperText('Hanya cabang aktif yang ditampilkan'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Cabang')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(50)
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon')
                    ->searchable(),

                Tables\Columns\TextColumn::make('order')
                    ->label('Urutan')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('order', 'asc')
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('Semua cabang')
                    ->trueLabel('Hanya aktif')
                    ->falseLabel('Hanya nonaktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
        ];
    }
}
