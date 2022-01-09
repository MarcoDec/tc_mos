<script lang="ts" setup>
    import {computed, defineProps, withDefaults} from 'vue'
    import type {BootstrapVariant} from '../../../types/bootstrap-5'

    const props = withDefaults(defineProps<{variant?: BootstrapVariant}>(), {variant: 'dark'})
    const navClass = computed(() => `navbar-${props.variant} bg-${props.variant}`)
</script>

<template>
    <nav :class="navClass" class="mb-2 navbar navbar-expand-lg">
        <AppContainer fluid>
            <slot/>
        </AppContainer>
    </nav>
</template>
