<script lang="ts" setup>
    import {defineProps} from 'vue'

    defineProps<{href: string, title: string}>()
</script>

<template>
    <a :href="href" class="navbar-brand">
        {{ title }}
    </a>
</template>
