<script setup>
    import AppNavbarItem from './AppNavbarItem.vue'
    import AppNavbarLink from './link/AppNavbarLink.vue'
    import AppNavbarUser from './AppNavbarUser.vue'
    import AppNotifications from './notification/AppNotifications.vue'
    import {computed} from 'vue'
    import {useBrowserLocation} from '@vueuse/core'
    import useUser from '../../stores/security'

    defineProps({id: {required: true, type: String}})

    const location = useBrowserLocation()
    const database = computed(() => `${location.value.protocol}//${location.value.hostname}:8080`)
    const user = useUser()
</script>

<template>
    <div :id="id" class="collapse navbar-collapse">
        <ul class="me-auto navbar-nav">
            <AppNavbarItem v-if="user.isPurchaseReader" id="purchase" icon="shopping-bag" title="Achats">
                <AppDropdownItem disabled variant="success">
                    Lecteur
                </AppDropdownItem>
                <AppNavbarLink icon="user-tag" to="supplier" variant="success">
                    Fournisseur
                </AppNavbarLink>
                <template v-if="user.isPurchaseAdmin">
                    <AppDropdownItem disabled variant="warning">
                        Administrateur
                    </AppDropdownItem>
                    <AppNavbarLink icon="magnet" to="attributes" variant="warning">
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
                    <AppNavbarLink icon="print" to="printers" variant="warning">
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
            <AppNavbarItem v-if="user.isItAdmin" id="it" icon="laptop" title="Informatique">
                <AppDropdownItem disabled variant="warning">
                    Administrateur
                </AppDropdownItem>
                <a :href="database" class="dropdown-item text-warning" target="_blank">
                    <Fa icon="database"/>
                    Base de données
                </a>
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
            <AppNavbarItem v-if="user.isProductionReader" id="production" icon="industry" title="Production">
                <AppDropdownItem disabled variant="success">
                    Lecteur
                </AppDropdownItem>
                <AppNavbarLink icon="oil-well" to="manufacturers" variant="success">
                    Fabricants
                </AppNavbarLink>
                <AppNavbarLink icon="map-marked" to="zones" variant="success">
                    Zones
                </AppNavbarLink>
                <template v-if="user.isProductionAdmin">
                    <AppDropdownItem disabled variant="warning">
                        Administrateur
                    </AppDropdownItem>
                    <AppNavbarLink icon="wrench" to="engine-groups" variant="warning">
                        Groupes d'équipements
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isProjectReader" id="project" icon="project-diagram" title="Projet">
                <AppDropdownItem disabled variant="success">
                    Lecteur
                </AppDropdownItem>
                <AppNavbarLink icon="atom" to="project-operations" variant="success">
                    Opérations
                </AppNavbarLink>
                <template v-if="user.isProjectAdmin">
                    <AppDropdownItem disabled variant="warning">
                        Administrateur
                    </AppDropdownItem>
                    <AppNavbarLink icon="layer-group" to="product-families" variant="warning">
                        Familles de produits
                    </AppNavbarLink>
                    <AppNavbarLink brands icon="elementor" to="operation-types" variant="warning">
                        Types d'opérations
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isQualityReader" id="quality" icon="certificate" title="Qualité">
                <AppDropdownItem disabled variant="success">
                    Lecteur
                </AppDropdownItem>
                <AppNavbarLink icon="check-circle" to="component-reference-values" variant="success">
                    Relevés qualités composants
                </AppNavbarLink>
                <template v-if="user.isQualityAdmin">
                    <AppDropdownItem disabled variant="warning">
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
                <AppDropdownItem disabled variant="success">
                    Lecteur
                </AppDropdownItem>
                <AppNavbarLink icon="user-graduate" to="out-trainers" variant="success">
                    Formateurs extérieurs
                </AppNavbarLink>
                <template v-if="user.isHrAdmin">
                    <AppDropdownItem disabled variant="warning">
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
        </ul>
    </div>
    <AppSuspense variant="white">
        <AppNotifications/>
    </AppSuspense>
    <AppNavbarUser/>
</template>
