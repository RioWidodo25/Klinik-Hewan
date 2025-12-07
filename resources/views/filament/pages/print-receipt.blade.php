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
        <div class="bg-white p-8 rounded-lg shadow-lg print:shadow-none" id="receipt">
            <!-- Header -->
            <div class="text-center border-b-2 border-dashed border-gray-300 pb-4 mb-4">
                <h1 class="text-2xl font-bold">A2 VET</h1>
                <p class="text-sm text-gray-600">Klinik Hewan & Pet Shop</p>
                <p class="text-xs text-gray-500 mt-1">Jl. Contoh No. 123, Jakarta</p>
                <p class="text-xs text-gray-500">Telp: (021) 1234-5678</p>
            </div>

            <!-- Transaction Info -->
            <div class="mb-4 space-y-1">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">No. Order:</span>
                    <span class="font-semibold">{{ $order->order_number }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Tanggal:</span>
                    <span>{{ $order->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Kasir:</span>
                    <span>{{ auth()->user()->name }}</span>
                </div>
            </div>

            <!-- Customer Info -->
            <div class="mb-4 pb-4 border-b border-gray-200">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Pelanggan:</span>
                    <span class="font-semibold">{{ $order->customer_name }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Telepon:</span>
                    <span>{{ $order->customer_phone }}</span>
                </div>
            </div>

            <!-- Items Table -->
            <table class="w-full mb-4">
                <thead>
                    <tr class="border-b-2 border-gray-300">
                        <th class="text-left py-2 text-sm">Item</th>
                        <th class="text-center py-2 text-sm">Qty</th>
                        <th class="text-right py-2 text-sm">Harga</th>
                        <th class="text-right py-2 text-sm">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                        <tr class="border-b border-gray-200">
                            <td class="py-2 text-sm">{{ $item->product_name }}</td>
                            <td class="text-center py-2 text-sm">{{ $item->quantity }}</td>
                            <td class="text-right py-2 text-sm">{{ number_format($item->price, 0, ',', '.') }}</td>
                            <td class="text-right py-2 text-sm">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Totals -->
            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal:</span>
                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold border-t-2 border-gray-300 pt-2">
                    <span>TOTAL:</span>
                    <span>Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Notes -->
            @if($order->notes)
                <div class="mb-4 pb-4 border-b border-gray-200">
                    <p class="text-xs text-gray-600">Catatan: {{ $order->notes }}</p>
                </div>
            @endif

            <!-- Footer -->
            <div class="text-center border-t-2 border-dashed border-gray-300 pt-4">
                <p class="text-sm font-semibold mb-2">Terima Kasih Atas Kunjungan Anda!</p>
                <p class="text-xs text-gray-500">Barang yang sudah dibeli tidak dapat dikembalikan</p>
                <p class="text-xs text-gray-500 mt-1">Simpan struk ini sebagai bukti pembelian</p>
            </div>
        </div>
    </div>

    <style>
        @media print {
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
                width: 100%;
                box-shadow: none;
            }
            .print\:hidden {
                display: none !important;
            }
        }
    </style>
</x-filament-panels::page>
