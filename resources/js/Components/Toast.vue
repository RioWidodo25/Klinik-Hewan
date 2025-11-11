<script setup>
import { ref, watch, onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const show = ref(false);
const message = ref('');
const type = ref('success'); // success, error, warning, info
let timeoutId = null;

const icons = {
    success: `
        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
    `,
    error: `
        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
    `,
    warning: `
        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
    `,
    info: `
        <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    `,
};

const colorClasses = {
    success: 'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-200',
    error: 'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-200',
    warning: 'bg-amber-50 border-amber-200 text-amber-800 dark:bg-amber-900/20 dark:border-amber-800 dark:text-amber-200',
    info: 'bg-blue-50 border-blue-200 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-200',
};

const iconColorClasses = {
    success: 'bg-green-100 text-green-600 dark:bg-green-800 dark:text-green-200',
    error: 'bg-red-100 text-red-600 dark:bg-red-800 dark:text-red-200',
    warning: 'bg-amber-100 text-amber-600 dark:bg-amber-800 dark:text-amber-200',
    info: 'bg-blue-100 text-blue-600 dark:bg-blue-800 dark:text-blue-200',
};

const showToast = (msg, toastType = 'success') => {
    message.value = msg;
    type.value = toastType;
    show.value = true;

    // Clear existing timeout
    if (timeoutId) {
        clearTimeout(timeoutId);
    }

    // Auto hide after 5 seconds
    timeoutId = setTimeout(() => {
        show.value = false;
    }, 5000);
};

const closeToast = () => {
    show.value = false;
    if (timeoutId) {
        clearTimeout(timeoutId);
    }
};

// Watch for flash messages
watch(() => page.props.flash, (flash) => {
    if (flash?.success) {
        showToast(flash.success, 'success');
    } else if (flash?.error) {
        showToast(flash.error, 'error');
    } else if (flash?.warning) {
        showToast(flash.warning, 'warning');
    } else if (flash?.info) {
        showToast(flash.info, 'info');
    }
}, { deep: true, immediate: true });

onMounted(() => {
    // Check for initial flash messages
    const flash = page.props.flash;
    if (flash?.success) {
        showToast(flash.success, 'success');
    } else if (flash?.error) {
        showToast(flash.error, 'error');
    } else if (flash?.warning) {
        showToast(flash.warning, 'warning');
    } else if (flash?.info) {
        showToast(flash.info, 'info');
    }
});
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-300 transform"
            enter-from-class="translate-y-2 opacity-0 scale-95"
            enter-to-class="translate-y-0 opacity-100 scale-100"
            leave-active-class="transition ease-in duration-200 transform"
            leave-from-class="translate-y-0 opacity-100 scale-100"
            leave-to-class="translate-y-2 opacity-0 scale-95"
        >
            <div
                v-if="show"
                class="fixed top-4 right-4 z-[9999] max-w-md w-full pointer-events-auto"
            >
                <div
                    :class="[
                        'flex items-start gap-4 p-4 rounded-2xl border-2 shadow-2xl backdrop-blur-sm',
                        colorClasses[type]
                    ]"
                >
                    <!-- Icon with animation -->
                    <div
                        :class="[
                            'flex-shrink-0 rounded-full p-2',
                            iconColorClasses[type]
                        ]"
                    >
                        <div
                            v-if="type === 'success'"
                            class="animate-[checkmark_0.5s_ease-in-out]"
                        >
                            <svg class="size-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div
                            v-else-if="type === 'error'"
                            class="animate-[shake_0.5s_ease-in-out]"
                            v-html="icons.error"
                        />
                        <div
                            v-else
                            v-html="icons[type]"
                        />
                    </div>

                    <!-- Message -->
                    <div class="flex-1 pt-0.5">
                        <p class="font-semibold text-sm leading-relaxed">
                            {{ message }}
                        </p>
                    </div>

                    <!-- Close button -->
                    <button
                        @click="closeToast"
                        class="flex-shrink-0 rounded-lg p-1.5 hover:bg-black/5 dark:hover:bg-white/10 transition-colors"
                    >
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Progress bar -->
                    <div class="absolute bottom-0 left-0 right-0 h-1 bg-black/10 dark:bg-white/10 rounded-b-2xl overflow-hidden">
                        <div
                            class="h-full bg-current opacity-50 animate-[shrink_5s_linear]"
                            :class="{
                                'bg-green-600': type === 'success',
                                'bg-red-600': type === 'error',
                                'bg-amber-600': type === 'warning',
                                'bg-blue-600': type === 'info',
                            }"
                        />
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<style scoped>
@keyframes checkmark {
    0% {
        transform: scale(0) rotate(-45deg);
        opacity: 0;
    }
    50% {
        transform: scale(1.2) rotate(0deg);
    }
    100% {
        transform: scale(1) rotate(0deg);
        opacity: 1;
    }
}

@keyframes shake {
    0%, 100% {
        transform: translateX(0);
    }
    10%, 30%, 50%, 70%, 90% {
        transform: translateX(-4px);
    }
    20%, 40%, 60%, 80% {
        transform: translateX(4px);
    }
}

@keyframes shrink {
    from {
        width: 100%;
    }
    to {
        width: 0%;
    }
}
</style>
