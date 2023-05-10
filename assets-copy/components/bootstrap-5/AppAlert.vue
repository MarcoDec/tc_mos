<script lang="ts" setup>
    import {computed, defineProps, withDefaults} from 'vue'

    const props = withDefaults(defineProps<{variant?: string}>(), {variant: 'primary'})
    const alertClass = computed(() => `alert-${props.variant}`)
</script>

<template>
    <div :class="alertClass" class="alert" role="alert">
        <slot/>
    </div>
</template>
