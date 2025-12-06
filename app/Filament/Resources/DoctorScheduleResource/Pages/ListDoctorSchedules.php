<?php

namespace App\Filament\Resources\DoctorScheduleResource\Pages;

use App\Filament\Resources\DoctorScheduleResource;
use App\Models\Setting;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListDoctorSchedules extends ListRecords
{
    protected static string $resource = DoctorScheduleResource::class;

    protected function getHeaderActions(): array
    {
        $menuVisible = Setting::get('show_doctor_schedule_menu', '1') === '1';

        return [
            Actions\Action::make('toggle_menu_visibility')
                ->label($menuVisible ? 'Sembunyikan Menu dari User' : 'Tampilkan Menu ke User')
                ->icon($menuVisible ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                ->color($menuVisible ? 'warning' : 'success')
                ->outlined()
                ->tooltip($menuVisible 
                    ? 'Klik untuk menyembunyikan menu "Jadwal Dokter" dari navigation bar user'
                    : 'Klik untuk menampilkan menu "Jadwal Dokter" di navigation bar user')
                ->requiresConfirmation()
                ->modalHeading($menuVisible ? 'Sembunyikan Menu Jadwal Dokter?' : 'Tampilkan Menu Jadwal Dokter?')
                ->modalDescription($menuVisible 
                    ? 'Menu "Jadwal Dokter" akan hilang dari dropdown "About Us" di navbar user. Halaman tetap bisa diakses via URL langsung.'
                    : 'Menu "Jadwal Dokter" akan muncul kembali di dropdown "About Us" di navbar user.')
                ->modalSubmitActionLabel($menuVisible ? 'Sembunyikan Menu' : 'Tampilkan Menu')
                ->action(function () use ($menuVisible) {
                    Setting::set('show_doctor_schedule_menu', $menuVisible ? '0' : '1');
                    
                    Notification::make()
                        ->title($menuVisible ? 'Menu Disembunyikan' : 'Menu Ditampilkan')
                        ->body($menuVisible 
                            ? 'Menu "Jadwal Dokter" sekarang disembunyikan dari navigation bar user.'
                            : 'Menu "Jadwal Dokter" sekarang terlihat di navigation bar user.')
                        ->success()
                        ->send();
                    
                    // Refresh page to update button state
                    redirect()->route('filament.admin.resources.doctor-schedules.index');
                }),
            
            Actions\CreateAction::make(),
        ];
    }
}
