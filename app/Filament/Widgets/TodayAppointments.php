<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TodayAppointments extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Appointment::query()
                    ->whereDate('appointment_date', now())
                    ->with(['user', 'pet', 'doctor', 'services'])
                    ->orderBy('appointment_time', 'asc')
            )
            ->heading('Appointment Hari Ini')
            ->columns([
                Tables\Columns\TextColumn::make('booking_code')
                    ->label('Kode Booking')
                    ->searchable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('appointment_time')
                    ->label('Waktu')
                    ->time('H:i')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pasien')
                    ->searchable(),

                Tables\Columns\TextColumn::make('pet.name')
                    ->label('Hewan')
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('Dokter')
                    ->searchable(),

                Tables\Columns\TextColumn::make('services.title')
                    ->label('Layanan')
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->expandableLimitedList(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'in_progress' => 'primary',
                        'completed' => 'success',
                        'cancelled' => 'danger',
                        'no_show' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'confirmed' => 'Dikonfirmasi',
                        'in_progress' => 'Berlangsung',
                        'completed' => 'Selesai',
                        'cancelled' => 'Dibatalkan',
                        'no_show' => 'Tidak Hadir',
                        default => $state,
                    }),
            ])
            ->defaultSort('appointment_time', 'asc');
    }
}
