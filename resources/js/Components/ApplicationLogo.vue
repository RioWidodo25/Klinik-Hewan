<script setup>
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const placeholderLogo = '/images/logo.png';

// Get logo from shared props
const page = usePage();

// Track if logo failed to load
const logoError = ref(false);

// Always use default logo from /images/logo.png
const logoUrl = computed(() => {
    return placeholderLogo;
});

// Handle image load error - fallback to placeholder
const handleError = () => {
    console.error('Logo failed to load:', logoUrl.value);
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
