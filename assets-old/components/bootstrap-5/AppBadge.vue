<script lang="ts" setup>
    import {computed, defineProps, withDefaults} from 'vue'
    import type {BootstrapVariant} from '../../types/bootstrap-5'

    const props = withDefaults(defineProps<{variant: BootstrapVariant}>(), {variant: 'danger'})
    const bg = computed(() => `bg-${props.variant}`)
</script>

<template>
    <span :class="bg" class="badge">
        <slot/>
    </span>
</template>
