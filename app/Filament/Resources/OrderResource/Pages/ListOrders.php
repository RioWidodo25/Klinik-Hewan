<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Models\Setting;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        $showPetshopMenu = Setting::get('show_petshop_menu', '1') === '1';
        
        return [
            Actions\Action::make('toggle_menu_visibility')
                ->label($showPetshopMenu ? 'Sembunyikan Menu Petshop' : 'Tampilkan Menu Petshop')
                ->icon($showPetshopMenu ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                ->color($showPetshopMenu ? 'warning' : 'success')
                ->requiresConfirmation()
                ->modalHeading($showPetshopMenu ? 'Sembunyikan Menu Petshop?' : 'Tampilkan Menu Petshop?')
                ->modalDescription($showPetshopMenu 
                    ? 'Menu Petshop akan disembunyikan dari halaman user dan tidak dapat dilihat.'
                    : 'Menu Petshop akan ditampilkan kembali di halaman user.')
                ->modalSubmitActionLabel($showPetshopMenu ? 'Sembunyikan' : 'Tampilkan')
                ->action(function () {
                    $showPetshopMenu = Setting::get('show_petshop_menu', '1') === '1';
                    Setting::set('show_petshop_menu', $showPetshopMenu ? '0' : '1');
                    
                    \Filament\Notifications\Notification::make()
                        ->title($showPetshopMenu ? 'Menu Disembunyikan' : 'Menu Ditampilkan')
                        ->body($showPetshopMenu 
                            ? 'Menu Petshop sekarang disembunyikan dari halaman user.'
                            : 'Menu Petshop sekarang terlihat di halaman user.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
