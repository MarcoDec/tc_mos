<script setup>
    defineProps({to: {required: true, type: String}})
</script>

<template>
    <AppRouterLink :to="to" class="navbar-brand">
        <slot/>
    </AppRouterLink>
</template>
