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
    branches: {
        type: Array,
        default: () => [],
    },
});

const selectedBranch = ref(null);
const lottieContainer = ref(null);
const lottieContainerRight = ref(null);

const openMapModal = (branch) => {
    selectedBranch.value = branch;
};

const closeMapModal = () => {
    selectedBranch.value = null;
};

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

                        <div v-if="clinic.address" class="mt-4 group relative overflow-hidden rounded-xl bg-gradient-to-br from-amber-50 to-orange-50 dark:from-amber-900/10 dark:to-orange-900/10 p-4 shadow-sm hover:shadow-md transition-all duration-300" data-aos="fade-up" data-aos-delay="300">
                            <div class="absolute top-0 right-0 h-32 w-32 rounded-full bg-amber-200/30 blur-3xl group-hover:bg-amber-300/40 transition-all"></div>
                            <div class="relative flex items-start gap-3">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 shadow-lg">
                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-amber-600 dark:text-amber-400">Alamat</p>
                                    <p class="text-base font-bold text-gray-900 dark:text-white leading-relaxed">{{ clinic.address }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Branches Section -->
        <div v-if="branches.length > 0" class="bg-gray-50 py-16 dark:bg-gray-800 sm:py-24">
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
                        class="group overflow-hidden rounded-2xl bg-white shadow-lg transition-all duration-300 hover:shadow-2xl dark:bg-gray-900"
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

        <!-- Empty State -->
        <div v-else class="bg-gray-50 py-16 dark:bg-gray-800 sm:py-24">
            <div class="mx-auto max-w-7xl px-4 text-center sm:px-6 lg:px-8">
                <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="mt-4 text-xl font-semibold text-gray-900 dark:text-white">Belum ada cabang</h3>
                <p class="mt-2 text-gray-600 dark:text-gray-400">Informasi cabang akan segera ditambahkan.</p>
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
                                <div class="overflow-hidden rounded-lg">
                                    <div v-if="selectedBranch.google_maps_iframe" v-html="selectedBranch.google_maps_iframe" class="h-96 w-full"></div>
                                    <div v-else-if="selectedBranch.latitude && selectedBranch.longitude" class="h-96 w-full bg-gray-100 dark:bg-gray-700">
                                        <iframe
                                            :src="`https://www.google.com/maps?q=${selectedBranch.latitude},${selectedBranch.longitude}&hl=id&z=15&output=embed`"
                                            width="100%"
                                            height="100%"
                                            style="border:0;"
                                            allowfullscreen=""
                                            loading="lazy"
                                            referrerpolicy="no-referrer-when-downgrade"
                                        ></iframe>
                                    </div>
                                </div>

                                <!-- Address Info -->
                                <div class="mt-4 rounded-lg bg-gray-50 p-4 dark:bg-gray-900">
                                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Alamat:</p>
                                    <p class="mt-1 text-base text-gray-900 dark:text-white">{{ selectedBranch.address }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </PublicLayout>
</template>
