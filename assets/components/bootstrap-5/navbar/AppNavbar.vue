<script setup>
    import {computed} from 'vue'

    const props = defineProps({variant: {default: 'dark', type: String}})
    const navClass = computed(() => `navbar-${props.variant} bg-${props.variant}`)
</script>

<template>
    <nav :class="navClass" class="mb-2 navbar navbar-expand-lg">
        <AppContainer fluid>
            <slot/>
        </AppContainer>
    </nav>
</template>
