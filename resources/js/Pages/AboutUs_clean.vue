<script setup>
import { Head } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { ref, onMounted } from 'vue';
import lottie from 'lottie-web';

defineProps({
    clinic: {
        type: Object,
        required: true,
    },
});

const lottieContainer = ref(null);
const lottieContainerRight = ref(null);

onMounted(() => {
    if (lottieContainer.value) {
        lottie.loadAnimation({
            container: lottieContainer.value,
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: '/animations/Chameleon changing colour.json'
        });
    }
    
    if (lottieContainerRight.value) {
        lottie.loadAnimation({
            container: lottieContainerRight.value,
            renderer: 'svg',
            loop: true,
            autoplay: true,
            path: '/animations/Chameleon changing colour.json'
        });
    }
});
</script>

<template>
    <Head title="Tentang Kami" />

    <PublicLayout>
        <!-- Hero Section with Lottie Animation -->
        <div class="relative overflow-hidden py-16 sm:py-24">
            <!-- Lottie Background Animation Left -->
            <div ref="lottieContainer" class="absolute inset-0 z-0" style="transform: translateX(-30%);"></div>
            
            <!-- Lottie Background Animation Right (Flipped) -->
            <div ref="lottieContainerRight" class="absolute inset-0 z-0" style="transform: translateX(30%) scaleX(-1);"></div>
            
            <div class="relative z-10 mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h1 class="text-4xl font-bold tracking-tight text-amber-500 sm:text-5xl md:text-6xl" data-aos="fade-down">
                        Tentang Kami
                    </h1>
                    <p class="mx-auto mt-4 max-w-2xl text-lg text-amber-600 sm:text-xl" data-aos="fade-up" data-aos-delay="100">
                        Mengenal lebih dekat klinik hewan terpercaya Anda
                    </p>
                </div>
            </div>
        </div>

        <!-- Clinic Profile Section -->
        <div class="bg-white py-16 dark:bg-gray-900 sm:py-24">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="grid gap-12 lg:grid-cols-2 lg:gap-16">
                    <!-- Logo/Image -->
                    <div class="flex items-center justify-center" data-aos="fade-right" data-aos-delay="100">
                        <div class="relative group cursor-pointer">
                            <div class="absolute -inset-4 rounded-full bg-gradient-to-r from-amber-400 to-orange-500 opacity-20 blur-2xl group-hover:opacity-30 transition-opacity duration-300"></div>
                            <div class="relative rounded-2xl bg-white p-8 shadow-2xl dark:bg-gray-800 transition-all duration-300 ease-in-out group-hover:scale-110 group-hover:shadow-[0_20px_60px_-15px_rgba(251,146,60,0.5)] group-hover:-translate-y-2">
                                <img
                                    src="/images/logo.png"
                                    :alt="clinic.name"
                                    class="h-64 w-64 object-contain transition-transform duration-300 group-hover:scale-105"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- About Text -->
                    <div class="flex flex-col justify-center" data-aos="fade-left" data-aos-delay="100">
                        <div class="relative">
                            <!-- Decorative element -->
                            <div class="absolute -top-4 -left-4 h-24 w-24 rounded-full bg-gradient-to-br from-amber-400/20 to-orange-500/20 blur-2xl"></div>
                            
                            <h2 class="relative text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl">
                                {{ clinic.name }}
                            </h2>
                        </div>
                        
                        <div class="mt-6 relative">
                            <!-- Quote icon -->
                            <svg class="absolute -top-2 -left-2 h-8 w-8 text-amber-200 dark:text-amber-900/50" fill="currentColor" viewBox="0 0 32 32">
                                <path d="M9.352 4C4.456 7.456 1 13.12 1 19.36c0 5.088 3.072 8.064 6.624 8.064 3.36 0 5.856-2.688 5.856-5.856 0-3.168-2.208-5.472-5.088-5.472-.576 0-1.344.096-1.536.192.48-3.264 3.552-7.104 6.624-9.024L9.352 4zm16.512 0c-4.8 3.456-8.256 9.12-8.256 15.36 0 5.088 3.072 8.064 6.624 8.064 3.264 0 5.856-2.688 5.856-5.856 0-3.168-2.304-5.472-5.184-5.472-.576 0-1.248.096-1.44.192.48-3.264 3.456-7.104 6.528-9.024L25.864 4z"/>
                            </svg>
                            
                            <div class="pl-6 mt-2 space-y-4 text-lg leading-relaxed text-gray-600 dark:text-gray-300 border-l-4 border-amber-400">
                                <p v-if="clinic.about" class="relative">{{ clinic.about }}</p>
                                <p v-else class="italic text-gray-400">Deskripsi klinik belum tersedia.</p>
                            </div>
                        </div>

                        <!-- Contact Info with Cards -->
                        <div class="mt-8 grid gap-4 sm:grid-cols-2" data-aos="fade-up" data-aos-delay="200">
                            <div v-if="clinic.phone" class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/10 dark:to-orange-900/10 p-4 shadow-sm hover:shadow-md transition-all duration-300">
                                <div class="absolute top-0 right-0 h-20 w-20 rounded-full bg-amber-200/30 blur-2xl group-hover:bg-amber-300/40 transition-all"></div>
                                <div class="relative flex items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 shadow-lg">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-amber-600 dark:text-amber-400">Telepon</p>
                                        <p class="text-base font-bold text-gray-900 dark:text-white">{{ clinic.phone }}</p>
                                    </div>
                                </div>
                            </div>

                            <div v-if="clinic.email" class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/10 dark:to-orange-900/10 p-4 shadow-sm hover:shadow-md transition-all duration-300">
                                <div class="absolute top-0 right-0 h-20 w-20 rounded-full bg-amber-200/30 blur-2xl group-hover:bg-amber-300/40 transition-all"></div>
                                <div class="relative flex items-center gap-3">
                                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 shadow-lg">
                                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-xs font-medium text-amber-600 dark:text-amber-400">Email</p>
                                        <p class="text-base font-bold text-gray-900 dark:text-white">{{ clinic.email }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </PublicLayout>
</template>
