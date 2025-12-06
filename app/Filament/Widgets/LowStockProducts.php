<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LowStockProducts extends BaseWidget
{
    protected static ?int $sort = 5;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->where('stock', '<=', 10)
                    ->where('stock', '>', 0)
                    ->orderBy('stock', 'asc')
            )
            ->heading('Produk Stok Rendah')
            ->columns([
                Tables\Columns\ImageColumn::make('primary_image.image_path')
                    ->label('Gambar')
                    ->disk('public')
                    ->size(40)
                    ->defaultImageUrl(url('/images/no-image.png')),
                    
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Produk')
                    ->searchable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable()
                    ->copyable(),
                    
                Tables\Columns\TextColumn::make('stock')
                    ->label('Stok')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state <= 5 => 'danger',
                        $state <= 10 => 'warning',
                        default => 'success',
                    }),
                    
                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->badge()
                    ->color('info'),
            ])
            ->defaultSort('stock', 'asc')
            ->emptyStateHeading('Semua Produk Stok Aman')
            ->emptyStateDescription('Tidak ada produk dengan stok rendah.');
    }
}
