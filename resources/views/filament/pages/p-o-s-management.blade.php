<x-filament-panels::page>
    <div class="space-y-4">
        <!-- Product List and Cart Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Products List (Left Side) -->
            <div class="lg:col-span-2 space-y-4" x-data="{ search: '' }">
                <!-- Search Bar -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <input 
                        type="text" 
                        x-model="search"
                        placeholder="Cari produk..." 
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-primary-500 focus:ring-primary-500"
                    />
                </div>

                <!-- Products Grid -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <h3 class="text-lg font-semibold mb-4">Daftar Produk</h3>
                    
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3 max-h-[600px] overflow-y-auto">
                        @foreach($products as $product)
                            <div x-show="search === '' || '{{ strtolower($product['name']) }}'.includes(search.toLowerCase()) || '{{ strtolower($product['category']) }}'.includes(search.toLowerCase()) || '{{ strtolower($product['sku'] ?? '') }}'.includes(search.toLowerCase())"
                                 class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 hover:shadow-xl hover:border-primary-500 hover:bg-primary-50 dark:hover:bg-gray-700 dark:hover:border-primary-400 transition-all duration-200 cursor-pointer"
                                 wire:click="addToCart({{ $product['id'] }}, {{ $product['has_variants'] ? 'null' : 'null' }})">
                                
                                <!-- Product Image -->
                                <div class="aspect-square bg-gray-100 dark:bg-gray-700 rounded-lg mb-2 overflow-hidden">
                                    @if($product['image'])
                                        <img src="{{ Storage::url($product['image']) }}" 
                                             alt="{{ $product['name'] }}" 
                                             class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Info -->
                                <div class="space-y-1">
                                    <h4 class="text-sm font-semibold line-clamp-2 min-h-[2.5rem]">{{ $product['name'] }}</h4>
                                    <p class="text-xs text-gray-500">{{ $product['category'] }}</p>
                                    <p class="text-sm font-bold text-primary-600">Rp {{ number_format($product['price'], 0, ',', '.') }}</p>
                                    <p class="text-xs {{ $product['stock'] > 10 ? 'text-green-600' : 'text-orange-600' }}">
                                        Stok: {{ $product['stock'] }}
                                    </p>
                                </div>

                                @if($product['has_variants'])
                                    <div class="mt-2">
                                        <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">Ada Varian</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Cart (Right Side) -->
            <div class="space-y-4">
                <!-- Customer Form -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    {{ $this->form }}
                </div>

                <!-- Cart Items -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Keranjang</h3>
                        @if(!empty($cart))
                            <button 
                                wire:click="clearCart" 
                                class="text-xs text-red-600 hover:text-red-700"
                            >
                                Kosongkan
                            </button>
                        @endif
                    </div>

                    <div class="space-y-3 max-h-[400px] overflow-y-auto">
                        @forelse($cart as $key => $item)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold">{{ $item['name'] }}</h4>
                                        <p class="text-xs text-gray-500">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                    </div>
                                    <button 
                                        wire:click="removeFromCart('{{ $key }}')"
                                        class="text-red-500 hover:text-red-700"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <button 
                                            wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] - 1 }})"
                                            class="w-6 h-6 rounded bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 flex items-center justify-center"
                                        >
                                            <span class="text-sm">−</span>
                                        </button>
                                        <span class="text-sm font-semibold w-8 text-center">{{ $item['quantity'] }}</span>
                                        <button 
                                            wire:click="updateQuantity('{{ $key }}', {{ $item['quantity'] + 1 }})"
                                            class="w-6 h-6 rounded bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 flex items-center justify-center"
                                        >
                                            <span class="text-sm">+</span>
                                        </button>
                                    </div>
                                    <p class="text-sm font-bold">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-500">
                                <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="text-sm">Keranjang kosong</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Total -->
                    @if(!empty($cart))
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex justify-between items-center mb-4">
                                <span class="text-lg font-bold">Total</span>
                                <span class="text-2xl font-bold text-primary-600">
                                    Rp {{ number_format($total, 0, ',', '.') }}
                                </span>
                            </div>

                            <button 
                                wire:click="processTransaction"
                                class="w-full bg-primary-600 hover:bg-primary-700 text-white font-bold py-3 px-4 rounded-lg transition"
                            >
                                Proses Transaksi
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Receipt Modal -->
    @if($showReceipt && $currentOrder)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" @click="$wire.closeReceipt()"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <!-- Receipt Content -->
                    <div id="receipt-content" class="bg-white p-8 text-gray-900">
                        <!-- Header -->
                        <div class="text-center border-b-2 border-dashed border-gray-300 pb-4 mb-4">
                            <h1 class="text-2xl font-bold text-gray-900">A2 VET</h1>
                            <p class="text-sm text-gray-600">Klinik Hewan & Pet Shop</p>
                            <p class="text-base font-bold text-gray-900 mt-2">STRUK PEMBELIAN</p>
                            @if($footerSettings)
                                <p class="text-xs text-gray-500 mt-1">{{ $footerSettings->contact_address ?? 'Alamat tidak tersedia' }}</p>
                                <p class="text-xs text-gray-500">Telp: {{ $footerSettings->contact_phone ?? '-' }}</p>
                            @else
                                <p class="text-xs text-gray-500 mt-1">Alamat tidak tersedia</p>
                                <p class="text-xs text-gray-500">Telp: -</p>
                            @endif
                        </div>

                        <!-- Transaction Info -->
                        <div class="mb-4 space-y-1">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">No. Order:</span>
                                <span class="font-semibold text-gray-900">{{ $currentOrder->order_number }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tanggal:</span>
                                <span class="text-gray-900">{{ $currentOrder->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Kasir:</span>
                                <span class="text-gray-900">{{ auth()->user()->name }}</span>
                            </div>
                        </div>

                        <!-- Customer Info -->
                        <div class="mb-4 pb-4 border-b border-gray-200">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Pelanggan:</span>
                                <span class="font-semibold text-gray-900">{{ $currentOrder->customer_name }}</span>
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
                                @foreach($currentOrder->items as $item)
                                    <tr class="border-b border-gray-200">
                                        <td class="py-2 text-sm text-gray-900">{{ $item->product_name }}</td>
                                        <td class="text-center py-2 text-sm text-gray-900">{{ $item->quantity }}</td>
                                        <td class="text-right py-2 text-sm text-gray-900">{{ number_format($item->price, 0, ',', '.') }}</td>
                                        <td class="text-right py-2 text-sm text-gray-900">{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <!-- Totals -->
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="text-gray-900">Rp {{ number_format($currentOrder->subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t-2 border-gray-300 pt-2">
                                <span class="text-gray-900">TOTAL:</span>
                                <span class="text-gray-900">Rp {{ number_format($currentOrder->total, 0, ',', '.') }}</span>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        @if($currentOrder->notes && str_contains($currentOrder->notes, 'Pembayaran:'))
                            <div class="mb-4 pb-4 border-b border-gray-200">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Metode Pembayaran:</span>
                                    <span class="font-semibold text-gray-900">
                                        @php
                                            preg_match('/Pembayaran: (.+?)(\n|$)/', $currentOrder->notes, $matches);
                                            echo $matches[1] ?? 'Tunai (Cash)';
                                        @endphp
                                    </span>
                                </div>
                            </div>
                        @endif

                        <!-- Notes -->
                        @if($currentOrder->notes && !str_contains($currentOrder->notes, 'Pembayaran:'))
                            <div class="mb-4 pb-4 border-b border-gray-200">
                                <p class="text-xs text-gray-600">
                                    Catatan: {{ preg_replace('/Pembayaran:.+?(\n|$)/', '', $currentOrder->notes) }}
                                </p>
                            </div>
                        @elseif($currentOrder->notes)
                            @php
                                $cleanNotes = trim(preg_replace('/Pembayaran:.+?(\n|$)/', '', $currentOrder->notes));
                            @endphp
                            @if($cleanNotes)
                                <div class="mb-4 pb-4 border-b border-gray-200">
                                    <p class="text-xs text-gray-600">Catatan: {{ $cleanNotes }}</p>
                                </div>
                            @endif
                        @endif

                        <!-- Footer -->
                        <div class="text-center border-t-2 border-dashed border-gray-300 pt-4">
                            <p class="text-sm font-semibold mb-2 text-gray-900">Terima Kasih Atas Kunjungan Anda!</p>
                            <p class="text-xs text-gray-500">Transaksi Kasir - Barang sudah dibeli</p>
                            <p class="text-xs text-gray-500 mt-1">Simpan struk ini sebagai bukti pembelian</p>
                            <p class="text-xs text-gray-600 mt-2">{{ now()->format('d/m/Y H:i:s') }}</p>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse print:hidden">
                        <a 
                            href="{{ route('filament.admin.pages.print-receipt', ['order' => $currentOrder->id]) }}"
                            target="_blank"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm"
                        >
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Struk
                        </a>
                        <button 
                            type="button" 
                            wire:click="closeReceipt"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:w-auto sm:text-sm"
                        >
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</x-filament-panels::page>
