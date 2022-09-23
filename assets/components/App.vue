<script setup>
    import AppNavbar from './nav/AppNavbar.vue'
    import {computed} from 'vue'
    import {useRoute} from 'vue-router'
    import {useTitle} from '@vueuse/core'

    const route = useRoute()
    const title = computed(() => route.meta.title)
    useTitle(title)
</script>

<template>
    <AppNavbar/>
    <AppContainer>
        <RouterView/>
    </AppContainer>
</template>
