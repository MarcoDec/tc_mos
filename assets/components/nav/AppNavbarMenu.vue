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
    const databaseHostName = computed(() => location.value.hostname.replace('desktop.','phpmyadmin.'))
    const database = computed(() => `${location.value.protocol}//${databaseHostName.value}`)
    const api = computed(() => `${location.value.protocol}//${location.value.hostname}/api`)
    const user = useUser()
    const variantManagement = user.isManagementAdmin ? 'danger' : user.isManagementWriter ? 'warning' : user.isManagementReader ? 'info' : null
    const variantLogistics = user.isLogisticsAdmin ? 'danger' : user.isLogisticsWriter ? 'warning' : user.isLogisticsReader ? 'info' : null
    const variantProduction = user.isProductionAdmin ? 'danger' : user.isProductionWriter ? 'warning' : user.isProductionReader ? 'info' : null
    const variantProject = user.isProjectAdmin ? 'danger' : user.isProjectWriter ? 'warning' : user.isProjectReader ? 'info' : null
    const variantQuality = user.isQualityAdmin ? 'danger' : user.isQualityWriter ? 'warning' : user.isQualityReader ? 'info' : null
    const variantHr = user.isHrAdmin ? 'danger' : user.isHrWriter ? 'warning' : user.isHrReader ? 'info' : null
    const variantSelling = user.isSellingAdmin ? 'danger' : user.isSellingWriter ? 'warning' : user.isSellingReader ? 'info' : null
    const variantPurchase = user.isPurchaseAdmin ? 'danger' : user.isPurchaseWriter ? 'warning' : user.isPurchaseReader ? 'info' : null
    const variantIt = user.isItAdmin ? 'danger' : null
</script>

<template>
    <div :id="id" class="collapse navbar-collapse">
        <ul class="me-auto navbar-nav pt-0">
            <AppNavbarItem v-if="user.isPurchaseReader !== null" id="purchase" icon="shopping-bag" title="Achats">
            <!--TODO                <p>Fournisseur</p>-->
            <AppNavbarLink icon="layer-group" to="component-list" :variant="variantPurchase">
                Liste des composants
            </AppNavbarLink>
            <AppNavbarLink icon="magnet" to="component-equivalents" :variant="variantPurchase">
                Groupes d'équivalences
            </AppNavbarLink>
                <template v-if="user.isPurchaseAdmin">
                    <AppDropdownItem disabled variant="danger">
                        <span class="text-white">Administration</span>
                    </AppDropdownItem>
                    <AppNavbarLink icon="layer-group" to="component-families" :variant="variantPurchase">
                        Familles de composants
                    </AppNavbarLink>
                    <AppNavbarLink icon="magnet" to="attributes" :variant="variantPurchase">
                        Attributs
                    </AppNavbarLink>
                    <AppNavbarLink icon="gear" to="purchase parameters" :variant="variantPurchase">
                        Paramètres
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isManagementReader" id="management" icon="sitemap" title="Direction">
                <AppNavbarLink icon="city" to="society-list" :variant="variantManagement">
                    Sociétés/Groupes
                </AppNavbarLink>
                <AppNavbarLink icon="city" to="company-list" :variant="variantManagement">
                    Compagnies
                </AppNavbarLink>
                <AppNavbarLink icon="calendar" to="agenda" :variant="variantManagement">
                    Agenda
                </AppNavbarLink>
                <AppNavbarLink icon="people-group" to="teams" :variant="variantManagement">
                    Equipes
                </AppNavbarLink>
                <AppNavbarLink v-if="user.isManagementWriter" icon="print" to="printers" :variant="variantManagement">
                    Imprimantes
                </AppNavbarLink>
                <AppNavbarLink v-if="user.isManagementWriter" icon="palette" to="colors" :variant="variantManagement">
                    Couleurs
                </AppNavbarLink>
                <template v-if="user.isManagementAdmin">
                    <AppDropdownItem disabled variant="danger">
                        <span class="text-white">Administration</span>
                    </AppDropdownItem>
                    <AppNavbarLink icon="gauge-high" to="suivi_depenses_ventes" :variant="variantManagement">
                        Suivi des dépenses et ventes
                    </AppNavbarLink>
                    <!-- TODO                    <p>Devises</p>-->
                    <AppNavbarLink icon="hourglass-half" to="invoice-time-dues" :variant="variantManagement">
                        Délais de paiement des factures
                    </AppNavbarLink>
                    <AppNavbarLink icon="comments-dollar" to="vat-messages" :variant="variantManagement">
                        Messages TVA
                    </AppNavbarLink>
                    <AppNavbarLink icon="ruler-horizontal" to="units" :variant="variantManagement">
                        Unités
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isItAdmin" id="it" icon="laptop" title="Informatique">
                <template v-if="user.isItAdmin">
                    <AppDropdownItem disabled variant="danger">
                        <span class="text-white">Administration</span>
                    </AppDropdownItem>
                    <a :href="database" class="dropdown-item text-danger" target="_blank">
                        <Fa icon="database"/>
                        Base de données
                    </a>
                    <a :href="api" class="dropdown-item text-danger" target="_blank">
                        <Fa icon="database"/>
                        Application Programming Interface (API)
                    </a>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isLogisticsReader" id="logistics" icon="boxes" title="Logistique">
                <AppNavbarLink icon="shuttle-van" to="carriers" :variant="variantLogistics">
                    Transporteurs
                </AppNavbarLink>
                <template v-if="user.isLogisticsWriter">
                </template>
                <template v-if="user.isLogisticsAdmin">
                    <AppDropdownItem disabled variant="danger">
                        <span class="text-white">Administration</span>
                    </AppDropdownItem>
                    <AppNavbarLink icon="file-contract" to="incoterms" :variant="variantLogistics">
                        Incoterms
                    </AppNavbarLink>
                    <AppNavbarLink icon="warehouse" to="warehouse-list" :variant="variantLogistics">
                        Entrepots
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isProductionReader" id="production" icon="industry" title="Production">
                <!--TODO                <p>Production</p>-->
                <!--TODO                    <p>Catégories d'événements des équipements (engine-events)</p>-->
                <AppNavbarLink icon="oil-well" to="engines" :variant="variantProduction">
                    Liste des Equipements
                </AppNavbarLink>
                <AppNavbarLink icon="map-marked" to="zones" :variant="variantProduction">
                    Zones
                </AppNavbarLink>
                <AppNavbarLink v-if="user.isProductionWriter" to="label-template-list" icon="tags" :variant="variantProduction">
                    Modèles d'étiquette
                </AppNavbarLink>
                <AppNavbarLink v-if="user.isProductionWriter" icon="wrench" to="engine-groups" :variant="variantProduction">
                    Groupes d'équipements
                </AppNavbarLink>
                <template v-if="user.isProductionAdmin">
                    <AppDropdownItem disabled variant="danger">
                        <span class="text-white">Administration</span>
                    </AppDropdownItem>
                    <AppNavbarLink icon="tags" to="etiquette-list" :variant="variantProduction">
                        Etiquettes Générées
                    </AppNavbarLink>
                    <AppNavbarLink icon="oil-well" to="manufacturers" :variant="variantProduction">
                        Fabricants Equipement
                    </AppNavbarLink>
                    <AppNavbarLink icon="oil-well" to="manufacturer-engines" :variant="variantProduction">
                        Equipements de référence
                    </AppNavbarLink>
                    <AppNavbarLink icon="gear" to="production parameters" :variant="variantProduction">
                        Paramètres
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isProjectReader" id="project" icon="project-diagram" title="Projet">
            <!--                <AppDropdownItem disabled variant="success">-->
            <!--                    Lecteur-->
            <!--                </AppDropdownItem>-->
            <!--                <AppNavbarLink icon="atom" to="project-operations" variant="success">-->
            <!--                    Opérations-->
            <!--                </AppNavbarLink>-->
                <AppNavbarLink icon="fa-brands fa-product-hunt" to="product-list" :variant="variantProject">
                    Liste des Produits
                </AppNavbarLink>
                <template v-if="user.isProjectAdmin">
                    <AppDropdownItem disabled variant="danger">
                        <span class="text-white">Administration</span>
                    </AppDropdownItem>
            <!--                    <AppNavbarLink brands icon="elementor" to="operation-types" variant="warning">-->
            <!--                        Types d'opérations-->
            <!--                    </AppNavbarLink>-->

                    <AppNavbarLink icon="layer-group" to="product-families" :variant="variantProject">
                        Familles de produits
                    </AppNavbarLink>
                    <AppNavbarLink icon="gear" to="project parameters" :variant="variantProject">
                        Paramètres
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isQualityReader" id="quality" icon="certificate" title="Qualité">
                <template v-if="user.isQualityWriter">
                    <AppNavbarLink icon="check-circle" to="component-reference-values" :variant="variantQuality">
                        Relevés qualités composants
                    </AppNavbarLink>
                </template>
                <template v-if="user.isQualityAdmin">
                    <AppDropdownItem disabled variant="danger">
                        <span class="text-white">Administration</span>
                    </AppDropdownItem>
                    <AppNavbarLink brands icon="elementor" to="reject-types" :variant="variantQuality">
                        Catégories de rejets de production
                    </AppNavbarLink>
                    <AppNavbarLink brands icon="elementor" to="quality-types" :variant="variantQuality">
                        Critères qualités
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isHrReader" id="hr" icon="male" title="RH">
                <AppNavbarLink icon="user-tag" to="employee-list" :variant="variantHr">
                    Liste des employés
                </AppNavbarLink>
                <AppNavbarLink icon="user-graduate" to="out-trainers" :variant="variantHr">
                    Formateurs extérieurs
                </AppNavbarLink>
                <AppNavbarLink icon="signal" to="skill-types" :variant="variantHr">
                    Types de Compétences
                </AppNavbarLink>
                <template v-if="user.isHrAdmin">
                    <AppDropdownItem disabled variant="danger">
                        <span class="text-white">Administration</span>
                    </AppDropdownItem>
                    <AppNavbarLink brands icon="elementor" to="event-types" :variant="variantHr">
                        Catégories d'événements des employés
                    </AppNavbarLink>
                    <AppNavbarLink icon="clock" to="time-slots" :variant="variantHr">
                        Plages horaires
                    </AppNavbarLink>
                    <AppNavbarLink icon="gear" to="hr parameters" :variant="variantHr">
                        Paramètres
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isSellingReader" id="selling" icon="euro-sign" title="Ventes">
                <AppNavbarLink icon="user-tie" to="customer-list" :variant="variantSelling">
                    Liste des clients
                </AppNavbarLink>
                <template v-if="user.isSellingAdmin">
                    <AppDropdownItem disabled variant="danger">
                        <span class="text-white">Administration</span>
                    </AppDropdownItem>
                    <AppNavbarLink icon="gear" to="selling parameters" :variant="variantSelling">
                        Paramètres
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
        </ul>
        <div class="align-items-center d-flex flex-row">
            <AppSuspense variant="white">
                <AppNotifications/>
            </AppSuspense>
            <AppNavbarUser/>
        </div>
    </div>
</template>
