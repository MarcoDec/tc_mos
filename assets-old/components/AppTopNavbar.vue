<script setup>
    import {useRepo, useRouter} from '../composition'
    import AppDropdownItem from './bootstrap-5/navbar/AppDropdownItem'
    import AppNavbar from './bootstrap-5/navbar/AppNavbar'
    import AppNavbarBrand from './bootstrap-5/navbar/AppNavbarBrand'
    import AppNavbarCollapse from './bootstrap-5/navbar/AppNavbarCollapse'
    import AppNavbarItem from './bootstrap-5/navbar/AppNavbarItem.vue'
    import AppNavbarLink from './bootstrap-5/navbar/AppNavbarLink'
    import {EmployeeRepository} from '../store/modules'
    import {computed} from 'vue'

    const database = `${location.protocol}//${location.hostname}:8080`
    const repo = useRepo(EmployeeRepository)
    const {router} = useRouter()
    const hasUser = computed(() => repo.hasUser)
    const user = computed(() => repo.user)

    async function logout() {
        await repo.logout('login')
        router.push({name: 'login'})
    }
</script>

<template>
    <AppNavbar>
        <AppNavbarBrand to="home">
            T-Concept
        </AppNavbarBrand>
        <template v-if="hasUser">
            <AppNavbarCollapse>
                <AppNavbarItem v-if="user.isPurchaseReader" id="purchase" icon="shopping-bag" title="Achats">
                    <template v-if="user.isPurchaseAdmin">
                        <AppDropdownItem variant="warning">
                            Administrateur
                        </AppDropdownItem>
                        <AppNavbarLink disabled icon="magnet" to="attributes" variant="danger">
                            Attributs
                        </AppNavbarLink>
                        <AppNavbarLink icon="layer-group" to="component-families" variant="warning">
                            Familles de composants
                        </AppNavbarLink>
                    </template>
                </AppNavbarItem>
                <AppNavbarItem v-if="user.isManagementReader" id="management" icon="sitemap" title="Direction">
                    <template v-if="user.isManagementAdmin">
                        <AppDropdownItem variant="warning">
                            Administrateur
                        </AppDropdownItem>
                        <AppNavbarLink icon="palette" to="colors" variant="warning">
                            Couleurs
                        </AppNavbarLink>
                        <AppNavbarLink icon="hourglass-half" to="invoice-time-dues" variant="warning">
                            Délais de paiement des factures
                        </AppNavbarLink>
                        <AppNavbarLink disabled icon="print" to="printers" variant="danger">
                            Imprimantes
                        </AppNavbarLink>
                        <AppNavbarLink icon="comments-dollar" to="vat-messages" variant="warning">
                            Messages TVA
                        </AppNavbarLink>
                        <AppNavbarLink icon="ruler-horizontal" to="units" variant="warning">
                            Unités
                        </AppNavbarLink>
                    </template>
                </AppNavbarItem>
                <AppNavbarItem id="it" icon="laptop" title="Informatique">
                    <AppNavbarLink disabled icon="laptop-code" to="it-requests" variant="danger">
                        Demandes
                    </AppNavbarLink>
                    <template v-if="user.isItAdmin">
                        <AppDropdownItem variant="warning">
                            Administrateur
                        </AppDropdownItem>
                        <a class="dropdown-item text-warning" href="/api" target="_blank">
                            <Fa icon="server"/>
                            API
                        </a>
                        <a :href="database" class="dropdown-item text-warning" target="_blank">
                            <Fa icon="database"/>
                            Base de données
                        </a>
                    </template>
                </AppNavbarItem>
                <AppNavbarItem v-if="user.isLogisticsReader" id="logistics" icon="boxes" title="Logistique">
                    <AppDropdownItem variant="success">
                        Lecteur
                    </AppDropdownItem>
                    <AppNavbarLink icon="shuttle-van" to="carriers" variant="success">
                        Transporteurs
                    </AppNavbarLink>
                    <template v-if="user.isLogisticsAdmin">
                        <AppDropdownItem variant="warning">
                            Administrateur
                        </AppDropdownItem>
                        <AppNavbarLink icon="file-contract" to="incoterms" variant="warning">
                            Incoterms
                        </AppNavbarLink>
                    </template>
                </AppNavbarItem>
                <AppNavbarItem v-if="user.isProductionReader" id="production" icon="industry" title="Production">
                    <AppDropdownItem variant="success">
                        Lecteur
                    </AppDropdownItem>
                    <AppNavbarLink disabled icon="map-marked" to="zones" variant="danger">
                        Zones
                    </AppNavbarLink>
                    <template v-if="user.isProductionAdmin">
                        <AppDropdownItem variant="warning">
                            Administrateur
                        </AppDropdownItem>
                        <AppNavbarLink disabled icon="calendar-day" to="engine-events" variant="danger">
                            Catégories d'événements des équipements
                        </AppNavbarLink>
                        <AppNavbarLink icon="wrench" to="engine-groups" variant="warning">
                            Groupes d'équipements
                        </AppNavbarLink>
                    </template>
                </AppNavbarItem>
                <AppNavbarItem v-if="user.isProjectReader" id="project" icon="industry" title="Projet">
                    <AppDropdownItem variant="success">
                        Lecteur
                    </AppDropdownItem>
                    <AppNavbarLink disabled icon="atom" to="operations" variant="danger">
                        Opérations
                    </AppNavbarLink>
                    <template v-if="user.isProjectAdmin">
                        <AppDropdownItem variant="warning">
                            Administrateur
                        </AppDropdownItem>
                        <AppNavbarLink icon="layer-group" to="product-families" variant="warning">
                            Familles de produits
                        </AppNavbarLink>
                        <AppNavbarLink brands disabled icon="elementor" to="operation-types" variant="danger">
                            Types d'opérations
                        </AppNavbarLink>
                    </template>
                </AppNavbarItem>
                <AppNavbarItem v-if="user.isQualityReader" id="quality" icon="certificate" title="Qualité">
                    <AppDropdownItem variant="success">
                        Lecteur
                    </AppDropdownItem>
                    <AppNavbarLink disabled icon="check-circle" to="component-reference-values" variant="danger">
                        Relevés qualités composants
                    </AppNavbarLink>
                    <template v-if="user.isQualityAdmin">
                        <AppDropdownItem variant="warning">
                            Administrateur
                        </AppDropdownItem>
                        <AppNavbarLink brands icon="elementor" to="reject-types" variant="warning">
                            Catégories de rejets de production
                        </AppNavbarLink>
                        <AppNavbarLink brands icon="elementor" to="quality-types" variant="warning">
                            Critères qualités
                        </AppNavbarLink>
                    </template>
                </AppNavbarItem>
                <AppNavbarItem v-if="user.isHrReader" id="hr" icon="male" title="RH">
                    <AppDropdownItem variant="success">
                        Lecteur
                    </AppDropdownItem>
                    <AppNavbarLink icon="user-graduate" to="out-trainers" variant="success">
                        Formateurs extérieurs
                    </AppNavbarLink>
                    <template v-if="user.isHrAdmin">
                        <AppDropdownItem variant="warning">
                            Administrateur
                        </AppDropdownItem>
                        <AppNavbarLink brands icon="elementor" to="event-types" variant="warning">
                            Catégories d'événements des employés
                        </AppNavbarLink>
                        <AppNavbarLink icon="clock" to="time-slots" variant="warning">
                            Plages horaires
                        </AppNavbarLink>
                    </template>
                </AppNavbarItem>
            </AppNavbarCollapse>
            <div class="text-white">
                <Fa icon="user-circle"/>
                {{ user.name }}
                <AppBtn icon="sign-out-alt" title="Déconnexion" variant="danger" @click="logout"/>
            </div>
        </template>
    </AppNavbar>
</template>
