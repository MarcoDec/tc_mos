<script setup>
    import AppNavbar from './nav/AppNavbar.vue'
    import {computed} from 'vue'
    import {useRoute} from 'vue-router'
    import {useTitle} from '@vueuse/core'

    const route = useRoute()
    const container = computed(() => route.meta.container ?? true)
    const title = computed(() => route.meta.title)
    useTitle(title)
</script>

<template>
    <AppNavbar/>
    <AppContainer v-if="container">
        <RouterView :key="route.name"/>
    </AppContainer>
    <RouterView v-else :key="route.name"/>
</template>
