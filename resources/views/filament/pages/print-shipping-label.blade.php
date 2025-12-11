<x-filament-panels::page>
    <div class="max-w-2xl mx-auto">
        <!-- Print Button -->
        <div class="mb-4 flex gap-2 print:hidden">
            <button 
                onclick="window.print()" 
                class="bg-primary-600 hover:bg-primary-700 text-white font-bold py-2 px-4 rounded-lg"
            >
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Resi
                </span>
            </button>
            
            <a 
                href="{{ route('filament.admin.resources.orders.index') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg"
            >
                Kembali ke Pesanan
            </a>
        </div>

        <!-- Thermal Receipt -->
        <div class="bg-white p-4 print:p-0" id="thermal-receipt">
            <!-- Header -->
            <div class="text-center border-b-2 border-dashed border-gray-800 pb-2 mb-2">
                <h1 class="text-xl font-bold">A2 VET</h1>
                <p class="text-xs">Klinik Hewan & Pet Shop</p>
                <p class="text-sm font-bold mt-2">═══ RESI PENGIRIMAN ═══</p>
                <p class="text-xs mt-1">(PESANAN PETSHOP ONLINE)</p>
            </div>

            <!-- Order Info -->
            <div class="mb-3 text-xs space-y-1">
                <div class="flex justify-between">
                    <span>No. Order:</span>
                    <strong>{{ $order->order_number }}</strong>
                </div>
                <div class="flex justify-between">
                    <span>Tanggal:</span>
                    <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if($order->tracking_number)
                <div class="flex justify-between">
                    <span>No. Resi:</span>
                    <strong>{{ $order->tracking_number }}</strong>
                </div>
                @endif
            </div>

            <!-- Sender -->
            <div class="mb-3 pb-2 border-b border-gray-800 text-xs">
                <p class="font-bold mb-1">PENGIRIM:</p>
                <p class="font-bold">A2 VET</p>
                @if($footerSettings)
                    <p>{{ $footerSettings->contact_address ?? 'Alamat tidak tersedia' }}</p>
                    <p>Telp: {{ $footerSettings->contact_phone ?? '-' }}</p>
                @else
                    <p>Alamat tidak tersedia</p>
                    <p>Telp: -</p>
                @endif
            </div>

            <!-- Receiver -->
            <div class="mb-3 pb-2 border-b border-gray-800 text-xs">
                <p class="font-bold mb-1">PENERIMA:</p>
                @if(is_array($order->shipping_address))
                    <p class="font-bold">{{ $order->shipping_address['recipient_name'] ?? $order->customer_name }}</p>
                    <p>{{ $order->shipping_address['phone_number'] ?? $order->customer_phone }}</p>
                    <p>{{ $order->shipping_address['full_address'] ?? '' }}</p>
                    <p>{{ $order->shipping_address['city'] ?? $order->shipping_city }}, {{ $order->shipping_address['province'] ?? $order->shipping_province }}</p>
                    <p>{{ $order->shipping_address['postal_code'] ?? $order->shipping_postal_code }}</p>
                @else
                    <p class="font-bold">{{ $order->customer_name }}</p>
                    <p>{{ $order->customer_phone }}</p>
                    <p>{{ $order->shipping_city }}, {{ $order->shipping_province }}</p>
                    <p>{{ $order->shipping_postal_code }}</p>
                @endif
            </div>

            <!-- Items -->
            <div class="mb-3 pb-2 border-b border-gray-800">
                <p class="font-bold text-xs mb-2">DAFTAR PRODUK:</p>
                <table class="w-full text-xs">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="py-1 align-top">
                                {{ $item->product_name }}
                                @if($item->variant_name)
                                    <br><span class="text-gray-600">({{ $item->variant_name }})</span>
                                @endif
                            </td>
                            <td class="text-center py-1 align-top">x{{ $item->quantity }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>

            <!-- Package Info -->
            <div class="mb-3 pb-2 border-b border-gray-800 text-xs space-y-1">
                <div class="flex justify-between">
                    <span>Total Item:</span>
                    <strong>{{ $order->items->sum('quantity') }} Item</strong>
                </div>
                <div class="flex justify-between">
                    <span>Ongkir:</span>
                    <strong>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</strong>
                </div>
                <div class="flex justify-between">
                    <span>COD:</span>
                    <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong>
                </div>
            </div>

            <!-- Customer Notes -->
            @if($order->notes)
                <div class="mb-3 pb-2 border-b border-gray-800 text-xs">
                    <p class="font-bold mb-1">CATATAN PELANGGAN:</p>
                    <p class="leading-relaxed">{{ $order->notes }}</p>
                </div>
            @endif

            <!-- Footer -->
            <div class="text-center text-xs mt-4 pt-2 border-t border-dashed border-gray-800">
                <p class="font-bold mb-1">PESANAN PETSHOP ONLINE</p>
                <p>Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
                <p class="mt-1">Terima kasih atas pembelian Anda</p>
                <p class="text-xs mt-1">Periksa barang sebelum menerima paket</p>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            
            @page {
                size: 80mm auto;
                margin: 0;
            }
            
            /* Hide everything except receipt */
            body * {
                visibility: hidden;
            }
            
            #thermal-receipt,
            #thermal-receipt * {
                visibility: visible;
            }
            
            #thermal-receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 80mm;
                margin: 0;
                padding: 3mm 5mm;
                font-family: 'Courier New', monospace;
                font-size: 9pt;
                line-height: 1.3;
            }
            
            #thermal-receipt h1 {
                font-size: 14pt;
                margin: 0;
            }
            
            #thermal-receipt .text-xl {
                font-size: 14pt;
            }
            
            #thermal-receipt .text-sm {
                font-size: 10pt;
            }
            
            #thermal-receipt .text-xs {
                font-size: 8pt;
            }
            
            #thermal-receipt table {
                font-size: 8pt;
            }
            
            #thermal-receipt .mb-3 {
                margin-bottom: 0.4rem;
            }
            
            #thermal-receipt .pb-2 {
                padding-bottom: 0.3rem;
            }
            
            #thermal-receipt .pt-2 {
                padding-top: 0.3rem;
            }
            
            .print\:hidden {
                display: none !important;
            }
            
            .print\:p-0 {
                padding: 0 !important;
            }
        }
    </style>
</x-filament-panels::page>
