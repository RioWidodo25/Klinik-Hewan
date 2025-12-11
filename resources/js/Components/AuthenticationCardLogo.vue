<script setup>
import { Link } from '@inertiajs/vue3';

const logoUrl = '/images/logo.png';
</script>

<template>
    <Link :href="'/'">
        <img
            :src="logoUrl"
            alt="Logo"
            class="h-16 w-auto"
        />
    </Link>
</template>
