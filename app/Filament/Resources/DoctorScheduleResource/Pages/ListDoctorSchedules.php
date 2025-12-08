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
        return [
            Actions\Action::make('toggle_menu_visibility')
                ->label(fn () => Setting::get('show_doctor_schedule_menu', '1') === '1' 
                    ? 'Sembunyikan Menu dari User' 
                    : 'Tampilkan Menu ke User')
                ->icon(fn () => Setting::get('show_doctor_schedule_menu', '1') === '1' 
                    ? 'heroicon-o-eye-slash' 
                    : 'heroicon-o-eye')
                ->color(fn () => Setting::get('show_doctor_schedule_menu', '1') === '1' 
                    ? 'warning' 
                    : 'success')
                ->outlined()
                ->tooltip(fn () => Setting::get('show_doctor_schedule_menu', '1') === '1'
                    ? 'Klik untuk menyembunyikan menu "Jadwal Dokter" dari navigation bar user'
                    : 'Klik untuk menampilkan menu "Jadwal Dokter" di navigation bar user')
                ->requiresConfirmation()
                ->modalHeading(fn () => Setting::get('show_doctor_schedule_menu', '1') === '1' 
                    ? 'Sembunyikan Menu Jadwal Dokter?' 
                    : 'Tampilkan Menu Jadwal Dokter?')
                ->modalDescription(fn () => Setting::get('show_doctor_schedule_menu', '1') === '1'
                    ? 'Menu "Jadwal Dokter" akan hilang dari dropdown "About Us" di navbar user. Halaman tetap bisa diakses via URL langsung.'
                    : 'Menu "Jadwal Dokter" akan muncul kembali di dropdown "About Us" di navbar user.')
                ->modalSubmitActionLabel(fn () => Setting::get('show_doctor_schedule_menu', '1') === '1' 
                    ? 'Sembunyikan Menu' 
                    : 'Tampilkan Menu')
                ->action(function () {
                    $currentValue = Setting::get('show_doctor_schedule_menu', '1');
                    $newValue = $currentValue === '1' ? '0' : '1';
                    Setting::set('show_doctor_schedule_menu', $newValue);
                    
                    Notification::make()
                        ->title($newValue === '0' ? 'Menu Disembunyikan' : 'Menu Ditampilkan')
                        ->body($newValue === '0'
                            ? 'Menu "Jadwal Dokter" sekarang disembunyikan dari navigation bar user.'
                            : 'Menu "Jadwal Dokter" sekarang terlihat di navigation bar user.')
                        ->success()
                        ->send();
                }),
            
            Actions\CreateAction::make(),
        ];
    }
}
