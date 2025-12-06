<?php

namespace App\Filament\Widgets;

use App\Models\Appointment;
use Filament\Widgets\ChartWidget;

class AppointmentStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Status Appointment';
    
    protected static ?int $sort = 4;

    protected function getData(): array
    {
        $pending = Appointment::where('status', 'pending')->count();
        $confirmed = Appointment::where('status', 'confirmed')->count();
        $completed = Appointment::where('status', 'completed')->count();
        $cancelled = Appointment::where('status', 'cancelled')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Status',
                    'data' => [$pending, $confirmed, $completed, $cancelled],
                    'backgroundColor' => [
                        'rgb(234, 179, 8)',   // warning - pending
                        'rgb(59, 130, 246)',  // info - confirmed
                        'rgb(34, 197, 94)',   // success - completed
                        'rgb(239, 68, 68)',   // danger - cancelled
                    ],
                ],
            ],
            'labels' => ['Menunggu', 'Dikonfirmasi', 'Selesai', 'Dibatalkan'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
