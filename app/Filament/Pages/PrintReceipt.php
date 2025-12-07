<?php

namespace App\Filament\Pages;

use App\Models\Order;
use Filament\Pages\Page;

class PrintReceipt extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-printer';
    
    protected static ?string $title = 'Cetak Struk';
    
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.print-receipt';
    
    public Order $order;
    
    public function mount($order): void
    {
        $this->order = Order::with(['items.product', 'items.variant'])->findOrFail($order);
    }
}
