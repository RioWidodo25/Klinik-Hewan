<?php

namespace App\Filament\Resources\AppointmentResource\Pages;

use App\Filament\Resources\AppointmentResource;
use App\Models\Setting;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAppointments extends ListRecords
{
    protected static string $resource = AppointmentResource::class;

    protected function getHeaderActions(): array
    {
        $showAppointmentMenu = Setting::get('show_appointment_menu', '1') === '1';
        
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('toggle_menu_visibility')
                ->label($showAppointmentMenu ? 'Sembunyikan Menu Janji Temu' : 'Tampilkan Menu Janji Temu')
                ->icon($showAppointmentMenu ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                ->color($showAppointmentMenu ? 'warning' : 'success')
                ->requiresConfirmation()
                ->modalHeading($showAppointmentMenu ? 'Sembunyikan Menu Janji Temu?' : 'Tampilkan Menu Janji Temu?')
                ->modalDescription($showAppointmentMenu 
                    ? 'Menu Janji Temu (Booking) akan disembunyikan dari halaman user dan tidak dapat diakses.'
                    : 'Menu Janji Temu (Booking) akan ditampilkan kembali di halaman user.')
                ->modalSubmitActionLabel($showAppointmentMenu ? 'Sembunyikan' : 'Tampilkan')
                ->action(function () {
                    $showAppointmentMenu = Setting::get('show_appointment_menu', '1') === '1';
                    Setting::set('show_appointment_menu', $showAppointmentMenu ? '0' : '1');
                    
                    \Filament\Notifications\Notification::make()
                        ->title($showAppointmentMenu ? 'Menu Disembunyikan' : 'Menu Ditampilkan')
                        ->body($showAppointmentMenu 
                            ? 'Menu Janji Temu sekarang disembunyikan dari halaman user.'
                            : 'Menu Janji Temu sekarang terlihat di halaman user.')
                        ->success()
                        ->send();
                }),
        ];
    }
    
    // Add polling interval for real-time updates
    public function getRefreshRate(): int
    {
        return 2000; // Refresh every 2 seconds (in milliseconds)
    }
    
    protected function getHeaderWidgets(): array
    {
        return [];
    }
    
    protected function getFooterWidgets(): array
    {
        return [];
    }
}
