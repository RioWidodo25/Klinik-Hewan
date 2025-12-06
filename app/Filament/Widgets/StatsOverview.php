<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = now()->startOfDay();
        $startOfWeek = now()->startOfWeek();
        $startOfMonth = now()->startOfMonth();

        // Total Appointments Today
        $appointmentsToday = Appointment::whereDate('appointment_date', $today)->count();
        
        // Total Appointments This Week
        $appointmentsWeek = Appointment::whereBetween('appointment_date', [$startOfWeek, now()])->count();
        
        // Total Active Pets
        $activePets = Pet::where('is_active', true)->count();
        
        // Total Revenue This Month
        $revenueMonth = Order::where('status', 'completed')
            ->whereBetween('created_at', [$startOfMonth, now()])
            ->sum('total');

        return [
            Stat::make('Appointment Hari Ini', $appointmentsToday)
                ->description('Total booking hari ini')
                ->descriptionIcon('heroicon-m-calendar')
                ->color('success')
                ->chart([7, 12, 8, 15, 10, 18, $appointmentsToday]),
                
            Stat::make('Appointment Minggu Ini', $appointmentsWeek)
                ->description('Total booking minggu ini')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('info'),
                
            Stat::make('Total Pasien Aktif', $activePets)
                ->description('Hewan peliharaan terdaftar')
                ->descriptionIcon('heroicon-m-heart')
                ->color('warning'),
                
            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($revenueMonth, 0, ',', '.'))
                ->description('Dari penjualan produk')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
