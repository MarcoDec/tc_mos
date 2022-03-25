<script lang="ts" setup>
    import {computed, defineProps, withDefaults} from 'vue'
    import type {BootstrapVariant} from '../../../types/bootstrap-5'

    const props = withDefaults(defineProps<{variant?: BootstrapVariant}>(), {variant: 'dark'})
    const bg = computed(() => `bg-${props.variant}`)
</script>

<template>
    <li :class="bg" class="dropdown-item text-dark">
        <slot/>
    </li>
</template>
