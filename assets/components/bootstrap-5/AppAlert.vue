<script lang="ts" setup>
    import {computed, defineProps, withDefaults} from 'vue'
    import type {BootstrapVariant} from '../../types/bootstrap-5'

    const props = withDefaults(defineProps<{variant: BootstrapVariant}>(), {variant: 'danger'})
    const bg = computed(() => `alert-${props.variant}`)
</script>

<template>
    <div :class="bg" class="alert" role="alert">
        <slot/>
    </div>
</template>
