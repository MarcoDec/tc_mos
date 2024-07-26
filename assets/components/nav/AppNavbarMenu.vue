<script setup>
    // import {useCookies} from '@vueuse/integrations/useCookies'
    import AppNavbarItem from './AppNavbarItem.vue'
    import AppNavbarLink from './link/AppNavbarLink.vue'
    import AppNavbarUser from './AppNavbarUser.vue'
    import AppNotifications from './notification/AppNotifications.vue'
    import {computed, ref} from 'vue'
    import {useBrowserLocation} from '@vueuse/core'
    import useUser from '../../stores/security'

    defineProps({id: {required: true, type: String}})
    const emit = defineEmits(['closeMenu'])
    //region définition des Ids uniques des sous-menus
    const subMenuIds = {
        purchase: {
            admin: 'purchase-admin'
        },
        management: {
            admin: 'management-admin'
        },
        it: {
            admin: 'it-admin'
        },
        logistic: {
            admin: 'logistic-admin'
        },
        production: {
            equipment: '1',
            label: '2',
            admin: 'production-admin'
        },
        project: {
            admin: 'project-admin'
        },
        quality: {
            admin: 'quality-admin'
        },
        hr: {
            admin: 'hr-admin'
        },
        selling: {
            admin: 'selling-admin'
        }
    }
    //endregion
    // const cookies = useCookies()
    // function getTableFromString(str) {
    //     return JSON.parse(str.replace(/'/g, '"'))
    // }
    // fonction d'ajout du token dans l'url
    // function addTokenToUrl(url) {
    //     if (cookies.get('token')) {
    //         return `${url}?token=${cookies.get('token')}`
    //     }
    //     return url
    // }

    // const localId = import.meta.env.VITE_BACKEND_LOCALID
    // const allIds = getTableFromString(import.meta.env.VITE_BACKEND_ALLIDS)
    // const allIdsUrl = getTableFromString(import.meta.env.VITE_BACKEND_ALLIDSURL)
    //Récupération des Ids et Urls autre que le localId
    // const otherIds = allIds.filter(id => id !== localId)
    // const otherIdsUrl = allIdsUrl.filter((url, index) => allIds[index] !== localId)

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
    const openedSubMenus = ref([])
    function onSubMenuClick(e, menuId) {
        e.preventDefault()
        //On empèche tout évènement de propagation uniquement si aria-expanded est à true et que le clic est sur un item du menu
        if (e.target.getAttribute('aria-expanded') === 'true') {
            openedSubMenus.value.push(menuId)
            //On ferme tous les autres menus
            openedSubMenus.value.filter(id => id !== menuId).forEach(id => {
                const dropdown = document.getElementById(`${id}-dropdown`)
                if (dropdown) {
                    //On ferme le menu en simulant un clic
                    dropdown.click()
                }
            })
            e.stopPropagation()
        } else {
            openedSubMenus.value = openedSubMenus.value.filter(id => id !== menuId)
            e.stopPropagation()
        }
    }
    function onSubMenuItemClick(e) {
        e.preventDefault()
        e.stopPropagation()
        emit('closeMenu')
    }
</script>

<template>
    <div :id="id" class="collapse navbar-collapse">
        <ul class="me-auto navbar-nav pt-0">
            <!--            <AppNavbarItem id="switch" title="oldGP" icon="repeat" class="d-flex flex-column justify-content-center bg-danger">-->
            <!--                <a v-for="(name, index) in otherIds" :key="`switch_${index}`" class="btn btn-secondary d-block width70 m-2" :href="addTokenToUrl(otherIdsUrl[index])">{{ name }}</a>-->
            <!--            </AppNavbarItem>-->
            <AppNavbarItem v-if="user.isPurchaseReader !== null" id="purchase" icon="shopping-bag" title="Achats">
                <AppNavbarLink icon="user-tie" to="supplier-list" :variant="variantPurchase" @click="emit('closeMenu')">
                    Fournisseurs
                </AppNavbarLink>
                <AppNavbarLink icon="layer-group" to="component-list" :variant="variantPurchase" @click="emit('closeMenu')">
                    Composants
                </AppNavbarLink>
                <AppNavbarLink icon="shopping-cart" to="purchaseOrderList" :variant="variantPurchase" @click="emit('closeMenu')">
                    Commandes
                </AppNavbarLink>
                <AppNavbarLink icon="gauge-high" to="needs" :variant="variantPurchase" @click="emit('closeMenu')">
                    Calcul des besoins
                </AppNavbarLink>
                <template v-if="user.isPurchaseAdmin">
                    <AppNavbarItem :id="subMenuIds.purchase.admin" title="Administration" icon="screwdriver-wrench" disabled variant="secondary" :drop-end="true" @click="(e) => onSubMenuClick(e, subMenuIds.purchase.admin)">
                        <AppNavbarLink icon="magnet" to="attributes" :variant="variantPurchase" @click="emit('closeMenu')">
                            Attributs
                        </AppNavbarLink>
                        <AppNavbarLink icon="layer-group" to="component-families" :variant="variantPurchase" @click="emit('closeMenu')">
                            Familles de composants
                        </AppNavbarLink>
                        <AppNavbarLink icon="magnet" to="component-equivalents" :variant="variantPurchase" @click="emit('closeMenu')">
                            Groupes d'équivalences
                        </AppNavbarLink>
                        <AppNavbarLink icon="gear" to="purchase parameters" :variant="variantPurchase" @click="emit('closeMenu')">
                            Paramètres
                        </AppNavbarLink>
                    </AppNavbarItem>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isManagementReader" id="management" icon="sitemap" title="Direction">
                <AppNavbarLink icon="city" to="society-list" :variant="variantManagement" @click="emit('closeMenu')">
                    Sociétés/Groupes
                </AppNavbarLink>
                <AppNavbarLink icon="city" to="company-list" :variant="variantManagement" @click="emit('closeMenu')">
                    Compagnies
                </AppNavbarLink>
                <AppNavbarLink icon="calendar" to="agenda" :variant="variantManagement" @click="emit('closeMenu')">
                    Agenda
                </AppNavbarLink>
                <AppNavbarLink icon="gauge-high" to="suivi_depenses_ventes" :variant="variantManagement" @click="emit('closeMenu')">
                    Suivi des dépenses et ventes
                </AppNavbarLink>
                <template v-if="user.isManagementAdmin">
                    <AppNavbarItem :id="subMenuIds.management.admin" title="Administration" icon="screwdriver-wrench" disabled variant="secondary" :drop-end="true" @click="(e) => onSubMenuClick(e, subMenuIds.management.admin)">
                        <AppNavbarLink icon="people-group" to="teams" :variant="variantManagement" @click="emit('closeMenu')">
                            Equipes
                        </AppNavbarLink>
                        <AppNavbarLink v-if="user.isManagementWriter" icon="print" to="printers" :variant="variantManagement" @click="emit('closeMenu')">
                            Imprimantes
                        </AppNavbarLink>
                        <AppNavbarLink v-if="user.isManagementWriter" icon="palette" to="colors" :variant="variantManagement" @click="emit('closeMenu')">
                            Couleurs
                        </AppNavbarLink>
                        <AppNavbarLink to="currencies" icon="comments-dollar" :variant="variantManagement" @click="emit('closeMenu')">
                            Devises
                        </AppNavbarLink>
                        <AppNavbarLink icon="hourglass-half" to="invoice-time-dues" :variant="variantManagement" @click="emit('closeMenu')">
                            Délais de paiement des factures
                        </AppNavbarLink>
                        <AppNavbarLink icon="comments-dollar" to="vat-messages" :variant="variantManagement" @click="emit('closeMenu')">
                            Messages TVA
                        </AppNavbarLink>
                        <AppNavbarLink icon="ruler-horizontal" to="units" :variant="variantManagement" @click="emit('closeMenu')">
                            Unités
                        </AppNavbarLink>
                    </AppNavbarItem>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isItReader || user.isItWriter || user.isItAdmin" id="it" icon="laptop" title="Informatique">
                <AppNavbarLink icon="laptop-code" to="informatiques" :variant="variantIt" @click="emit('closeMenu')">
                    Eléments informatiques
                </AppNavbarLink>
                <a :href="database" class="dropdown-item text-danger" target="_blank" @click="emit('closeMenu')">
                    <Fa icon="database"/>
                    Base de données
                </a>
                <a :href="api" class="dropdown-item text-danger" target="_blank" @click="emit('closeMenu')">
                    <Fa icon="database"/>
                    Application Programming Interface (API)
                </a>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isLogisticsReader" id="logistics" icon="boxes" title="Logistique">
                <AppNavbarLink icon="warehouse" to="warehouse-list" :variant="variantLogistics" @click="emit('closeMenu')">
                    Entrepots
                </AppNavbarLink>
                <AppNavbarLink icon="shuttle-van" to="carriers" :variant="variantLogistics" @click="emit('closeMenu')">
                    Transporteurs
                </AppNavbarLink>
                <template v-if="user.isLogisticsAdmin">
                    <AppNavbarItem :id="subMenuIds.logistic.admin" title="Administration" icon="screwdriver-wrench" disabled variant="secondary" :drop-end="true" @click="(e) => onSubMenuClick(e, subMenuIds.logistic.admin)">
                        <AppNavbarLink icon="file-contract" to="incoterms" :variant="variantLogistics" @click="emit('closeMenu')">
                            Incoterms
                        </AppNavbarLink>
                    </AppNavbarItem>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isProductionReader" id="production" icon="industry" title="Production">
                <AppNavbarLink v-if="user.isProductionReader" icon="table-list" to="manufacturing-schedule" :variant="variantProduction" @click="emit('closeMenu')">
                    Planning de production
                </AppNavbarLink>
                <AppNavbarLink v-if="user.isProductionReader" icon="table-list" to="manufacturing-order-needs" :variant="variantProduction" @click="emit('closeMenu')">
                    Tableau de bord OFs du site
                </AppNavbarLink>
                <AppNavbarLink v-if="user.isProductionReader" icon="bullhorn" to="of-list" :variant="variantProduction" @click="emit('closeMenu')">
                    Ordres de fabrication
                </AppNavbarLink>
                <AppNavbarItem :id="subMenuIds.production.equipment" title="Equipements" icon="screwdriver-wrench" disabled variant="secondary" :drop-end="true" @click="(e) => onSubMenuClick(e, subMenuIds.production.equipment)">
                    <AppNavbarLink icon="building" to="infrastructures" :variant="variantProduction" @click="emit('closeMenu')">
                        Eléments d'infrastructures
                    </AppNavbarLink>
                    <AppNavbarLink icon="code-fork" to="workstations" :variant="variantProduction" @click="onSubMenuItemClick">
                        Postes de travail
                    </AppNavbarLink>
                    <AppNavbarLink icon="cogs" to="machines" :variant="variantProduction" @click="emit('closeMenu')">
                        Machines
                    </AppNavbarLink>
                    <AppNavbarLink icon="wrench" to="tools" :variant="variantProduction" @click="emit('closeMenu')">
                        Outils
                    </AppNavbarLink>
                    <AppNavbarLink icon="flask" to="counter-parts" :variant="variantProduction" @click="emit('closeMenu')">
                        Contre-parties de test
                    </AppNavbarLink>
                    <AppNavbarLink icon="puzzle-piece" to="spare-parts" :variant="variantProduction" @click="emit('closeMenu')">
                        Pièces de rechange
                    </AppNavbarLink>
                </AppNavbarItem>
                <AppNavbarItem :id="subMenuIds.production.label" title="Etiquette" icon="tags" disabled variant="secondary" :drop-end="true" @click="(e) => onSubMenuClick(e, subMenuIds.production.label)">
                    <AppNavbarLink v-if="user.isProductionWriter" to="label-template-list" icon="tags" :variant="variantProduction" @click="emit('closeMenu')">
                        Modèles d'étiquette
                    </AppNavbarLink>
                    <template v-if="user.isProductionAdmin">
                        <AppNavbarLink icon="tags" to="etiquette-list" :variant="variantProduction" @click="emit('closeMenu')">
                            Etiquettes Générées
                        </AppNavbarLink>
                    </template>
                </AppNavbarItem>
                <template v-if="user.isProductionAdmin">
                    <AppNavbarItem :id="subMenuIds.production.admin" title="Administration" icon="screwdriver-wrench" disabled variant="secondary" :drop-end="true" @click="(e) => onSubMenuClick(e, subMenuIds.production.admin)">
                        <AppNavbarLink icon="map-marked" to="zones" :variant="variantProduction" @click="emit('closeMenu')">
                            Zones
                        </AppNavbarLink>
                        <AppNavbarLink icon="oil-well" to="manufacturers" :variant="variantProduction" @click="emit('closeMenu')">
                            Fabricants Equipement
                        </AppNavbarLink>
                        <AppNavbarLink icon="oil-well" to="manufacturer-engines" :variant="variantProduction" @click="emit('closeMenu')">
                            Modèles d'équipements
                        </AppNavbarLink>
                        <AppNavbarLink icon="gear" to="production parameters" :variant="variantProduction" @click="emit('closeMenu')">
                            Paramètres
                        </AppNavbarLink>
                    </AppNavbarItem>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isProjectReader" id="project" icon="project-diagram" title="Projet">
                <AppNavbarLink icon="fa-brands fa-product-hunt" to="product-list" :variant="variantProject" @click="emit('closeMenu')">
                    Produits
                </AppNavbarLink>
                <AppNavbarLink icon="fa-solid fa-atom" to="project-operations" :variant="variantProject" @click="emit('closeMenu')">
                    Opérations
                </AppNavbarLink>
                <template v-if="user.isProjectAdmin">
                    <AppNavbarItem :id="subMenuIds.project.admin" title="Administration" icon="screwdriver-wrench" disabled variant="secondary" :drop-end="true" @click="(e) => onSubMenuClick(e, subMenuIds.project.admin)">
                        <AppDropdownItem disabled variant="danger">
                            <span class="text-white">Administration</span>
                        </AppDropdownItem>
                        <AppNavbarLink icon="fa-brands fa-elementor" to="operation-types" :variant="variantProject" @click="emit('closeMenu')">
                            Types d'Opération
                        </AppNavbarLink>
                        <AppNavbarLink icon="layer-group" to="product-families" :variant="variantProject" @click="emit('closeMenu')">
                            Familles de produits
                        </AppNavbarLink>
                        <AppNavbarLink icon="gear" to="project parameters" :variant="variantProject" @click="emit('closeMenu')">
                            Paramètres
                        </AppNavbarLink>
                    </AppNavbarItem>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isQualityReader" id="quality" icon="certificate" title="Qualité">
                <template v-if="user.isQualityWriter">
                    <AppNavbarLink icon="check-circle" to="component-reference-values" :variant="variantQuality" @click="emit('closeMenu')">
                        Relevés qualités composants
                    </AppNavbarLink>
                </template>
                <template v-if="user.isQualityAdmin">
                    <AppNavbarItem :id="subMenuIds.quality.admin" title="Administration" icon="screwdriver-wrench" disabled variant="secondary" :drop-end="true" @click="(e) => onSubMenuClick(e, subMenuIds.quality.admin)">
                        <AppNavbarLink brands icon="elementor" to="reject-types" :variant="variantQuality" @click="emit('closeMenu')">
                            Catégories de rejets de production
                        </AppNavbarLink>
                        <AppNavbarLink brands icon="elementor" to="quality-types" :variant="variantQuality" @click="emit('closeMenu')">
                            Critères qualités
                        </AppNavbarLink>
                    </AppNavbarItem>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isHrReader" id="hr" icon="male" title="RH">
                <AppNavbarLink icon="user-tag" to="employee-list" :variant="variantHr" @click="emit('closeMenu')">
                    Employés
                </AppNavbarLink>
                <AppNavbarLink icon="user-graduate" to="out-trainers" :variant="variantHr" @click="emit('closeMenu')">
                    Formateurs extérieurs
                </AppNavbarLink>
                <template v-if="user.isHrAdmin">
                    <AppNavbarItem :id="subMenuIds.quality.admin" title="Administration" icon="screwdriver-wrench" disabled variant="secondary" :drop-start="true" @click="(e) => onSubMenuClick(e, subMenuIds.quality.admin)">
                        <AppNavbarLink brands icon="elementor" to="event-types" :variant="variantHr" @click="emit('closeMenu')">
                            Catégories d'événements des employés
                        </AppNavbarLink>
                        <AppNavbarLink icon="signal" to="skill-types" :variant="variantHr" @click="emit('closeMenu')">
                            Types de Compétences
                        </AppNavbarLink>
                        <AppNavbarLink icon="clock" to="time-slots" :variant="variantHr" @click="emit('closeMenu')">
                            Plages horaires
                        </AppNavbarLink>
                        <AppNavbarLink icon="gear" to="hr parameters" :variant="variantHr" @click="emit('closeMenu')">
                            Paramètres
                        </AppNavbarLink>
                    </AppNavbarItem>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="user.isSellingReader" id="selling" icon="euro-sign" title="Ventes">
                <AppNavbarLink icon="user-tie" to="customer-list" :variant="variantSelling" @click="emit('closeMenu')">
                    Clients
                </AppNavbarLink>
                <AppNavbarLink icon="bullhorn" to="customer-order-list" :variant="variantSelling" @click="emit('closeMenu')">
                    Ventes
                </AppNavbarLink>
                <template v-if="user.isSellingAdmin">
                    <AppNavbarItem :id="subMenuIds.quality.admin" title="Administration" icon="screwdriver-wrench" disabled variant="secondary" :drop-start="true" @click="(e) => onSubMenuClick(e, subMenuIds.quality.admin)">
                        <AppNavbarLink icon="gear" to="selling parameters" :variant="variantSelling" @click="emit('closeMenu')">
                            Paramètres
                        </AppNavbarLink>
                    </AppNavbarItem>
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
