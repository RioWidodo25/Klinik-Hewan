<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\FooterSetting;
use Filament\Pages\Page;

class PrintShippingLabel extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    
    protected static ?string $title = 'Cetak Resi Pengiriman';
    
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.print-shipping-label';
    
    public Order $order;
    public $footerSettings;
    
    public function mount(): void
    {
        $orderId = request()->query('order');
        
        if (!$orderId) {
            abort(404, 'Order ID tidak ditemukan');
        }
        
        $this->order = Order::with(['items.product', 'items.variant', 'user'])->findOrFail($orderId);
        $this->footerSettings = FooterSetting::first();
    }
}
