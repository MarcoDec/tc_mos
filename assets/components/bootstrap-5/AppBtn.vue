<script lang="ts" setup>
    import {computed, defineProps, withDefaults} from 'vue'
    import type {BootstrapVariant} from '../../types/bootstrap-5'

    const props = withDefaults(defineProps<{
        label: string
        type?: 'button' | 'reset' | 'submit'
        variant?: BootstrapVariant
    }>(), {type: 'button', variant: 'primary'})
    const btnClass = computed(() => `btn-${props.variant}`)
</script>

<template>
    <button :class="btnClass" :type="type" class="btn">
        {{ label }}
    </button>
</template>
