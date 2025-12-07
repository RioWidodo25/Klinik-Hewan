<x-filament-panels::page>
    <div class="space-y-4">
        <!-- Product List and Cart Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Products List (Left Side) -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Search Bar -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                    <input 
                        type="text" 
                        x-data="{ search: '' }"
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
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 hover:shadow-lg transition cursor-pointer"
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
</x-filament-panels::page>
