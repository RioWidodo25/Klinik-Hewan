<script setup>
import ProductCard from '@/Components/Petshop/ProductCard.vue';
import QuantitySelector from '@/Components/Petshop/QuantitySelector.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { showConfirm } from '@/Plugins/sweetalert';

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },
    relatedProducts: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const flash = computed(() => page.props.flash || {});

const placeholderImage = 'https://ui-avatars.com/api/?name=Petshop&background=EBF4FF&color=7F9CF5';
const selectedImage = ref(props.product.images?.[0]?.url || placeholderImage);
const quantity = ref(1);
const selectedVariantId = ref(
    props.product.variants && props.product.variants.length === 1
        ? props.product.variants[0].id
        : null,
);
const variantError = ref('');

const productHasVariants = computed(() => props.product.variants && props.product.variants.length > 0);

const selectedVariant = computed(() =>
    props.product.variants?.find((variant) => variant.id === selectedVariantId.value) ?? null,
);

const maxQuantity = computed(() => {
    const stock = selectedVariant.value ? selectedVariant.value.stock : props.product.stock;
    return Math.max(stock || 0, 0);
});

const inStock = computed(() => maxQuantity.value > 0);

const displayPrice = computed(() => {
    if (selectedVariant.value) {
        return selectedVariant.value.final_price || props.product.price;
    }
    return props.product.price;
});

const displayComparePrice = computed(() => {
    if (selectedVariant.value && selectedVariant.value.final_price && props.product.compare_price) {
        return props.product.compare_price;
    }
    return props.product.compare_price;
});

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

const selectVariant = (variant) => {
    if (variant.stock === 0) {
        return;
    }
    selectedVariantId.value = variant.id;
};

const setActiveImage = (url) => {
    selectedImage.value = url || placeholderImage;
};

const addToCart = () => {
    variantError.value = '';

    // Check if user is authenticated
    if (!page.props.auth?.user) {
        // Show dialog and redirect to login
        showConfirm({
            title: 'Login Diperlukan',
            text: 'Anda harus login terlebih dahulu untuk menambahkan produk ke keranjang. Login sekarang?',
            icon: 'info',
            confirmButtonText: 'Ya, Login',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                router.visit(route('login'), {
                    onBefore: () => {
                        // Store the current page as intended URL
                        sessionStorage.setItem('intended_url', window.location.href);
                    }
                });
            }
        });
        return;
    }

    if (productHasVariants.value && !selectedVariantId.value) {
        variantError.value = 'Silakan pilih varian terlebih dahulu.';
        return;
    }

    if (!inStock.value) {
        variantError.value = 'Stok produk sedang tidak tersedia.';
        return;
    }

    router.post(route('petshop.cart.items.store'), {
        product_id: props.product.id,
        variant_id: selectedVariantId.value,
        quantity: quantity.value,
    }, {
        preserveScroll: true,
    });
};

watch(selectedVariantId, () => {
    variantError.value = '';
    if (selectedVariant.value && selectedVariant.value.stock > 0 && quantity.value > selectedVariant.value.stock) {
        quantity.value = selectedVariant.value.stock;
    }
});

watch(maxQuantity, (value) => {
    if (value > 0 && quantity.value > value) {
        quantity.value = value;
    }
});

watch(quantity, (value) => {
    if (value < 1) {
        quantity.value = 1;
    }
    if (maxQuantity.value > 0 && value > maxQuantity.value) {
        quantity.value = maxQuantity.value;
    }
});

const showAllReviewsModal = ref(false);
const selectedRatingFilter = ref(null);
const sortBy = ref('terbaru');

const filteredReviews = computed(() => {
    if (!props.product.reviews) {
        return [];
    }

    let reviews = [...props.product.reviews];

    if (selectedRatingFilter.value) {
        reviews = reviews.filter((review) => review.rating === selectedRatingFilter.value);
    }

    const parseDate = (value) => new Date(value);

    switch (sortBy.value) {
        case 'terbaru':
            reviews.sort((a, b) => parseDate(b.created_at) - parseDate(a.created_at));
            break;
        case 'terlama':
            reviews.sort((a, b) => parseDate(a.created_at) - parseDate(b.created_at));
            break;
        case 'rating_tertinggi':
            reviews.sort((a, b) => b.rating - a.rating);
            break;
        case 'rating_terendah':
            reviews.sort((a, b) => a.rating - b.rating);
            break;
        default:
            break;
    }

    return reviews;
});
</script>

<template>
    <Head :title="product.name" />

    <PublicLayout>
        <section class="bg-white py-12 dark:bg-gray-900">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-12 lg:grid-cols-2">
                    <!-- Gallery -->
                    <div>
                        <div class="overflow-hidden rounded-3xl border border-gray-100 bg-gray-100 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <img
                                :src="selectedImage"
                                :alt="product.name"
                                class="h-full w-full object-cover"
                            >
                        </div>
                        <div v-if="product.images && product.images.length > 1" class="mt-4 grid grid-cols-4 gap-3 sm:grid-cols-5">
                            <button
                                v-for="image in product.images"
                                :key="image.id"
                                type="button"
                                @click="setActiveImage(image.url)"
                                :class="[
                                    'overflow-hidden rounded-xl border transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900',
                                    selectedImage === image.url ? 'border-amber-500 ring-2 ring-amber-200 dark:ring-amber-500/40' : 'border-transparent',
                                ]"
                            >
                                <img :src="image.url" :alt="product.name" class="h-20 w-full object-cover">
                            </button>
                        </div>

                        <!-- Ulasan Pembeli -->
                        <div v-if="product.reviews && product.reviews.length > 0" class="mt-8">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">Ulasan Pembeli</h2>
                                <button
                                    type="button"
                                    class="text-sm font-semibold text-amber-600 hover:text-amber-700 dark:text-amber-300"
                                    @click="showAllReviewsModal = true"
                                >
                                    Lihat semua
                                </button>
                            </div>

                            <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800 mb-4">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="flex items-center gap-2">
                                        <svg class="size-6 text-amber-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111 5.518.403a.562.562 0 01.318.986l-4.204 3.602 1.285 5.385a.562.562 0 01-.84.61L12 16.902l-4.722 2.694a.562.562 0 01-.84-.61l1.285-5.386-4.204-3.6a.562.562 0 01.318-.986l5.518-.403 2.125-5.112z" />
                                        </svg>
                                        <span class="text-2xl font-bold text-gray-900 dark:text-white">{{ Number(product.rating_average || 0).toFixed(1) }}</span>
                                        <span class="text-gray-500 dark:text-gray-400">/5</span>
                                    </div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ product.review_count }} ulasan</span>
                                </div>

                                <div class="space-y-2">
                                    <div v-for="star in [5, 4, 3, 2, 1]" :key="star" class="flex items-center gap-2">
                                        <div class="flex items-center gap-1 w-8">
                                            <svg class="size-3 text-amber-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111 5.518.403a.562.562 0 01.318.986l-4.204 3.602 1.285 5.385a.562.562 0 01-.84.61L12 16.902l-4.722 2.694a.562.562 0 01-.84-.61l1.285-5.386-4.204-3.6a.562.562 0 01.318-.986l5.518-.403 2.125-5.112z" />
                                            </svg>
                                            <span class="text-xs text-gray-600 dark:text-gray-300">{{ star }}</span>
                                        </div>
                                        <div class="flex-1 h-2 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-amber-500 rounded-full" :style="{ width: ((product.reviews.filter((r) => r.rating === star).length / product.reviews.length) * 100) + '%' }"></div>
                                        </div>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 w-6 text-right">{{ product.reviews.filter((r) => r.rating === star).length }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <article v-for="review in product.reviews.slice(0, 2)" :key="review.id" class="rounded-2xl border border-gray-100 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                                    <div class="flex items-start justify-between mb-2">
                                        <div class="flex items-center gap-2">
                                            <div class="size-8 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                                                <span class="text-xs font-semibold text-amber-700 dark:text-amber-300">
                                                    {{ (review.user?.name || 'P')[0].toUpperCase() }}
                                                </span>
                                            </div>
                                            <div>
                                                <p class="text-xs font-semibold text-gray-900 dark:text-white">{{ review.user?.name || 'Pelanggan' }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ review.created_at }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-0.5">
                                            <svg v-for="index in 5" :key="index" class="size-3" :class="index <= review.rating ? 'text-amber-500' : 'text-gray-300 dark:text-gray-600'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111 5.518.403a.562.562 0 01.318.986l-4.204 3.602 1.285 5.385a.562.562 0 01-.84.61L12 16.902l-4.722 2.694a.562.562 0 01-.84-.61l1.285-5.386-4.204-3.6a.562.562 0 01.318-.986l5.518-.403 2.125-5.112z" />
                                            </svg>
                                        </div>
                                    </div>

                                    <p class="text-xs text-gray-700 dark:text-gray-300 leading-relaxed">
                                        {{ review.review }}
                                    </p>
                                </article>
                            </div>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="space-y-6">
                        <div>
                            <div class="flex items-center gap-3">
                                <span v-if="product.category?.name" class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-amber-600 dark:bg-amber-900/40 dark:text-amber-200">
                                    {{ product.category.name }}
                                </span>
                                <span v-if="product.is_featured" class="inline-flex items-center gap-1 rounded-full bg-gradient-to-r from-amber-500 to-orange-500 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white shadow">
                                    <svg class="size-3.5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111 5.518.403a.562.562 0 01.318.986l-4.204 3.602 1.285 5.385a.562.562 0 01-.84.61L12 16.902l-4.722 2.694a.562.562 0 01-.84-.61l1.285-5.386-4.204-3.6a.562.562 0 01.318-.986l5.518-.403 2.125-5.112z" />
                                    </svg>
                                    Unggulan
                                </span>
                            </div>
                            <h1 class="mt-3 text-3xl font-bold text-gray-900 dark:text-white">
                                {{ product.name }}
                            </h1>
                            <p v-if="product.description" class="mt-4 text-gray-600 dark:text-gray-300">
                                {{ product.description }}
                            </p>
                        </div>

                        <div class="rounded-3xl border border-gray-100 bg-gray-50 p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-600 dark:text-amber-300">
                                        Harga
                                    </p>
                                    <div class="mt-2 flex items-baseline gap-3">
                                        <span class="text-3xl font-bold text-amber-600 dark:text-amber-300">
                                            {{ formatCurrency(displayPrice) }}
                                        </span>
                                        <span
                                            v-if="displayComparePrice && displayComparePrice > displayPrice"
                                            class="text-base text-gray-400 line-through dark:text-gray-500"
                                        >
                                            {{ formatCurrency(displayComparePrice) }}
                                        </span>
                                        <span
                                            v-if="product.discount_percentage"
                                            class="inline-flex rounded-full bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-600 dark:text-amber-300"
                                        >
                                            Hemat {{ product.discount_percentage }}%
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                        Stok
                                    </p>
                                    <p
                                        :class="[
                                            'mt-2 text-sm font-semibold',
                                            inStock ? 'text-green-600 dark:text-green-300' : 'text-red-600 dark:text-red-400',
                                        ]"
                                    >
                                        {{ inStock ? `Tersedia (${maxQuantity} item)` : 'Stok Habis' }}
                                    </p>
                                </div>
                            </div>

                            <div v-if="productHasVariants" class="mt-6">
                                <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">
                                    Pilih Varian
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <button
                                        v-for="variant in product.variants"
                                        :key="variant.id"
                                        type="button"
                                        @click="selectVariant(variant)"
                                        :disabled="variant.stock === 0"
                                        :class="[
                                            'rounded-xl border px-4 py-2 text-sm font-medium transition focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900',
                                            selectedVariantId === variant.id
                                                ? 'border-amber-500 bg-amber-50 text-amber-600 dark:border-amber-400 dark:bg-amber-900/40 dark:text-amber-200'
                                                : 'border-gray-200 text-gray-600 hover:border-amber-300 hover:bg-amber-50 hover:text-amber-600 dark:border-gray-700 dark:text-gray-200 dark:hover:border-amber-500 dark:hover:bg-amber-900/20',
                                            variant.stock === 0 ? 'cursor-not-allowed opacity-50 dark:opacity-40' : '',
                                        ]"
                                    >
                                        <span class="block text-left">
                                            {{ variant.name || variant.size || variant.color || 'Varian' }}
                                        </span>
                                        <span class="block text-xs text-gray-400 dark:text-gray-300">
                                            {{ formatCurrency(variant.final_price) }}
                                            · Stok {{ variant.stock }}
                                        </span>
                                    </button>
                                </div>
                                <p v-if="variantError" class="mt-2 text-xs font-medium text-red-500">
                                    {{ variantError }}
                                </p>
                            </div>

                            <div class="mt-6 flex flex-wrap items-center gap-4">
                                <QuantitySelector
                                    v-model="quantity"
                                    :max="Math.max(maxQuantity, 1)"
                                    size="lg"
                                />
                                <button
                                    type="button"
                                    :disabled="!inStock"
                                    @click="addToCart"
                                    class="inline-flex flex-1 items-center justify-center rounded-2xl bg-amber-500 px-6 py-3 text-base font-semibold text-white shadow-lg shadow-amber-500/30 transition hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:bg-gray-400 disabled:shadow-none dark:focus:ring-offset-gray-900"
                                >
                                    <svg class="me-2 size-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437m0 0 1.35 5.068m1.65 6.187h9.75m-9.75 0a2.25 2.25 0 11-4.5 0m4.5 0a2.25 2.25 0 01-4.5 0m14.25 0a2.25 2.25 0 11-4.5 0m4.5 0a2.25 2.25 0 01-4.5 0m0 0H7.125m12.75-7.875-1.064 4.256a1.125 1.125 0 01-1.09.844H8.978a1.125 1.125 0 01-1.09-.876l-1.148-4.599M7.5 6.75h13.125" />
                                    </svg>
                                    Tambah ke Keranjang
                                </button>
                            </div>

                            <div v-if="!inStock" class="mt-3 text-sm font-medium text-red-500 dark:text-red-400">
                                Produk sedang tidak tersedia. Silakan hubungi admin untuk informasi restock.
                            </div>
                        </div>

                        <div v-if="product.specifications" class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Spesifikasi Produk
                            </h2>
                            <div class="prose prose-sm mt-3 max-w-none text-gray-600 dark:prose-invert dark:text-gray-300" v-html="product.specifications" />
                        </div>

                        <div class="rounded-3xl border border-gray-100 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Informasi Tambahan
                            </h2>
                            <dl class="mt-4 space-y-3 text-sm text-gray-600 dark:text-gray-300">
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="font-medium text-gray-700 dark:text-gray-200">Berat</dt>
                                    <dd>{{ product.weight ? `${product.weight} gram` : '—' }}</dd>
                                </div>
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="font-medium text-gray-700 dark:text-gray-200">Total Terjual</dt>
                                    <dd>{{ product.order_count }} produk</dd>
                                </div>
                                <div class="flex items-start justify-between gap-4">
                                    <dt class="font-medium text-gray-700 dark:text-gray-200">Ulasan</dt>
                                    <dd>{{ product.review_count }} ulasan · Rating {{ Number(product.rating_average || 0).toFixed(1) }}/5</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                <div v-if="relatedProducts && relatedProducts.length > 0" class="mt-16">
                    <div class="flex items-center justify-between">
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                            Produk Terkait
                        </h2>
                        <SecondaryButton @click="router.visit(route('petshop.index'))">
                            Lihat Semua Produk
                        </SecondaryButton>
                    </div>

                    <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                        <ProductCard
                            v-for="related in relatedProducts"
                            :key="related.id"
                            :product="related"
                        />
                    </div>
                </div>
            </div>
        </section>

        <Teleport to="body">
            <div v-if="showAllReviewsModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click.self="showAllReviewsModal = false">
                <div class="relative w-full max-w-4xl max-h-[90vh] overflow-hidden rounded-3xl bg-white shadow-2xl dark:bg-gray-800">
                    <div class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-200 bg-white px-6 py-4 dark:border-gray-700 dark:bg-gray-800">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">Semua Ulasan ({{ filteredReviews.length }})</h3>
                        <button type="button" @click="showAllReviewsModal = false" class="rounded-full p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-300">
                            <svg class="size-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                            </svg>
                        </button>
                    </div>

                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4 dark:border-gray-700 dark:bg-gray-900/50">
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter:</span>
                                <button type="button" @click="selectedRatingFilter = null" :class="['rounded-full px-3 py-1 text-xs font-medium transition', selectedRatingFilter === null ? 'bg-amber-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700']">
                                    Semua
                                </button>
                                <button v-for="star in [5, 4, 3, 2, 1]" :key="star" type="button" @click="selectedRatingFilter = star" :class="['flex items-center gap-1 rounded-full px-3 py-1 text-xs font-medium transition', selectedRatingFilter === star ? 'bg-amber-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700']">
                                    <svg class="size-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111 5.518.403a.562.562 0 01.318.986l-4.204 3.602 1.285 5.385a.562.562 0 01-.84.61L12 16.902l-4.722 2.694a.562.562 0 01-.84-.61l1.285-5.386-4.204-3.6a.562.562 0 01.318-.986l5.518-.403 2.125-5.112z" />
                                    </svg>
                                    {{ star }}
                                </button>
                            </div>

                            <div class="ml-auto flex items-center gap-2">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Urutkan:</span>
                                <select v-model="sortBy" class="rounded-lg border-gray-300 text-sm focus:border-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-300">
                                    <option value="terbaru">Terbaru</option>
                                    <option value="terlama">Terlama</option>
                                    <option value="rating_tertinggi">Rating Tertinggi</option>
                                    <option value="rating_terendah">Rating Terendah</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-y-auto p-6" style="max-height: calc(90vh - 180px)">
                        <div v-if="filteredReviews.length === 0" class="py-12 text-center">
                            <svg class="mx-auto size-16 text-gray-300 dark:text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                            </svg>
                            <p class="mt-4 text-sm font-medium text-gray-500 dark:text-gray-400">Tidak ada ulasan yang sesuai dengan filter</p>
                        </div>

                        <div v-else class="space-y-6">
                            <article v-for="review in filteredReviews" :key="review.id" class="border-b border-gray-200 pb-6 last:border-b-0 dark:border-gray-700">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-center gap-3">
                                        <div class="size-10 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                                            <span class="text-sm font-semibold text-amber-700 dark:text-amber-300">
                                                {{ (review.user?.name || 'P')[0].toUpperCase() }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ review.user?.name || 'Pelanggan' }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ review.created_at }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <svg v-for="index in 5" :key="index" class="size-4" :class="index <= review.rating ? 'text-amber-500' : 'text-gray-300 dark:text-gray-600'" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111 5.518.403a.562.562 0 01.318.986l-4.204 3.602 1.285 5.385a.562.562 0 01-.84.61L12 16.902l-4.722 2.694a.562.562 0 01-.84-.61l1.285-5.386-4.204-3.6a.562.562 0 01.318-.986l5.518-.403 2.125-5.112z" />
                                        </svg>
                                    </div>
                                </div>

                                <div v-if="review.variant_name" class="mb-2">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">Varian: {{ review.variant_name }}</span>
                                </div>

                                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">
                                    {{ review.review }}
                                </p>

                                <div class="flex flex-wrap gap-2 mt-3">
                                    <span class="inline-flex items-center gap-1 text-xs px-2 py-1 bg-green-50 text-green-700 rounded-full dark:bg-green-900/30 dark:text-green-300">
                                        <svg class="size-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                        </svg>
                                        Kualitas andalan
                                    </span>
                                    <span class="inline-flex items-center gap-1 text-xs px-2 py-1 bg-blue-50 text-blue-700 rounded-full dark:bg-blue-900/30 dark:text-blue-300">
                                        <svg class="size-3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                        </svg>
                                        Harga sesuai kualitas
                                    </span>
                                </div>
                            </article>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </PublicLayout>
</template>
