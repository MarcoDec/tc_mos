<script setup>
    import AppNavbarUser from './AppNavbarUser.vue'
    import {computed} from 'vue'
    import {generateVariant} from '../validators'
    import useUserStore from '../../stores/hr/employee/user'

    const props = defineProps({variant: generateVariant('dark')})
    const user = useUserStore()
    const css = computed(() => `bg-${props.variant} navbar-${props.variant}`)
</script>

<template>
    <nav :class="css" class="mb-1 navbar">
        <AppContainer fluid>
            <span class="m-0 navbar-brand p-0">T-Concept</span>
            <AppNavbarUser v-if="user.isLogged"/>
        </AppContainer>
    </nav>
</template>
