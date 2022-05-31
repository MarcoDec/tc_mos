<script setup>
    import AppCollapse from './AppCollapse.vue'
    import AppNavbarItem from './AppNavbarItem.vue'
    import AppNavbarLink from './AppNavbarLink'
    import AppNavbarUser from './AppNavbarUser.vue'
    import {computed} from 'vue'
    import {generateVariant} from '../validators'
    import useUserStore from '../../stores/hr/employee/user'

    const database = `${location.protocol}//${location.hostname}:8080`
    const props = defineProps({variant: generateVariant('dark')})
    const user = useUserStore()
    const css = computed(() => `bg-${props.variant} navbar-${props.variant}`)
</script>

<template>
    <nav :class="css" class="mb-1 navbar navbar-expand-sm">
        <AppContainer fluid>
            <span class="m-0 navbar-brand p-0">
                <AppRouterLink to="home">T-Concept</AppRouterLink>
            </span>
            <template v-if="user.isLogged">
                <AppCollapse/>
                <div id="top-navbar" class="collapse navbar-collapse">
                    <ul class="me-auto navbar-nav">
                        <AppNavbarItem v-if="user.isPurchaseReader" id="purchase" icon="shopping-bag" title="Achats">
                            <template v-if="user.isPurchaseAdmin">
                                <AppDropdownItem disabled variant="warning">
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
                                <AppDropdownItem disabled variant="warning">
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
                                <AppDropdownItem disabled variant="warning">
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
                            <AppDropdownItem disabled variant="success">
                                Lecteur
                            </AppDropdownItem>
                            <AppNavbarLink icon="shuttle-van" to="carriers" variant="success">
                                Transporteurs
                            </AppNavbarLink>
                            <template v-if="user.isLogisticsAdmin">
                                <AppDropdownItem disabled variant="warning">
                                    Administrateur
                                </AppDropdownItem>
                                <AppNavbarLink icon="file-contract" to="incoterms" variant="warning">
                                    Incoterms
                                </AppNavbarLink>
                            </template>
                        </AppNavbarItem>
                        <AppNavbarItem
                            v-if="user.isProductionReader" id="production" icon="industry"
                            title="Production">
                            <AppDropdownItem disabled variant="success">
                                Lecteur
                            </AppDropdownItem>
                            <AppNavbarLink disabled icon="map-marked" to="zones" variant="danger">
                                Zones
                            </AppNavbarLink>
                            <template v-if="user.isProductionAdmin">
                                <AppDropdownItem disabled variant="warning">
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
                        <AppNavbarItem v-if="user.isProjectReader" id="project" icon="project-diagram" title="Projet">
                            <AppDropdownItem disabled variant="success">
                                Lecteur
                            </AppDropdownItem>
                            <AppNavbarLink disabled icon="atom" to="operations" variant="danger">
                                Opérations
                            </AppNavbarLink>
                            <template v-if="user.isProjectAdmin">
                                <AppDropdownItem disabled variant="warning">
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
                    </ul>
                </div>
                <AppNavbarUser/>
            </template>
        </AppContainer>
    </nav>
</template>
