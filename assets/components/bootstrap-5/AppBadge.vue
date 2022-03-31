<script setup>
    import {computed} from 'vue'

    const props = defineProps({variant: {default: 'danger', type: String}})
    const bg = computed(() => `bg-${props.variant}`)
</script>

<template>
    <span :class="bg" class="badge">
        <slot/>
    </span>
</template>
