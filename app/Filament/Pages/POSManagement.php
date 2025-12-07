<?php

namespace App\Filament\Pages;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\FooterSetting;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class POSManagement extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    
    protected static ?string $navigationLabel = 'POS (Kasir)';
    
    protected static ?string $title = 'Point of Sale - Kasir';
    
    protected static ?string $navigationGroup = 'Petshop';
    
    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.p-o-s-management';

    public ?array $data = [];
    
    public $products = [];
    public $cart = [];
    public $total = 0;
    public $showReceipt = false;
    public $currentOrder = null;
    public $footerSettings = null;

    public function mount(): void
    {
        $this->form->fill([
            'customer_name' => '',
            'notes' => '',
            'payment_method' => 'cash',
        ]);
        
        $this->footerSettings = FooterSetting::first();
        $this->loadProducts();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pelanggan')
                    ->schema([
                        TextInput::make('customer_name')
                            ->label('Nama Pelanggan')
                            ->required(),
                        
                        Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(2),
                    ]),
                
                Section::make('Metode Pembayaran')
                    ->schema([
                        Select::make('payment_method')
                            ->label('Metode Pembayaran')
                            ->options([
                                'cash' => 'Tunai (Cash)',
                                'qris' => 'QRIS',
                                'transfer' => 'Transfer Bank',
                                'debit' => 'Kartu Debit',
                            ])
                            ->default('cash')
                            ->required(),
                    ]),
            ])
            ->statePath('data');
    }

    public function loadProducts(): void
    {
        $this->products = Product::with(['variants', 'images', 'category'])
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderBy('name')
            ->get()
            ->map(function ($product) {
                $hasVariants = $product->variants->where('is_active', true)->count() > 0;
                
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category?->name ?? 'Tanpa Kategori',
                    'price' => $product->price,
                    'stock' => $product->stock,
                    'sku' => $product->sku,
                    'image' => $product->images->first()?->image_path ?? null,
                    'has_variants' => $hasVariants,
                    'variants' => $hasVariants ? $product->variants->where('is_active', true)->map(function ($variant) {
                        return [
                            'id' => $variant->id,
                            'name' => $variant->name,
                            'price' => $variant->price,
                            'stock' => $variant->stock,
                            'size' => $variant->size,
                            'color' => $variant->color,
                        ];
                    })->values() : [],
                ];
            })
            ->toArray();
    }

    public function addToCart($productId, $variantId = null): void
    {
        $product = Product::with('variants')->find($productId);
        
        if (!$product) {
            Notification::make()
                ->danger()
                ->title('Produk tidak ditemukan')
                ->send();
            return;
        }

        $cartKey = $variantId ? "variant_{$variantId}" : "product_{$productId}";
        
        if ($variantId) {
            $variant = ProductVariant::find($variantId);
            
            if (!$variant || $variant->stock <= 0) {
                Notification::make()
                    ->danger()
                    ->title('Varian tidak tersedia atau stok habis')
                    ->send();
                return;
            }
            
            $currentQty = $this->cart[$cartKey]['quantity'] ?? 0;
            
            if ($currentQty >= $variant->stock) {
                Notification::make()
                    ->warning()
                    ->title('Stok tidak mencukupi')
                    ->body("Stok tersisa: {$variant->stock}")
                    ->send();
                return;
            }
            
            $this->cart[$cartKey] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'name' => $product->name . ' - ' . $variant->name,
                'price' => $variant->price,
                'quantity' => $currentQty + 1,
                'stock' => $variant->stock,
                'subtotal' => ($currentQty + 1) * $variant->price,
            ];
        } else {
            if ($product->stock <= 0) {
                Notification::make()
                    ->danger()
                    ->title('Stok habis')
                    ->send();
                return;
            }
            
            $currentQty = $this->cart[$cartKey]['quantity'] ?? 0;
            
            if ($currentQty >= $product->stock) {
                Notification::make()
                    ->warning()
                    ->title('Stok tidak mencukupi')
                    ->body("Stok tersisa: {$product->stock}")
                    ->send();
                return;
            }
            
            $this->cart[$cartKey] = [
                'product_id' => $productId,
                'variant_id' => null,
                'name' => $product->name,
                'price' => $product->price,
                'quantity' => $currentQty + 1,
                'stock' => $product->stock,
                'subtotal' => ($currentQty + 1) * $product->price,
            ];
        }
        
        $this->calculateTotal();
        
        Notification::make()
            ->success()
            ->title('Ditambahkan ke keranjang')
            ->send();
    }

    public function updateQuantity($cartKey, $quantity): void
    {
        if ($quantity <= 0) {
            unset($this->cart[$cartKey]);
        } else {
            if ($quantity > $this->cart[$cartKey]['stock']) {
                Notification::make()
                    ->warning()
                    ->title('Stok tidak mencukupi')
                    ->send();
                return;
            }
            
            $this->cart[$cartKey]['quantity'] = $quantity;
            $this->cart[$cartKey]['subtotal'] = $quantity * $this->cart[$cartKey]['price'];
        }
        
        $this->calculateTotal();
    }

    public function removeFromCart($cartKey): void
    {
        unset($this->cart[$cartKey]);
        $this->calculateTotal();
        
        Notification::make()
            ->success()
            ->title('Item dihapus dari keranjang')
            ->send();
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->calculateTotal();
        
        Notification::make()
            ->success()
            ->title('Keranjang dikosongkan')
            ->send();
    }

    public function calculateTotal(): void
    {
        $this->total = array_sum(array_column($this->cart, 'subtotal'));
    }

    public function processTransaction(): void
    {
        if (empty($this->cart)) {
            Notification::make()
                ->danger()
                ->title('Keranjang kosong')
                ->body('Tambahkan produk terlebih dahulu')
                ->send();
            return;
        }
        
        $data = $this->form->getState();
        
        try {
            DB::beginTransaction();
            
            // Generate order number
            $orderNumber = 'POS-' . now()->format('Ymd') . '-' . str_pad(Order::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);
            
            // Create order with payment method info
            $paymentMethods = [
                'cash' => 'Tunai (Cash)',
                'qris' => 'QRIS',
                'transfer' => 'Transfer Bank',
                'debit' => 'Kartu Debit',
            ];
            $paymentMethod = $paymentMethods[$data['payment_method']] ?? 'Tunai (Cash)';
            
            $notes = $data['notes'] ?? '';
            if ($notes) {
                $notes .= "\n";
            }
            $notes .= "Pembayaran: " . $paymentMethod;
            
            $order = Order::create([
                'user_id' => auth()->id(),
                'order_number' => $orderNumber,
                'customer_name' => $data['customer_name'],
                'customer_phone' => '-',
                'customer_email' => 'pos@local.store',
                'shipping_address' => 'Pembelian Langsung di Toko',
                'shipping_city' => 'Toko',
                'shipping_province' => 'Lokal',
                'shipping_postal_code' => '00000',
                'subtotal' => $this->total,
                'shipping_cost' => 0,
                'total' => $this->total,
                'status' => 'delivered',
                'payment_status' => 'paid',
                'notes' => $notes,
                'paid_at' => now(),
                'delivered_at' => now(),
            ]);
            
            // Create order items and update stock
            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['variant_id'],
                    'product_name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                ]);
                
                // Update stock
                if ($item['variant_id']) {
                    $variant = ProductVariant::find($item['variant_id']);
                    $variant->decrement('stock', $item['quantity']);
                } else {
                    $product = Product::find($item['product_id']);
                    $product->decrement('stock', $item['quantity']);
                }
            }
            
            DB::commit();
            
            // Clear cart
            $this->cart = [];
            $this->total = 0;
            $this->form->fill([
                'customer_name' => '',
                'notes' => '',
                'payment_method' => 'cash',
            ]);
            
            // Reload products to update stock
            $this->loadProducts();
            
            // Show receipt modal
            $this->currentOrder = $order->load(['items.product', 'items.variant']);
            $this->showReceipt = true;
            
            Notification::make()
                ->success()
                ->title('Transaksi berhasil!')
                ->body("Nomor Order: {$orderNumber}")
                ->send();
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            Notification::make()
                ->danger()
                ->title('Transaksi gagal')
                ->body($e->getMessage())
                ->send();
        }
    }

    public function closeReceipt(): void
    {
        $this->showReceipt = false;
        $this->currentOrder = null;
    }

    public function printReceipt(): void
    {
        // Method for triggering print from Livewire
    }

    protected function getViewData(): array
    {
        return [
            'footerSettings' => $this->footerSettings ?? FooterSetting::first(),
        ];
    }
}
