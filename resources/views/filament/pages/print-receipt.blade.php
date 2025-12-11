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
                    Cetak Struk
                </span>
            </button>
            
            <a 
                href="{{ route('filament.admin.pages.p-o-s-management') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded-lg"
            >
                Kembali ke POS
            </a>
        </div>

        <!-- Receipt -->
        <div class="bg-white p-4 rounded-lg shadow-lg print:shadow-none print:p-0" id="receipt">
            <!-- Header -->
            <div class="text-center border-b-2 border-dashed border-gray-800 pb-2 mb-2">
                <h1 class="text-lg font-bold">A2 VET</h1>
                <p class="text-xs">Klinik Hewan & Pet Shop</p>
                <p class="text-sm font-bold mt-1">STRUK PEMBELIAN</p>
                @if($footerSettings)
                    <p class="text-xs mt-1">{{ $footerSettings->contact_address ?? 'Alamat tidak tersedia' }}</p>
                    <p class="text-xs">Telp: {{ $footerSettings->contact_phone ?? '-' }}</p>
                @else
                    <p class="text-xs mt-1">Alamat tidak tersedia</p>
                    <p class="text-xs">Telp: -</p>
                @endif
            </div>

            <!-- Transaction Info -->
            <div class="mb-2 space-y-0.5 text-xs">
                <div class="flex justify-between">
                    <span>No. Order:</span>
                    <strong>{{ $order->order_number }}</strong>
                </div>
                <div class="flex justify-between">
                    <span>Tanggal:</span>
                    <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Kasir:</span>
                    <span>{{ auth()->user()->name }}</span>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="mb-2 pb-2 border-b border-gray-800 text-xs">
                <div class="flex justify-between">
                    <span>Pelanggan:</span>
                    <strong>{{ $order->customer_name }}</strong>
                </div>
                @if($order->customer_phone && $order->customer_phone !== '-')
                <div class="flex justify-between">
                    <span>Telepon:</span>
                    <span>{{ $order->customer_phone }}</span>
                </div>
                @endif
            </div>

            <!-- Items Table -->
            <table class="w-full mb-2 text-xs">
                <thead>
                    <tr class="border-b-2 border-gray-800">
                        <th class="text-left py-1">Item</th>
                        <th class="text-center py-1 w-12">Qty</th>
                        <th class="text-right py-1 w-16">Harga</th>
                        <th class="text-right py-1 w-20">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr class="border-b border-gray-300">
                            <td class="py-1 align-top">{{ $item->product_name }}</td>
                            <td class="text-center py-1 align-top">{{ $item->quantity }}</td>
                            <td class="text-right py-1 align-top">{{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-right py-1 align-top">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Totals -->
            <div class="space-y-1 mb-2 text-xs">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-sm border-t-2 border-gray-800 pt-1">
                    <span>TOTAL:</span>
                    <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Payment Method -->
            @if($order->notes && str_contains($order->notes, 'Pembayaran:'))
                <div class="mb-2 pb-2 border-b border-gray-800 text-xs">
                    <div class="flex justify-between">
                        <span>Pembayaran:</span>
                        <strong>
                            @php
                                preg_match('/Pembayaran: (.+?)(\n|$)/', $order->notes, $matches);
                                echo $matches[1] ?? 'Tunai';
                            @endphp
                        </strong>
                    </div>
                </div>
            @endif

            <!-- Notes -->
            @if($order->notes && !str_contains($order->notes, 'Pembayaran:'))
                <div class="mb-2 pb-2 border-b border-gray-800 text-xs">
                    <p><strong>Catatan:</strong></p>
                    <p class="mt-1">{{ preg_replace('/Pembayaran:.+?(\n|$)/', '', $order->notes) }}</p>
                </div>
            @elseif($order->notes)
                @php
                    $cleanNotes = trim(preg_replace('/Pembayaran:.+?(\n|$)/', '', $order->notes));
                @endphp
                @if($cleanNotes)
                    <div class="mb-2 pb-2 border-b border-gray-800 text-xs">
                        <p><strong>Catatan:</strong></p>
                        <p class="mt-1">{{ $cleanNotes }}</p>
                    </div>
                @endif
            @endif

            <!-- Footer -->
            <div class="text-center border-t-2 border-dashed border-gray-800 pt-2 text-xs">
                <p class="font-semibold mb-1">Terima Kasih!</p>
                <p class="text-xs">Transaksi Kasir - Barang sudah dibeli</p>
                <p class="text-xs mt-0.5">Simpan struk sebagai bukti pembelian</p>
                <p class="text-xs mt-1">{{ now()->format('d/m/Y H:i:s') }}</p>
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
            
            body * {
                visibility: hidden;
            }
            
            #receipt, #receipt * {
                visibility: visible;
            }
            
            #receipt {
                position: absolute;
                left: 0;
                top: 0;
                width: 80mm;
                margin: 0;
                padding: 3mm 5mm;
                box-shadow: none;
                font-family: 'Courier New', monospace;
                font-size: 9pt;
                line-height: 1.3;
            }
            
            #receipt h1 {
                font-size: 14pt;
                margin: 0;
            }
            
            #receipt .text-lg {
                font-size: 12pt;
            }
            
            #receipt .text-sm {
                font-size: 9pt;
            }
            
            #receipt .text-xs {
                font-size: 8pt;
            }
            
            #receipt table {
                font-size: 8pt;
            }
            
            #receipt .mb-2 {
                margin-bottom: 0.3rem;
            }
            
            #receipt .pb-2 {
                padding-bottom: 0.3rem;
            }
            
            #receipt .pt-2 {
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
