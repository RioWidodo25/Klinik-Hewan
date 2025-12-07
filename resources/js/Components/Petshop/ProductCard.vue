<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    showAddButton: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['add-to-cart']);

const placeholderImage = 'https://ui-avatars.com/api/?name=Petshop&background=EBF4FF&color=7F9CF5';

const primaryImage = computed(() => props.product.primary_image_url || placeholderImage);
const isLoggedIn = computed(() => !!page.props.auth.user);
const isFavorited = computed(() => {
    const favIds = page.props.favoriteProductIds || [];
    return favIds.includes(props.product.id);
});

const isTogglingFavorite = ref(false);

const toggleFavorite = async () => {
    if (!isLoggedIn.value) {
        router.visit(route('login'));
        return;
    }

    isTogglingFavorite.value = true;

    router.post(
        route('profile.favorites.toggle', props.product.id),
        {},
        {
            preserveScroll: true,
            onFinish: () => {
                isTogglingFavorite.value = false;
            },
        }
    );
};

const formatCurrency = (value) => {
    if (value === null || value === undefined) {
        return 'Rp0';
    }

    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value);
};

// Modal state
const showModal = ref(false);
const quantity = ref(1);
const selectedVariantId = ref(null);
const isAdding = ref(false);

const openAddToCartModal = () => {
    if (!isLoggedIn.value) {
        router.visit(route('login'));
        return;
    }
    
    // Reset state
    quantity.value = 1;
    selectedVariantId.value = props.product.has_variants && props.product.variants?.length === 1 
        ? props.product.variants[0].id 
        : null;
    showModal.value = true;
};

const selectedVariant = computed(() => 
    props.product.variants?.find(v => v.id === selectedVariantId.value) ?? null
);

const displayPrice = computed(() => {
    if (selectedVariant.value) {
        return selectedVariant.value.final_price || props.product.price;
    }
    return props.product.price;
});

const maxQuantity = computed(() => {
    const stock = selectedVariant.value ? selectedVariant.value.stock : props.product.stock;
    return Math.max(stock || 0, 0);
});

const addToCart = () => {
    if (props.product.has_variants && !selectedVariantId.value) {
        return;
    }

    isAdding.value = true;
    
    router.post(route('petshop.cart.items.store'), {
        product_id: props.product.id,
        variant_id: selectedVariantId.value,
        quantity: quantity.value,
    }, {
        preserveScroll: true,
        onFinish: () => {
            isAdding.value = false;
            showModal.value = false;
        },
    });
};
</script>

<template>
    <Link
        :href="route('petshop.product.show', product.slug)"
        class="group relative flex h-full flex-col overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl dark:border-gray-700/60 dark:bg-gray-800"
    >
        <div class="relative aspect-square overflow-hidden">
            <img
                :src="primaryImage"
                :alt="product.name"
                class="h-full w-full object-cover transition duration-500 group-hover:scale-105"
            >

            <!-- Favorite Button -->
            <button
                type="button"
                @click.prevent="toggleFavorite"
                :disabled="isTogglingFavorite"
                class="absolute left-2 top-2 z-10 rounded-full bg-white/90 p-1.5 shadow-md transition hover:scale-110 hover:bg-white disabled:opacity-50 dark:bg-gray-800/90 dark:hover:bg-gray-800"
                :class="isFavorited ? 'text-red-500' : 'text-gray-400 hover:text-red-500'"
            >
                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" :fill="isFavorited ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
            </button>

            <div v-if="product.discount_percentage" class="absolute right-2 top-2 rounded-full bg-amber-500 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white shadow">
                -{{ product.discount_percentage }}%
            </div>

            <div v-if="product.is_featured" class="absolute bottom-2 right-2 flex items-center gap-0.5 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-amber-700 shadow dark:bg-amber-900/50 dark:text-amber-200">
                <svg class="size-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111 5.518.403a.562.562 0 01.318.986l-4.204 3.602 1.285 5.385a.562.562 0 01-.84.61L12 16.902l-4.722 2.694a.562.562 0 01-.84-.61l1.285-5.386-4.204-3.6a.562.562 0 01.318-.986l5.518-.403 2.125-5.112z" />
                </svg>
                Unggulan
            </div>
        </div>

        <div class="flex flex-1 flex-col p-2">
            <div class="flex items-center justify-between text-[9px] uppercase tracking-wide text-amber-600 dark:text-amber-300">
                <span v-if="product.category?.name" class="truncate">
                    {{ product.category.name }}
                </span>
                <span v-if="product.rating_average" class="flex items-center gap-0.5 text-gray-500 dark:text-gray-300">
                    <svg class="size-2.5 text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111 5.518.403a.562.562 0 01.318.986l-4.204 3.602 1.285 5.385a.562.562 0 01-.84.61L12 16.902l-4.722 2.694a.562.562 0 01-.84-.61l1.285-5.386-4.204-3.6a.562.562 0 01.318-.986l5.518-.403 2.125-5.112z" />
                    </svg>
                    {{ Number(product.rating_average).toFixed(1) }}
                </span>
            </div>

            <h3 class="mt-1 line-clamp-2 text-xs font-semibold text-gray-900 transition-colors duration-200 group-hover:text-amber-600 dark:text-white dark:group-hover:text-amber-300">
                {{ product.name }}
            </h3>

            <div class="mt-1.5 flex items-baseline gap-1">
                <span class="text-sm font-bold text-amber-600 dark:text-amber-400">
                    {{ formatCurrency(product.price) }}
                </span>
                <span
                    v-if="product.compare_price && product.compare_price > product.price"
                    class="text-[10px] text-gray-400 line-through dark:text-gray-500"
                >
                    {{ formatCurrency(product.compare_price) }}
                </span>
            </div>

            <p class="mt-1 text-[10px] text-gray-500 dark:text-gray-300">
                Stok: <span class="font-medium text-gray-700 dark:text-gray-200">{{ product.stock }}</span>
            </p>

            <!-- Rating and Sales -->
            <div v-if="product.rating_average || product.order_count" class="mt-1 flex items-center gap-1.5 text-[9px] text-gray-600 dark:text-gray-400">
                <div v-if="product.rating_average && product.review_count > 0" class="flex items-center gap-0.5 rounded-full bg-gray-50 px-1.5 py-0.5 dark:bg-gray-700/50">
                    <svg class="size-2.5 text-amber-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111 5.518.403a.562.562 0 01.318.986l-4.204 3.602 1.285 5.385a.562.562 0 01-.84.61L12 16.902l-4.722 2.694a.562.562 0 01-.84-.61l1.285-5.386-4.204-3.6a.562.562 0 01.318-.986l5.518-.403 2.125-5.112z" />
                    </svg>
                    <span class="font-medium text-gray-700 dark:text-gray-300">{{ Number(product.rating_average).toFixed(1) }}</span>
                    <span class="text-gray-400 dark:text-gray-500">({{ product.review_count }})</span>
                </div>
                <span v-if="product.order_count > 0" class="text-gray-500 dark:text-gray-400">
                    <span v-if="product.rating_average && product.review_count > 0">·</span> {{ product.order_count }}x
                </span>
            </div>

            <div v-if="showAddButton" class="mt-auto flex flex-col gap-1.5 pt-2">
                <button
                    type="button"
                    @click.prevent="openAddToCartModal"
                    class="inline-flex items-center justify-center rounded-lg bg-amber-500 px-2 py-1 text-[10px] font-semibold text-white shadow transition hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900"
                >
                    <svg class="me-1 size-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437m0 0 1.35 5.068m1.65 6.187h9.75m-9.75 0a2.25 2.25 0 11-4.5 0m4.5 0a2.25 2.25 0 01-4.5 0m14.25 0a2.25 2.25 0 11-4.5 0m4.5 0a2.25 2.25 0 01-4.5 0m0 0H7.125m12.75-7.875-1.064 4.256a1.125 1.125 0 01-1.09.844H8.978a1.125 1.125 0 01-1.09-.876l-1.148-4.599M7.5 6.75h13.125" />
                    </svg>
                    + Keranjang
                </button>
            </div>
        </div>
    </Link>

    <!-- Add to Cart Modal -->
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showModal = false">
                <Transition
                    enter-active-class="transition ease-out duration-200"
                    enter-from-class="opacity-0 translate-y-4 scale-95"
                    enter-to-class="opacity-100 translate-y-0 scale-100"
                    leave-active-class="transition ease-in duration-150"
                    leave-from-class="opacity-100 translate-y-0 scale-100"
                    leave-to-class="opacity-0 translate-y-4 scale-95"
                >
                    <div v-if="showModal" class="relative w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl dark:bg-gray-800">
                        <!-- Close Button -->
                        <button
                            type="button"
                            @click="showModal = false"
                            class="absolute right-4 top-4 rounded-lg p-1 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-300"
                        >
                            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>

                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Tambah ke Keranjang</h3>

                        <div class="flex gap-4 mb-6">
                            <!-- Product Image -->
                            <div class="h-24 w-24 flex-shrink-0 overflow-hidden rounded-xl bg-gray-100 dark:bg-gray-700">
                                <img :src="primaryImage" :alt="product.name" class="h-full w-full object-cover">
                            </div>

                            <!-- Product Info -->
                            <div class="flex-1">
                                <h4 class="font-semibold text-gray-900 dark:text-white line-clamp-2">{{ product.name }}</h4>
                                <p class="mt-1 text-lg font-bold text-amber-600 dark:text-amber-400">{{ formatCurrency(displayPrice) }}</p>
                                <p v-if="maxQuantity > 0" class="text-xs text-gray-500 dark:text-gray-400">Stok: {{ maxQuantity }}</p>
                            </div>
                        </div>

                        <!-- Variants Selection -->
                        <div v-if="product.has_variants && product.variants" class="mb-4">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Pilih Varian</label>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    v-for="variant in product.variants"
                                    :key="variant.id"
                                    type="button"
                                    @click="selectedVariantId = variant.id"
                                    :disabled="variant.stock === 0"
                                    :class="[
                                        'rounded-lg border px-3 py-2 text-sm font-medium transition',
                                        selectedVariantId === variant.id
                                            ? 'border-amber-500 bg-amber-50 text-amber-600 dark:bg-amber-900/40 dark:text-amber-200'
                                            : 'border-gray-200 text-gray-600 hover:border-amber-300 dark:border-gray-700 dark:text-gray-300',
                                        variant.stock === 0 ? 'cursor-not-allowed opacity-50' : ''
                                    ]"
                                >
                                    {{ variant.name || variant.size || variant.color }}
                                </button>
                            </div>
                            <p v-if="product.has_variants && !selectedVariantId" class="mt-2 text-xs text-red-500">Pilih varian terlebih dahulu</p>
                        </div>

                        <!-- Quantity Selector -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Jumlah</label>
                            <div class="flex items-center gap-3">
                                <button
                                    type="button"
                                    @click="quantity = Math.max(1, quantity - 1)"
                                    class="flex size-8 items-center justify-center rounded-lg border border-gray-300 text-gray-600 transition hover:bg-gray-100 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                                >
                                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                    </svg>
                                </button>
                                <input
                                    v-model.number="quantity"
                                    type="number"
                                    min="1"
                                    :max="maxQuantity"
                                    class="w-16 rounded-lg border border-gray-300 px-3 py-1 text-center text-sm font-semibold dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                >
                                <button
                                    type="button"
                                    @click="quantity = Math.min(maxQuantity, quantity + 1)"
                                    :disabled="quantity >= maxQuantity"
                                    class="flex size-8 items-center justify-center rounded-lg border border-gray-300 text-gray-600 transition hover:bg-gray-100 disabled:cursor-not-allowed disabled:opacity-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                                >
                                    <svg class="size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m7-7H5" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex gap-3">
                            <button
                                type="button"
                                @click="showModal = false"
                                class="flex-1 rounded-xl border border-gray-300 px-4 py-2 font-semibold text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700"
                            >
                                Batal
                            </button>
                            <button
                                type="button"
                                @click="addToCart"
                                :disabled="isAdding || maxQuantity === 0 || (product.has_variants && !selectedVariantId)"
                                class="flex-1 rounded-xl bg-amber-500 px-4 py-2 font-semibold text-white transition hover:bg-amber-600 disabled:cursor-not-allowed disabled:opacity-50"
                            >
                                {{ isAdding ? 'Menambahkan...' : 'Tambah ke Keranjang' }}
                            </button>
                        </div>
                    </div>
                </Transition>
            </div>
        </Transition>
    </Teleport>
</template>
