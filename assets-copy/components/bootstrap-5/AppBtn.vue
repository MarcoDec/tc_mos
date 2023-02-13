<script lang="ts" setup>
    import type {BootstrapSize, BootstrapVariant} from '../../types/bootstrap-5'
    import {computed, defineProps, withDefaults} from 'vue'

    const props = withDefaults(
        defineProps<{
            size?: BootstrapSize
            type?: 'button' | 'reset' | 'submit'
            variant?: BootstrapVariant
        }>(),
        {
            size: 'sm',
            type: 'button',
            variant: 'primary'
        }
    )
    const btnClass = computed(() => `btn-${props.size} btn-${props.variant}`)
</script>

<template>
    <button :class="btnClass" :type="type" class="btn">
        <slot/>
    </button>
</template>
