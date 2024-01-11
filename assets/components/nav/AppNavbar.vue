<script setup>
    import AppNavbarMenu from './AppNavbarMenu.vue'
    import useUser from '../../stores/security'

    const user = useUser()
    const appMode = import.meta.env.MODE
    console.info(`App mode: ${appMode}`)
    const logoPath = '/img/TConcept_Logo.png'
</script>

<template>
    <nav id="app-nav-bar" class="bg-dark mb-1 navbar navbar-dark navbar-expand-xxl sticky-top p-0">
        <AppContainer fluid>
            <span class="m-0 navbar-brand p-0">
                <AppRouterLink :to="{name: 'home'}">
                    <img :src="logoPath" alt="TConcept"/>
                </AppRouterLink>
            </span>
            <AppNavbarMenu v-if="user.isLogged" id="nav-navigation"/>
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
        </AppContainer>
    </nav>
</template>

<!--TODO: reprendre les nouvelles routes-->
<!--<template v-if="user.isLogged">-->
<!--    <AppCollapse/>-->
<!--    <div id="top-navbar" class="collapse navbar-collapse">-->
<!--        <ul class="me-auto navbar-nav">-->
<!--            <AppNavbarItem v-if="user.isPurchaseReader" id="purchase" icon="shopping-bag" title="Achats">-->
<!--                <AppNavbarLink icon="user-tag" to="supplier-show" variant="success">-->
<!--                    Fournisseur-->
<!--                </AppNavbarLink>-->
<!--                <AppNavbarLink icon="user-tag" to="supplier-list" variant="warning">-->
<!--                    Fournisseur List-->
<!--                </AppNavbarLink>-->
<!--                <AppNavbarLink icon="user-tag" to="manufacturingOrder-list" variant="warning">-->
<!--                    manufacturingOrder List-->
<!--                </AppNavbarLink>-->
<!--            </AppNavbarItem>-->
<!--            <AppNavbarItem v-if="user.isManagementReader" id="management" icon="sitemap" title="Direction">-->
<!--                <template v-if="user.isManagementAdmin">-->
<!--                    <AppNavbarLink icon="euro-sign" to="currencies" variant="warning">-->
<!--                        Devises-->
<!--                    </AppNavbarLink>-->
<!--                </template>-->
<!--            </AppNavbarItem>-->
<!--            <AppNavbarItem id="it" icon="laptop" title="Informatique">-->
<!--                <AppNavbarLink disabled icon="laptop-code" to="it-requests" variant="danger">-->
<!--                    Demandes-->
<!--                </AppNavbarLink>-->
<!--            </AppNavbarItem>-->
<!--            <AppNavbarItem-->
<!--                    v-if="user.isProductionReader" id="production" icon="industry"-->
<!--                    title="Production">-->
<!--                <template v-if="user.isProductionAdmin">-->
<!--                    <AppNavbarLink disabled icon="calendar-day" to="engine-events" variant="danger">-->
<!--                        Catégories d'événements des équipements-->
<!--                    </AppNavbarLink>-->
<!--                </template>-->
<!--            </AppNavbarItem>-->
<!--        </ul>-->
<!--    </div>-->
<!--    <AppNavbarUser/>-->
<!--</template>-->
