<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import { ref, computed } from 'vue';

const page = usePage();
const logoLight = computed(() => page.props.appLogo);
const logoDark = computed(() => page.props.appLogoDark);

defineProps({
    canResetPassword: Boolean,
    status: String,
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.transform(data => ({
        ...data,
        remember: form.remember ? 'on' : '',
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Masuk - A2 VET" />

    <div class="min-h-screen flex">
        <!-- Left Side - Image -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-amber-400 via-amber-500 to-amber-600 relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1548199973-03cce0bbc87b?w=1200')] bg-cover bg-center opacity-20"></div>
            
            <div class="relative z-10 flex flex-col justify-center items-center w-full px-12 text-white">
                <div class="mb-8 bg-white/20 backdrop-blur-sm rounded-3xl p-8">
                    <img 
                        v-if="logoLight" 
                        :src="logoLight" 
                        alt="A2 VET Logo" 
                        class="w-32 h-32 object-contain drop-shadow-2xl"
                    />
                    <div v-else class="w-32 h-32 flex items-center justify-center">
                        <span class="text-6xl font-bold">A2</span>
                    </div>
                </div>
                <h1 class="text-5xl font-bold mb-4 text-center drop-shadow-lg">A2 VET</h1>
                <p class="text-xl text-center text-white/90 max-w-md">Klinik Hewan Terpercaya untuk Sahabat Kesayangan Anda</p>
                
                <div class="mt-12 space-y-4 w-full max-w-md">
                    <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm rounded-lg p-4">
                        <div class="bg-white/20 p-3 rounded-full">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold">Dokter Berpengalaman</p>
                            <p class="text-sm text-white/80">Tim profesional siap membantu</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4 bg-white/10 backdrop-blur-sm rounded-lg p-4">
                        <div class="bg-white/20 p-3 rounded-full">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold">Layanan 24/7</p>
                            <p class="text-sm text-white/80">Siap melayani kapan saja</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50 dark:bg-gray-900">
            <div class="w-full max-w-md">
                <!-- Logo Mobile -->
                <div class="lg:hidden text-center mb-8">
                    <Link :href="route('home')" class="inline-flex items-center gap-3">
                        <img 
                            v-if="logoLight" 
                            :src="logoLight" 
                            alt="A2 VET Logo" 
                            class="w-12 h-12 object-contain"
                        />
                        <div v-else class="w-12 h-12 rounded-xl bg-amber-500 flex items-center justify-center">
                            <span class="text-xl font-bold text-white">A2</span>
                        </div>
                        <span class="text-2xl font-bold text-gray-900 dark:text-white">A2 VET</span>
                    </Link>
                </div>

                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Selamat Datang Kembali!</h2>
                        <p class="text-gray-600 dark:text-gray-400">Masuk ke akun Anda untuk melanjutkan</p>
                    </div>

                    <div v-if="status" class="mb-6 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                        <p class="text-sm text-green-600 dark:text-green-400">{{ status }}</p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                Email
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                    </svg>
                                </span>
                                <input
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="block w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:text-white transition"
                                    placeholder="email.com"
                                    required
                                    autofocus
                                    autocomplete="username"
                                />
                            </div>
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">
                                Password
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </span>
                                <input
                                    id="password"
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    class="block w-full pl-10 pr-12 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 dark:bg-gray-700 dark:text-white transition"
                                    placeholder="password"
                                    required
                                    autocomplete="current-password"
                                />
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition"
                                >
                                    <svg v-if="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </button>
                            </div>
                            <InputError class="mt-2" :message="form.errors.password" />
                        </div>

                        <div class="flex items-center justify-between">
                            <label class="flex items-center">
                                <input
                                    v-model="form.remember"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-amber-500 focus:ring-amber-500 dark:border-gray-600 dark:bg-gray-700"
                                />
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Ingat saya</span>
                            </label>

                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-sm font-medium text-amber-600 hover:text-amber-500 dark:text-amber-400"
                            >
                                Lupa password?
                            </Link>
                        </div>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-semibold text-white bg-amber-500 hover:bg-amber-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-amber-500 disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                        >
                            <svg v-if="form.processing" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ form.processing ? 'Memproses...' : 'Masuk' }}</span>
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Belum punya akun?
                            <Link :href="route('register')" class="font-medium text-amber-600 hover:text-amber-500 dark:text-amber-400">
                                Daftar sekarang
                            </Link>
                        </p>
                    </div>

                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <Link
                            :href="route('home')"
                            class="flex items-center justify-center gap-2 text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 transition"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Kembali ke Beranda
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
