<script lang="ts" setup>
    import {computed, defineProps} from 'vue'

    const props = defineProps<{to: string}>()
    const link = computed(() => ({name: props.to}))
</script>

<template>
    <RouterLink :to="link">
        <slot/>
    </RouterLink>
</template>
