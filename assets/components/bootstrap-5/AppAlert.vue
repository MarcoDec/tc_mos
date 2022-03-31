<script setup>
    import {computed} from 'vue'

    const props = defineProps({variant: {default: 'danger', type: String}})
    const bg = computed(() => `alert-${props.variant}`)
</script>

<template>
    <div :class="bg" class="alert" role="alert">
        <slot/>
    </div>
</template>
