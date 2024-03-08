<script setup>
    import {useCookies} from '@vueuse/integrations/useCookies'
    import AppNavbarItem from './AppNavbarItem.vue'
    import AppNavbarLink from './link/AppNavbarLink.vue'
    import AppNavbarUser from './AppNavbarUser.vue'
    import AppNotifications from './notification/AppNotifications.vue'
    import {computed} from 'vue'
    import {useBrowserLocation} from '@vueuse/core'
    import useUser from '../../stores/security'

    defineProps({id: {required: true, type: String}})
    const emit = defineEmits(['close-menu'])
    const cookies = useCookies()
    function getTableFromString(str) {
        return JSON.parse(str.replace(/'/g, '"'))
    }
    // fonction d'ajout du token dans l'url
    function addTokenToUrl(url) {
        if (cookies.get('token')) {
            return `${url}?token=${cookies.get('token')}`
        }
        return url
    }

    const localId = import.meta.env.VITE_BACKEND_LOCALID
    const allIds = getTableFromString(import.meta.env.VITE_BACKEND_ALLIDS)
    const allIdsUrl = getTableFromString(import.meta.env.VITE_BACKEND_ALLIDSURL)
    //Récupération des Ids et Urls autre que le localId
    const otherIds = allIds.filter(id => id !== localId)
    const otherIdsUrl = allIdsUrl.filter((url, index) => allIds[index] !== localId)

    const location = useBrowserLocation()
    const databaseHostName = computed(() => location.value.hostname.replace('desktop.', 'phpmyadmin.'))
    const database = computed(() => `${location.value.protocol}//${databaseHostName.value}`)
    const api = computed(() => `${location.value.protocol}//${location.value.hostname}/api`)
    const user = useUser()
    const variantManagement = user.isManagementAdmin ? 'danger' : user.isManagementWriter ? 'warning' : user.isManagementReader ? 'info' : null
    const variantLogistics = user.isLogisticsAdmin ? 'danger' : user.isLogisticsWriter ? 'warning' : user.isLogisticsReader ? 'info' : null
    const variantProduction = user.isProductionAdmin ? 'danger' : user.isProductionWriter ? 'warning' : user.isProductionReader ? 'info' : null
    const variantProject = user.isProjectAdmin ? 'danger' : user.isProjectWriter ? 'warning' : user.isProjectReader ? 'info' : null
    const variantQuality = user.isQualityAdmin ? 'danger' : user.isQualityWriter ? 'warning' : user.isQualityReader ? 'info' : null
    const variantHr = user.isHrAdmin ? 'danger' : user.isHrWriter ? 'warning' : user.isHrReader ? 'info' : null
    const variantIt = user.isItAdmin ? 'danger' : user.isItWriter ? 'warning' : user.isItReader ? 'info' : null
    const variantSelling = user.isSellingAdmin ? 'danger' : user.isSellingWriter ? 'warning' : user.isSellingReader ? 'info' : null
    const variantPurchase = user.isPurchaseAdmin ? 'danger' : user.isPurchaseWriter ? 'warning' : user.isPurchaseReader ? 'info' : null
    //const variantIt = user.isItAdmin ? 'danger' : null
</script>

<template>
    <div :id="id" class="collapse navbar-collapse">
        <ul class="me-auto navbar-nav pt-0">
            <AppNavbarItem id="switch" title="oldGP" icon="repeat" class="d-flex flex-column justify-content-center bg-danger">
                <a v-for="(name, index) in otherIds" :key="`switch_${index}`" class="btn btn-secondary d-block width70 m-2" :href="addTokenToUrl(otherIdsUrl[index])">{{ name }}</a>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isPurchaseReader !== null" id="purchase" icon="shopping-bag" title="Achats">
                <AppNavbarLink icon="user-tie" to="supplier-list" :variant="variantPurchase" @click="emit('close-menu')">
                    Liste des fournisseurs
                </AppNavbarLink>
                <AppNavbarLink icon="layer-group" to="component-list" :variant="variantPurchase" @click="emit('close-menu')">
                    Liste des composants
                </AppNavbarLink>
                <AppNavbarLink icon="magnet" to="component-equivalents" :variant="variantPurchase" @click="emit('close-menu')">
                    Groupes d'équivalences
                </AppNavbarLink>
                <template v-if="user.isPurchaseAdmin">
                    <AppDropdownItem disabled variant="danger">
                        <span class="text-white">Administration</span>
                    </AppDropdownItem>
                    <AppNavbarLink icon="layer-group" to="component-families" :variant="variantPurchase" @click="emit('close-menu')">
                        Familles de composants
                    </AppNavbarLink>
                    <AppNavbarLink icon="magnet" to="attributes" :variant="variantPurchase" @click="emit('close-menu')">
                        Attributs
                    </AppNavbarLink>
                    <AppNavbarLink icon="gear" to="purchase parameters" :variant="variantPurchase" @click="emit('close-menu')">
                        Paramètres
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isManagementReader" id="management" icon="sitemap" title="Direction">
                <AppNavbarLink icon="city" to="society-list" :variant="variantManagement" @click="emit('close-menu')">
                    Sociétés/Groupes
                </AppNavbarLink>
                <AppNavbarLink icon="city" to="company-list" :variant="variantManagement" @click="emit('close-menu')">
                    Compagnies
                </AppNavbarLink>
                <AppNavbarLink icon="calendar" to="agenda" :variant="variantManagement" @click="emit('close-menu')">
                    Agenda
                </AppNavbarLink>
                <AppNavbarLink icon="people-group" to="teams" :variant="variantManagement" @click="emit('close-menu')">
                    Equipes
                </AppNavbarLink>
                <AppNavbarLink v-if="user.isManagementWriter" icon="print" to="printers" :variant="variantManagement" @click="emit('close-menu')">
                    Imprimantes
                </AppNavbarLink>
                <AppNavbarLink v-if="user.isManagementWriter" icon="palette" to="colors" :variant="variantManagement" @click="emit('close-menu')">
                    Couleurs
                </AppNavbarLink>
                <template v-if="user.isManagementAdmin">
                    <AppDropdownItem disabled variant="danger">
                        <span class="text-white">Administration</span>
                    </AppDropdownItem>
                    <AppNavbarLink icon="gauge-high" to="suivi_depenses_ventes" :variant="variantManagement" @click="emit('close-menu')">
                        Suivi des dépenses et ventes
                    </AppNavbarLink>
                    <!-- TODO                    <p>Devises</p>-->
                    <AppNavbarLink to="currencies" icon="comments-dollar" :variant="variantManagement" @click="emit('close-menu')">
                        Devises
                    </AppNavbarLink>
                    <AppNavbarLink icon="hourglass-half" to="invoice-time-dues" :variant="variantManagement" @click="emit('close-menu')">
                        Délais de paiement des factures
                    </AppNavbarLink>
                    <AppNavbarLink icon="comments-dollar" to="vat-messages" :variant="variantManagement" @click="emit('close-menu')">
                        Messages TVA
                    </AppNavbarLink>
                    <AppNavbarLink icon="ruler-horizontal" to="units" :variant="variantManagement" @click="emit('close-menu')">
                        Unités
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isItReader||user.isItWriter||user.isItAdmin" id="it" icon="laptop" title="Informatique">
                <AppNavbarLink icon="laptop-code" to="informatiques" :variant="variantIt" @click="emit('close-menu')">
                    Eléments informatiques
                </AppNavbarLink>
                <template v-if="user.isItAdmin">
                    <AppDropdownItem disabled variant="danger">
                        <span class="text-white">Administration</span>
                    </AppDropdownItem>
                    <a :href="database" class="dropdown-item text-danger" target="_blank" @click="emit('close-menu')">
                        <Fa icon="database"/>
                        Base de données
                    </a>
                    <a :href="api" class="dropdown-item text-danger" target="_blank" @click="emit('close-menu')">
                        <Fa icon="database"/>
                        Application Programming Interface (API)
                    </a>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isLogisticsReader" id="logistics" icon="boxes" title="Logistique">
                <AppNavbarLink icon="shuttle-van" to="carriers" :variant="variantLogistics" @click="emit('close-menu')">
                    Transporteurs
                </AppNavbarLink>
                <template v-if="user.isLogisticsAdmin">
                    <AppDropdownItem disabled variant="danger">
                        <span class="text-white">Administration</span>
                    </AppDropdownItem>
                    <AppNavbarLink icon="file-contract" to="incoterms" :variant="variantLogistics" @click="emit('close-menu')">
                        Incoterms
                    </AppNavbarLink>
                    <AppNavbarLink icon="warehouse" to="warehouse-list" :variant="variantLogistics" @click="emit('close-menu')">
                        Entrepots
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isProductionReader" id="production" icon="industry" title="Production">
                <!--TODO                <p>Production</p>-->
                <!--TODO                    <p>Catégories d'événements des équipements (engine-events)</p>-->
                <AppNavbarLink icon="desktop" to="workstations" :variant="variantProduction" @click="emit('close-menu')">
                    Postes de travail
                </AppNavbarLink>
                <AppNavbarLink icon="cogs" to="machines" :variant="variantProduction" @click="emit('close-menu')">
                    Machines
                </AppNavbarLink>
                <AppNavbarLink icon="toolbox" to="tools" :variant="variantProduction" @click="emit('close-menu')">
                    Outils
                </AppNavbarLink>
                <AppNavbarLink icon="flask" to="counter-parts" :variant="variantProduction" @click="emit('close-menu')">
                    Contre-parties de test
                </AppNavbarLink>
                <AppNavbarLink icon="puzzle-piece" to="spare-parts" :variant="variantProduction" @click="emit('close-menu')">
                    Pièces de rechange
                </AppNavbarLink>
                <AppNavbarLink icon="building" to="infrastructures" :variant="variantProduction" @click="emit('close-menu')">
                    Eléments d'infrastructures
                </AppNavbarLink>
                <AppNavbarLink icon="map-marked" to="zones" :variant="variantProduction" @click="emit('close-menu')">
                    Zones
                </AppNavbarLink>
                <AppNavbarLink v-if="user.isProductionWriter" to="label-template-list" icon="tags" :variant="variantProduction" @click="emit('close-menu')">
                    Modèles d'étiquette
                </AppNavbarLink>
                <AppNavbarLink v-if="user.isProductionReader" icon="bullhorn" to="of-list" :variant="variantProduction" @click="emit('close-menu')">
                    Ordres de fabrication
                </AppNavbarLink>
                <AppNavbarLink v-if="user.isProductionWriter" icon="wrench" to="engine-groups" :variant="variantProduction" @click="emit('close-menu')">
                    Groupes d'équipements
                </AppNavbarLink>
                <template v-if="user.isProductionAdmin">
                    <AppDropdownItem disabled variant="danger">
                        <span class="text-white">Administration</span>
                    </AppDropdownItem>
                    <AppNavbarLink icon="tags" to="etiquette-list" :variant="variantProduction" @click="emit('close-menu')">
                        Etiquettes Générées
                    </AppNavbarLink>
                    <AppNavbarLink icon="oil-well" to="manufacturers" :variant="variantProduction" @click="emit('close-menu')">
                        Fabricants Equipement
                    </AppNavbarLink>
                    <AppNavbarLink icon="oil-well" to="manufacturer-engines" :variant="variantProduction" @click="emit('close-menu')">
                        Modèles d'équipements
                    </AppNavbarLink>
                    <AppNavbarLink icon="gear" to="production parameters" :variant="variantProduction" @click="emit('close-menu')">
                        Paramètres
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isProjectReader" id="project" icon="project-diagram" title="Projet">
                <AppNavbarLink icon="fa-brands fa-product-hunt" to="product-list" :variant="variantProject" @click="emit('close-menu')">
                    Liste des Produits
                </AppNavbarLink>
                <AppNavbarLink icon="fa-solid fa-atom" to="project-operations" :variant="variantProject" @click="emit('close-menu')">
                    Opérations
                </AppNavbarLink>
                <template v-if="user.isProjectAdmin">
                    <AppDropdownItem disabled variant="danger">
                        <span class="text-white">Administration</span>
                    </AppDropdownItem>
                    <AppNavbarLink icon="fa-brands fa-elementor" to="operation-types" :variant="variantProject" @click="emit('close-menu')">
                        Types d'Opération
                    </AppNavbarLink>
                    <AppNavbarLink icon="layer-group" to="product-families" :variant="variantProject" @click="emit('close-menu')">
                        Familles de produits
                    </AppNavbarLink>
                    <AppNavbarLink icon="gear" to="project parameters" :variant="variantProject" @click="emit('close-menu')">
                        Paramètres
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isQualityReader" id="quality" icon="certificate" title="Qualité">
                <template v-if="user.isQualityWriter">
                    <AppNavbarLink icon="check-circle" to="component-reference-values" :variant="variantQuality" @click="emit('close-menu')">
                        Relevés qualités composants
                    </AppNavbarLink>
                </template>
                <template v-if="user.isQualityAdmin">
                    <AppDropdownItem disabled variant="danger">
                        <span class="text-white">Administration</span>
                    </AppDropdownItem>
                    <AppNavbarLink brands icon="elementor" to="reject-types" :variant="variantQuality" @click="emit('close-menu')">
                        Catégories de rejets de production
                    </AppNavbarLink>
                    <AppNavbarLink brands icon="elementor" to="quality-types" :variant="variantQuality" @click="emit('close-menu')">
                        Critères qualités
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isHrReader" id="hr" icon="male" title="RH">
                <AppNavbarLink icon="user-tag" to="employee-list" :variant="variantHr" @click="emit('close-menu')">
                    Liste des employés
                </AppNavbarLink>
                <AppNavbarLink icon="user-graduate" to="out-trainers" :variant="variantHr" @click="emit('close-menu')">
                    Formateurs extérieurs
                </AppNavbarLink>
                <AppNavbarLink icon="signal" to="skill-types" :variant="variantHr" @click="emit('close-menu')">
                    Types de Compétences
                </AppNavbarLink>
                <template v-if="user.isHrAdmin">
                    <AppDropdownItem disabled variant="danger">
                        <span class="text-white">Administration</span>
                    </AppDropdownItem>
                    <AppNavbarLink brands icon="elementor" to="event-types" :variant="variantHr" @click="emit('close-menu')">
                        Catégories d'événements des employés
                    </AppNavbarLink>
                    <AppNavbarLink icon="clock" to="time-slots" :variant="variantHr" @click="emit('close-menu')">
                        Plages horaires
                    </AppNavbarLink>
                    <AppNavbarLink icon="gear" to="hr parameters" :variant="variantHr" @click="emit('close-menu')">
                        Paramètres
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isSellingReader" id="selling" icon="euro-sign" title="Ventes">
                <AppNavbarLink icon="user-tie" to="customer-list" :variant="variantSelling" @click="emit('close-menu')">
                    Liste des clients
                </AppNavbarLink>
                <template v-if="user.isSellingAdmin">
                    <AppDropdownItem disabled variant="danger">
                        <span class="text-white">Administration</span>
                    </AppDropdownItem>
                    <AppNavbarLink icon="gear" to="selling parameters" :variant="variantSelling" @click="emit('close-menu')">
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
