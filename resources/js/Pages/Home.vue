<script setup>
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, computed } from 'vue';

const props = defineProps({
    sliders: {
        type: Array,
        default: () => [],
    },
    doctors: {
        type: Array,
        default: () => [],
    },
    services: {
        type: Array,
        default: () => [],
    },
    branches: {
        type: Array,
        default: () => [],
    },
    showAppointmentMenu: Boolean,
});

// Fallback placeholder gradients jika tidak ada gambar dari database
const placeholderGradients = [
    'from-blue-500 to-purple-600',
    'from-green-500 to-teal-600',
    'from-orange-500 to-red-600',
    'from-pink-500 to-rose-600',
];

// Gunakan slider dari database atau fallback ke placeholder
const sliderImages = computed(() => {
    if (props.sliders && props.sliders.length > 0) {
        // Convert string URLs to object format
        if (typeof props.sliders[0] === 'string') {
            return props.sliders.map((url, index) => ({
                id: index + 1,
                title: null,
                image_url: url,
                gradient: null,
            }));
        }
        return props.sliders;
    }
    // Fallback placeholder
    return placeholderGradients.map((gradient, index) => ({
        id: index + 1,
        title: null,
        image_url: null,
        gradient: gradient,
    }));
});

const currentSlide = ref(0);
let sliderInterval = null;

const nextSlide = () => {
    currentSlide.value = (currentSlide.value + 1) % sliderImages.value.length;
};

const goToSlide = (index) => {
    currentSlide.value = index;
};

// Map Modal for Branches
const selectedBranch = ref(null);

const openMapModal = (branch) => {
    selectedBranch.value = branch;
};

const closeMapModal = () => {
    selectedBranch.value = null;
};

// Typing Effect
const typedText = ref('');
const fullText = 'Selamat Datang di ';
const showCursor = ref(true);
let typingInterval = null;
let cursorInterval = null;

const typeWriter = () => {
    let i = 0;
    typingInterval = setInterval(() => {
        if (i < fullText.length) {
            typedText.value += fullText.charAt(i);
            i++;
        } else {
            clearInterval(typingInterval);
        }
    }, 100); // Kecepatan typing 100ms per karakter
};

onMounted(() => {
    // Auto-slide setiap 5 detik
    if (sliderImages.value.length > 1) {
        sliderInterval = setInterval(nextSlide, 5000);
    }

    // Start typing effect setelah delay singkat
    setTimeout(() => {
        typeWriter();
    }, 500);

    // Blinking cursor effect
    cursorInterval = setInterval(() => {
        showCursor.value = !showCursor.value;
    }, 530);
});

onUnmounted(() => {
    if (sliderInterval) {
        clearInterval(sliderInterval);
    }
    if (typingInterval) {
        clearInterval(typingInterval);
    }
    if (cursorInterval) {
        clearInterval(cursorInterval);
    }
});
</script>

<template>
    <Head title="Home" />

    <PublicLayout>
        <!-- Hero Section with Image Slider -->
        <div class="relative overflow-hidden bg-gray-900 min-h-[600px] sm:min-h-[650px]">
            <!-- Slider Background -->
            <div class="absolute inset-0">
                <div
                    v-for="(image, index) in sliderImages"
                    :key="image.id"
                    class="absolute inset-0 transition-opacity duration-1000 ease-in-out"
                    :class="currentSlide === index ? 'opacity-100' : 'opacity-0'"
                >
                    <!-- Tampilkan gambar asli jika ada, atau gradient placeholder -->
                    <div
                        v-if="image.image_url"
                        class="h-full w-full bg-cover bg-center transition-transform duration-[8000ms] ease-out"
                        :class="currentSlide === index ? 'scale-110' : 'scale-100'"
                        :style="{ backgroundImage: `url(${image.image_url})` }"
                    ></div>
                    <div
                        v-else
                        class="h-full w-full bg-gradient-to-br transition-transform duration-[8000ms] ease-out"
                        :class="[image.gradient, currentSlide === index ? 'scale-110' : 'scale-100']"
                    ></div>
                </div>
                <!-- Overlay untuk readability -->
                <div class="absolute inset-0 bg-black bg-opacity-50"></div>
            </div>

            <!-- Content -->
            <div class="relative mx-auto max-w-7xl px-4 py-32 sm:px-6 sm:py-40 lg:px-8 lg:py-48">
                <div class="text-center">
                    <h1 class="text-4xl font-bold tracking-tight text-white sm:text-5xl md:text-6xl min-h-[4rem]">
                        <span>{{ typedText }}</span>
                        <span v-if="typedText.length < fullText.length" class="animate-pulse">|</span>
                        <span class="text-amber-400">Klinik Hewan A2 VET</span>
                        <span v-if="typedText.length === fullText.length && showCursor" class="text-amber-400">|</span>
                    </h1>
                    <p
                        class="mx-auto mt-3 max-w-md text-base text-gray-200 sm:text-lg md:mt-5 md:max-w-3xl md:text-xl"
                        data-aos="zoom-in-up"
                        data-aos-delay="100"
                    >
                        Memberikan perawatan terbaik untuk sahabat berbulu Anda dengan layanan medis profesional dan penuh kasih sayang.
                    </p>
                </div>

                <!-- Slider Indicators -->
                <div class="mt-8 flex justify-center space-x-3">
                    <button
                        v-for="(image, index) in sliderImages"
                        :key="'indicator-' + image.id"
                        @click="goToSlide(index)"
                        class="h-3 w-3 rounded-full transition-all duration-300"
                        :class="currentSlide === index ? 'bg-amber-400 w-8' : 'bg-white bg-opacity-50 hover:bg-opacity-75'"
                        :aria-label="'Go to slide ' + (index + 1)"
                    ></button>
                </div>

                <!-- CTA Button Buat Janji Temu -->
                <div v-if="showAppointmentMenu" class="mt-10 flex justify-center" data-aos="zoom-in" data-aos-delay="200">
                    <Link
                        :href="route('booking.index')"
                        class="group relative inline-flex items-center justify-center overflow-hidden rounded-full bg-gradient-to-br from-amber-500 to-amber-600 px-8 py-4 text-lg font-semibold text-white shadow-2xl transition-all duration-300 hover:scale-105 hover:shadow-amber-500/50"
                    >
                        <!-- Animated background effect -->
                        <span class="absolute inset-0 h-full w-full bg-gradient-to-br from-amber-400 to-amber-700 opacity-0 transition-opacity duration-300 group-hover:opacity-100"></span>
                        
                        <!-- Icon -->
                        <svg
                            class="relative mr-2 h-6 w-6 transition-transform duration-300 group-hover:scale-110"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        
                        <!-- Button Text -->
                        <span class="relative">Buat Janji Temu</span>
                        
                        <!-- Arrow icon that appears on hover -->
                        <svg
                            class="relative ml-2 h-5 w-5 translate-x-0 opacity-0 transition-all duration-300 group-hover:translate-x-1 group-hover:opacity-100"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </Link>
                </div>
            </div>

            <!-- Curved Bottom Border -->
            <div class="absolute bottom-0 left-0 right-0 z-10">
                <svg viewBox="0 0 1440 100" class="w-full" preserveAspectRatio="none" style="height: 80px;">
                    <path d="M0,0 C480,100 960,100 1440,0 L1440,100 L0,100 Z" fill="rgb(249, 250, 251)" class="dark:fill-gray-900"></path>
                </svg>
            </div>
        </div>

        <!-- Branches Section (relocated from About Us) -->
        <div v-if="branches.length > 0" class="bg-gray-50 py-16 dark:bg-gray-900 sm:py-20">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white sm:text-4xl" data-aos="fade-up">
                        Cabang Kami
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-300" data-aos="fade-up" data-aos-delay="100">
                        Temukan lokasi klinik terdekat dengan Anda
                    </p>
                </div>

                <div class="mt-12 grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <div
                        v-for="(branch, index) in branches"
                        :key="branch.id"
                        class="group overflow-hidden rounded-2xl bg-white shadow-lg transition-all duration-300 hover:shadow-2xl dark:bg-gray-800"
                        data-aos="zoom-in"
                        :data-aos-delay="(index % 3) * 100 + 100"
                    >
                        <!-- Branch Map Preview -->
                        <div class="relative h-48 overflow-hidden bg-gradient-to-br from-amber-400 to-orange-500">
                            <!-- Show Google Maps iframe if available -->
                            <div 
                                v-if="branch.google_maps_iframe" 
                                v-html="branch.google_maps_iframe" 
                                class="pointer-events-none h-full w-full [&>iframe]:h-full [&>iframe]:w-full [&>iframe]:border-0"
                            ></div>
                            <!-- Fallback to static map using coordinates -->
                            <iframe
                                v-else-if="branch.latitude && branch.longitude"
                                :src="`https://www.google.com/maps?q=${branch.latitude},${branch.longitude}&hl=id&z=15&output=embed`"
                                class="pointer-events-none h-full w-full border-0"
                                loading="lazy"
                            ></iframe>
                            <!-- Fallback to image or placeholder -->
                            <div v-else-if="branch.image_url" class="h-full w-full">
                                <img
                                    :src="branch.image_url"
                                    :alt="branch.name"
                                    class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-110"
                                />
                            </div>
                            <!-- Default placeholder -->
                            <div v-else class="flex h-full w-full items-center justify-center">
                                <svg class="h-20 w-20 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <!-- Interactive overlay -->
                            <div class="absolute inset-0 bg-black/0 transition-colors group-hover:bg-black/10"></div>
                        </div>

                        <!-- Branch Info -->
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                {{ branch.name }}
                            </h3>

                            <div class="mt-4 space-y-3">
                                <div class="flex items-start gap-2">
                                    <svg class="mt-0.5 h-5 w-5 shrink-0 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ branch.address }}</p>
                                </div>

                                <div v-if="branch.phone" class="flex items-center gap-2">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ branch.phone }}</p>
                                </div>

                                <div v-if="branch.email" class="flex items-center gap-2">
                                    <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ branch.email }}</p>
                                </div>

                                <div v-if="branch.operational_hours" class="mt-4 rounded-lg bg-amber-50 p-3 dark:bg-amber-900/20">
                                    <p class="mb-1 text-xs font-semibold uppercase text-amber-700 dark:text-amber-400">Jam Operasional</p>
                                    <p class="whitespace-pre-line text-sm text-gray-700 dark:text-gray-300">{{ branch.operational_hours }}</p>
                                </div>
                            </div>

                            <!-- View Map Button -->
                            <button
                                v-if="branch.google_maps_iframe || (branch.latitude && branch.longitude)"
                                @click="openMapModal(branch)"
                                class="mt-4 w-full rounded-lg bg-gradient-to-r from-amber-400 to-orange-500 px-4 py-2.5 text-sm font-semibold text-white shadow-md transition-all duration-200 hover:shadow-lg hover:from-amber-500 hover:to-orange-600"
                            >
                                <div class="flex items-center justify-center gap-2">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                                    </svg>
                                    Lihat Peta
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Services Section -->
        <div class="bg-gray-50 py-16 dark:bg-gray-900">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">
                        Layanan Kami
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-300">
                        Berbagai layanan kesehatan hewan yang komprehensif
                    </p>
                </div>

                <!-- Card Frame Transparan untuk membungkus grid services -->
                <div class="mt-12 rounded-2xl bg-white/50 p-8 backdrop-blur-sm dark:bg-gray-800/50" data-aos="fade-up" data-aos-delay="200">
                    <div class="grid gap-8 md:grid-cols-3">
                        <!-- Loop services dari database -->
                        <div
                            v-for="(service, index) in services"
                            :key="service.id"
                            class="group relative overflow-hidden rounded-lg bg-white p-6 shadow-md transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 dark:bg-gray-700"
                            data-aos="fade-up"
                            :data-aos-delay="(index + 1) * 100"
                        >
                            <!-- Decorative background effect on hover -->
                            <div class="absolute inset-0 bg-gradient-to-br from-amber-500/10 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>

                            <!-- Content wrapper -->
                            <div class="relative z-10">
                                <!-- Icon with animated background -->
                                <div
                                    class="flex h-12 w-12 items-center justify-center rounded-md text-white transition-all duration-300 group-hover:scale-110 group-hover:rotate-6"
                                    :class="service.icon_color"
                                >
                                    <!-- Tampilkan icon custom jika ada -->
                                    <img
                                        v-if="service.icon_url"
                                        :src="service.icon_url"
                                        :alt="service.title"
                                        class="h-8 w-8 object-contain transition-transform duration-300 group-hover:scale-110"
                                    />
                                    <!-- Default SVG icon jika tidak ada -->
                                    <svg
                                        v-else
                                        class="h-6 w-6 transition-transform duration-300 group-hover:rotate-12"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                </div>

                                <!-- Title with hover effect -->
                                <h3 class="mt-4 text-xl font-semibold text-gray-900 transition-colors duration-300 group-hover:text-amber-600 dark:text-white dark:group-hover:text-amber-400">
                                    {{ service.title }}
                                </h3>

                                <!-- Description -->
                                <p class="mt-2 text-gray-600 dark:text-gray-300">
                                    {{ service.description }}
                                </p>

                                <!-- Animated bottom border on hover -->
                                <div class="mt-4 h-1 w-0 rounded-full bg-gradient-to-r from-amber-500 to-amber-600 transition-all duration-300 group-hover:w-full"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Divider/Sekat -->
        <div class="bg-gray-50 dark:bg-gray-900">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="relative py-8">
                    <!-- Decorative divider line -->
                    <div class="absolute inset-0 flex items-center" aria-hidden="true">
                        <div class="w-full border-t-2 border-gray-300 dark:border-gray-700"></div>
                    </div>
                    <!-- Icon in center -->
                    <div class="relative flex justify-center">
                        <span class="bg-gray-50 px-4 dark:bg-gray-900">
                            <svg class="h-8 w-8 text-amber-500" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                            </svg>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dokter Spesialis Section -->
        <div class="bg-gray-50 py-16 pb-32 dark:bg-gray-900">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white" data-aos="fade-up">
                        Dokter Spesialis Kami
                    </h2>
                    <p class="mt-4 text-lg text-gray-600 dark:text-gray-300" data-aos="fade-up" data-aos-delay="100">
                        Tim dokter hewan profesional dan berpengalaman
                    </p>
                </div>

                <!-- Card Frame Transparan untuk membungkus grid dokter -->
                <div class="mt-12 rounded-2xl bg-white/50 p-8 backdrop-blur-sm dark:bg-gray-800/50" data-aos="fade-up" data-aos-delay="200">
                    <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-4">
                        <!-- Loop dokter dari database -->
                        <div
                            v-for="(doctor, index) in doctors"
                            :key="doctor.id"
                            class="group relative overflow-hidden rounded-xl bg-white shadow-lg transition-all duration-300 hover:shadow-2xl hover:-translate-y-2 dark:bg-gray-700"
                            data-aos="zoom-in"
                            :data-aos-delay="(index + 1) * 100"
                        >
                            <!-- Foto atau Gradient Background -->
                            <div class="aspect-[3/4] overflow-hidden">
                                <!-- Tampilkan foto jika ada -->
                                <div
                                    v-if="doctor.photo_url"
                                    class="h-full w-full bg-cover bg-center"
                                    :style="{ backgroundImage: `url(${doctor.photo_url})` }"
                                ></div>
                                <!-- Tampilkan gradient dengan icon jika tidak ada foto -->
                                <div
                                    v-else
                                    class="bg-gradient-to-br h-full w-full"
                                    :class="doctor.gradient_color"
                                >
                                    <div class="flex h-full items-center justify-center">
                                        <svg class="h-32 w-32 text-white opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Info Dokter -->
                            <div class="p-6 text-center">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ doctor.title }} {{ doctor.name }}
                                </h3>
                                <p class="mt-2 text-sm font-medium text-amber-600 dark:text-amber-400">
                                    {{ doctor.specialization }}
                                </p>
                                <p class="mt-3 text-sm text-gray-600 dark:text-gray-300">
                                    {{ doctor.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Modal -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition ease-out duration-200"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition ease-in duration-150"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="selectedBranch"
                    class="fixed inset-0 z-50 overflow-y-auto"
                    @click.self="closeMapModal"
                >
                    <div class="flex min-h-screen items-center justify-center p-4">
                        <!-- Backdrop -->
                        <div class="fixed inset-0 bg-black/50 transition-opacity"></div>

                        <!-- Modal Content -->
                        <div class="relative w-full max-w-4xl rounded-2xl bg-white shadow-2xl dark:bg-gray-800">
                            <!-- Header -->
                            <div class="flex items-center justify-between border-b border-gray-200 p-6 dark:border-gray-700">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ selectedBranch.name }}
                                </h3>
                                <button
                                    @click="closeMapModal"
                                    class="rounded-lg p-2 text-gray-400 transition-colors hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-300"
                                >
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Map Content -->
                            <div class="p-6">
                                <!-- Google Maps iframe if available -->
                                <div 
                                    v-if="selectedBranch.google_maps_iframe"
                                    v-html="selectedBranch.google_maps_iframe"
                                    class="h-96 w-full overflow-hidden rounded-lg [&>iframe]:h-full [&>iframe]:w-full [&>iframe]:border-0"
                                ></div>
                                <!-- Fallback to coordinates -->
                                <iframe
                                    v-else-if="selectedBranch.latitude && selectedBranch.longitude"
                                    :src="`https://www.google.com/maps?q=${selectedBranch.latitude},${selectedBranch.longitude}&hl=id&z=15&output=embed`"
                                    class="h-96 w-full rounded-lg border-0"
                                    loading="lazy"
                                ></iframe>

                                <!-- Branch Details -->
                                <div class="mt-6 space-y-3 border-t border-gray-200 pt-6 dark:border-gray-700">
                                    <div class="flex items-start gap-3">
                                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Alamat</p>
                                            <p class="mt-1 text-base text-gray-900 dark:text-white">{{ selectedBranch.address }}</p>
                                        </div>
                                    </div>
                                    <div v-if="selectedBranch.phone" class="flex items-start gap-3">
                                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Telepon</p>
                                            <p class="mt-1 text-base text-gray-900 dark:text-white">{{ selectedBranch.phone }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </PublicLayout>
</template>
