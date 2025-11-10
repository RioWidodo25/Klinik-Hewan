<script setup>
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
});

const statusConfig = computed(() => {
    const status = props.order.payment?.transaction_status || props.order.payment_status;
    
    const configs = {
        pending: {
            icon: '⏳',
            title: 'Menunggu Pembayaran',
            message: 'Pesanan Anda sedang menunggu pembayaran. Silakan selesaikan pembayaran Anda.',
            color: 'amber',
            bgColor: 'bg-amber-50',
            textColor: 'text-amber-700',
            borderColor: 'border-amber-200',
        },
        settlement: {
            icon: '✅',
            title: 'Pembayaran Berhasil',
            message: 'Terima kasih! Pembayaran Anda telah berhasil diproses.',
            color: 'green',
            bgColor: 'bg-green-50',
            textColor: 'text-green-700',
            borderColor: 'border-green-200',
        },
        capture: {
            icon: '✅',
            title: 'Pembayaran Berhasil',
            message: 'Terima kasih! Pembayaran Anda telah berhasil diproses.',
            color: 'green',
            bgColor: 'bg-green-50',
            textColor: 'text-green-700',
            borderColor: 'border-green-200',
        },
        paid: {
            icon: '✅',
            title: 'Pembayaran Berhasil',
            message: 'Terima kasih! Pembayaran Anda telah berhasil diproses.',
            color: 'green',
            bgColor: 'bg-green-50',
            textColor: 'text-green-700',
            borderColor: 'border-green-200',
        },
        deny: {
            icon: '❌',
            title: 'Pembayaran Ditolak',
            message: 'Maaf, pembayaran Anda ditolak. Silakan coba metode pembayaran lain.',
            color: 'red',
            bgColor: 'bg-red-50',
            textColor: 'text-red-700',
            borderColor: 'border-red-200',
        },
        cancel: {
            icon: '❌',
            title: 'Pembayaran Dibatalkan',
            message: 'Pembayaran Anda telah dibatalkan.',
            color: 'gray',
            bgColor: 'bg-gray-50',
            textColor: 'text-gray-700',
            borderColor: 'border-gray-200',
        },
        expire: {
            icon: '⏱️',
            title: 'Pembayaran Kadaluarsa',
            message: 'Waktu pembayaran telah habis. Silakan buat pesanan baru.',
            color: 'orange',
            bgColor: 'bg-orange-50',
            textColor: 'text-orange-700',
            borderColor: 'border-orange-200',
        },
    };
    
    return configs[status] || configs.pending;
});

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
    }).format(value ?? 0);
};
</script>

<template>
    <Head title="Status Pembayaran" />

    <PublicLayout>
        <section class="bg-gray-50 py-12 dark:bg-gray-900 min-h-[70vh] flex items-center">
            <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 w-full">
                <!-- Status Card -->
                <div 
                    class="rounded-2xl border-2 p-8 text-center shadow-lg"
                    :class="[statusConfig.bgColor, statusConfig.borderColor]"
                >
                    <!-- Icon -->
                    <div class="mb-4 text-6xl">
                        {{ statusConfig.icon }}
                    </div>

                    <!-- Title -->
                    <h1 
                        class="text-3xl font-bold mb-4"
                        :class="statusConfig.textColor"
                    >
                        {{ statusConfig.title }}
                    </h1>

                    <!-- Message -->
                    <p 
                        class="text-lg mb-6"
                        :class="statusConfig.textColor"
                    >
                        {{ statusConfig.message }}
                    </p>

                    <!-- Order Info -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 mb-6 text-left">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                            Informasi Pesanan
                        </h2>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Nomor Pesanan:</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ order.order_number }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Total Pembayaran:</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ formatCurrency(order.total) }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Status Pesanan:</span>
                                <span 
                                    class="inline-flex rounded-full px-3 py-1 text-xs font-semibold"
                                    :class="{
                                        'bg-amber-100 text-amber-800': order.status === 'pending',
                                        'bg-blue-100 text-blue-800': order.status === 'processing',
                                        'bg-green-100 text-green-800': order.status === 'delivered',
                                    }"
                                >
                                    {{ order.status }}
                                </span>
                            </div>

                            <!-- VA Number if available -->
                            <div v-if="order.payment?.va_number" class="pt-3 border-t">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 dark:text-gray-400">Virtual Account:</span>
                                    <span class="font-mono font-bold text-lg text-gray-900 dark:text-white">
                                        {{ order.payment.va_number }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1 text-right">
                                    {{ order.payment.payment_type }}
                                </p>
                            </div>

                            <!-- Snap Redirect URL if pending -->
                            <div v-if="order.payment?.snap_redirect_url && order.payment?.transaction_status === 'pending'" class="pt-3 border-t">
                                <a 
                                    :href="order.payment.snap_redirect_url" 
                                    target="_blank"
                                    class="inline-flex w-full justify-center rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 transition-colors"
                                >
                                    Lanjutkan Pembayaran
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <Link :href="route('petshop.index')">
                            <PrimaryButton class="w-full sm:w-auto">
                                Lanjut Belanja
                            </PrimaryButton>
                        </Link>

                        <Link v-if="order.payment_status === 'paid'" :href="route('dashboard')">
                            <SecondaryButton class="w-full sm:w-auto">
                                Lihat Pesanan Saya
                            </SecondaryButton>
                        </Link>
                    </div>
                </div>

                <!-- Help Text -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Butuh bantuan? Hubungi kami di 
                        <a href="mailto:support@klinkhewan.com" class="text-amber-600 hover:text-amber-700 font-semibold">
                            support@klinkhewan.com
                        </a>
                    </p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
