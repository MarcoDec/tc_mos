<script setup>
    import AppNavbarMenu from './AppNavbarMenu.vue'
    import useUser from '../../stores/security'

    const user = useUser()
    const appMode = import.meta.env.MODE
    console.info(`App mode: ${appMode}`)
    const logoPath = '/img/TConcept_Logo.png'
</script>

<template>
    <nav id="app-nav-bar" class="bg-dark mb-1 navbar navbar-dark navbar-expand-xxl sticky-top">
        <AppContainer fluid>
            <span class="m-0 navbar-brand p-0">
                <AppRouterLink :to="{name: 'home'}">
                    <img :src="logoPath" alt="TConcept"/>
                </AppRouterLink>
            </span>
            <button
                aria-controls="nav-navigation"
                aria-expanded="false"
                aria-label="Toggle navigation"
                class="navbar-toggler"
                data-bs-target="#nav-navigation"
                data-bs-toggle="collapse"
                type="button">
                <span class="navbar-toggler-icon"/>
            </button>
            <AppNavbarMenu v-if="user.isLogged" id="nav-navigation"/>
        </AppContainer>
    </nav>
</template>
