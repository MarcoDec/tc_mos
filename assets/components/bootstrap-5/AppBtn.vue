<script lang="ts" setup>
    import {computed, defineProps, withDefaults} from 'vue'
    import type {BootstrapVariant} from '../../types/bootstrap-5'

    const props = withDefaults(defineProps<{
        label?: string,
        size?: 'lg' | 'md' | 'sm',
        type?: 'button' | 'reset' | 'submit',
        variant?: BootstrapVariant
    }>(), {label: '', size: 'sm', type: 'button', variant: 'primary'})
    const btnClass = computed(() => `btn-${props.variant} btn-${props.size}`)
</script>

<template>
    <button :class="btnClass" :type="type" class="btn">
        <slot>{{ label }}</slot>
    </button>
</template>
