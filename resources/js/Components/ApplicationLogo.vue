<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const placeholderLogo = 'https://ui-avatars.com/api/?name=A2+VET&size=128&background=000000&color=fff&bold=true&rounded=true';

// Check if we're in dark mode
const isDark = computed(() => {
    return document.documentElement.classList.contains('dark');
});

// Get logo from shared props
const page = usePage();
const logoLight = computed(() => page.props.appLogo);
const logoDark = computed(() => page.props.appLogoDark);

// Track if logo failed to load
const logoError = ref(false);

// Use appropriate logo based on dark mode
const logoUrl = computed(() => {
    if (logoError.value) {
        return placeholderLogo;
    }
    return isDark.value && logoDark.value ? logoDark.value : logoLight.value;
});

// Handle image load error
const handleError = () => {
    logoError.value = true;
};
</script>

<template>
    <!-- Use custom logo if available, otherwise show placeholder from web -->
    <img
        :src="logoUrl || placeholderLogo"
        @error="handleError"
        alt="Logo"
        class="block h-9 w-auto"
    />
</template>
