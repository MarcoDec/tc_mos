<script setup>
    defineProps({css: {default: null, type: String}, to: {required: true, type: String}})
</script>

<template>
    <RouterLink :to="to" custom>
        <template #default="{navigate}">
            <span :class="css" class="pointer" @click="navigate">
                <slot/>
            </span>
        </template>
    </RouterLink>
</template>
