<script lang="ts" setup>
    import {defineProps} from 'vue'
    defineProps<{href: string}>()

</script>

<template>
    <a :href="href" class="navbar-brand">
        <slot/>
    </a>
</template>
