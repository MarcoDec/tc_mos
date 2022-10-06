<script setup>
    import AppNavbarMenu from './AppNavbarMenu.vue'
    import useUser from '../../stores/security'

    const user = useUser()
</script>

<template>
    <nav class="bg-dark mb-1 navbar navbar-dark navbar-expand-sm">
        <AppContainer fluid>
            <span class="m-0 navbar-brand p-0">
                T-Concept
            </span>
            <AppNavbarMenu v-if="user.isLogged"/>
        </AppContainer>
    </nav>
</template>
