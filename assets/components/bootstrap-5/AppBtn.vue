<script lang="ts" setup>
    import {computed, defineProps, withDefaults} from 'vue'
    import type {BootstrapVariant} from '../../types/bootstrap-5'

    const props = withDefaults(
        defineProps<{
            size?: 'lg' | 'md' | 'sm',
            type?: 'button' | 'reset' | 'submit',
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
