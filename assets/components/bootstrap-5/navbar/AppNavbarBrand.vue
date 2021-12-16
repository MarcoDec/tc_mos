<script lang="ts" setup>
    import {defineProps} from 'vue'
    defineProps<{to: string}>()
</script>

<template>
    <AppRouterLink :to="to" class="navbar-brand">
        <slot/>
    </AppRouterLink>
</template>


