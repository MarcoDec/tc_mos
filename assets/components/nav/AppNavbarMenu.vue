<script setup>
    import AppNavbarItem from './AppNavbarItem.vue'
    import AppNavbarLink from './link/AppNavbarLink.vue'
    import AppNavbarUser from './AppNavbarUser.vue'
    import AppNotifications from './notification/AppNotifications.vue'
    // import {computed} from 'vue'
    // import {useBrowserLocation} from '@vueuse/core'
    import useUser from '../../stores/security'

    defineProps({id: {required: true, type: String}})

    // const location = useBrowserLocation()
    // const database = computed(() => `${location.value.protocol}//${location.value.hostname}:8080`)
    // const api = computed(() => `${location.value.protocol}//${location.value.hostname}:8000/api`)
    const user = useUser()
    const variantManagement = user.isManagementAdmin ? 'danger' : user.isManagementWriter ? 'warning' : user.isManagementReader ? 'info' : null
    // const variantLogistics = user.isLogisticsAdmin ? 'danger' : user.isLogisticsWriter ? 'warning' : user.isLogisticsReader ? 'info' : null
    const variantProduction = user.isProductionAdmin ? 'danger' : user.isProductionWriter ? 'warning' : user.isProductionReader ? 'info' : null
    // const variantProject = user.isProjectAdmin ? 'danger' : user.isProjectWriter ? 'warning' : user.isProjectReader ? 'info' : null
    // const variantQuality = user.isQualityAdmin ? 'danger' : user.isQualityWriter ? 'warning' : user.isQualityReader ? 'info' : null
    // const variantHr = user.isHrAdmin ? 'danger' : user.isHrWriter ? 'warning' : user.isHrReader ? 'info' : null
    // const variantSelling = user.isSellingAdmin ? 'danger' : user.isSellingWriter ? 'warning' : user.isSellingReader ? 'info' : null
    // const variantPurchase = user.isPurchaseAdmin ? 'danger' : user.isPurchaseWriter ? 'warning' : user.isPurchaseReader ? 'info' : null
    // const variantIt = user.isItAdmin ? 'danger' : null
</script>

<template>
    <div :id="id" class="collapse navbar-collapse">
        <ul class="me-auto navbar-nav pt-0">
            <!--        <AppNavbarItem v-if="variantPurchase !== null" id="purchase" icon="shopping-bag" title="Achats">-->
            <!--                <AppDropdownItem disabled variant="success">-->
            <!--                    Lecteur-->
            <!--                </AppDropdownItem>-->
            <!--                <p>Fournisseur</p>-->
            <!--                <p>Composant</p>-->
            <!--                <p>Employee</p>-->
            <!--                <p>Customer</p>-->
            <!--                <p>Production</p>-->
            <!--                <p>Equipement</p>-->
            <!--                <template v-if="user.isPurchaseAdmin">-->
            <!--                    <AppDropdownItem disabled variant="warning">-->
            <!--                        Administrateur-->
            <!--                    </AppDropdownItem>-->
            <!--            <AppNavbarLink icon="magnet" to="attributes" :variant="variantPurchase">-->
            <!--                Attributs-->
            <!--            </AppNavbarLink>-->
            <!--            <AppNavbarLink icon="layer-group" to="component-families" :variant="variantPurchase">-->
            <!--                Familles de composants-->
            <!--            </AppNavbarLink>-->
            <!--                    <AppNavbarLink icon="gear" to="purchase parameters" variant="warning">-->
            <!--                        Paramètres-->
            <!--                    </AppNavbarLink>-->
            <!--                </template>-->
            <!--        </AppNavbarItem>-->
            <AppNavbarItem v-if="user.isManagementReader" id="management" icon="sitemap" title="Direction">
                <!--                <AppDropdownItem disabled variant="success">-->
                <!--                    Lecteur-->
                <!--                </AppDropdownItem>-->
                <!--                <AppNavbarLink icon="city" to="society-list" variant="success">-->
                <!--                    Sociétés-->
                <!--                </AppNavbarLink>-->
                <!--                <AppNavbarLink icon="city" to="company-list" variant="success">-->
                <!--                    Compagnies-->
                <!--                </AppNavbarLink>-->
                <!--                <AppNavbarLink icon="calendar" to="agenda" variant="success">-->
                <!--                    Agenda-->
                <!--                </AppNavbarLink>-->
                <!--                <template v-if="user.isManagementReader">-->
                <!--                    <AppDropdownItem disabled variant="info">-->
                <!--                        Contributeur-->
                <!--                    </AppDropdownItem>-->
                <AppNavbarLink icon="gauge-high" to="suivi_depenses_ventes" :variant="variantManagement">
                    Suivi des dépenses et ventes
                </AppNavbarLink>
                <!--                </template>-->
                <!--                                <template v-if="user.isManagementAdmin">-->
                <!--                                    <AppDropdownItem disabled variant="warning">-->
                <!--                                        Administrateur-->
                <!--                                    </AppDropdownItem>-->
                <AppNavbarLink icon="print" to="printers" :variant="variantManagement">
                    Imprimantes
                </AppNavbarLink>
            <!--                    <AppNavbarLink icon="palette" to="colors" variant="warning">-->
            <!--                        Couleurs-->
            <!--                    </AppNavbarLink>-->
            <!--                    <p>Devises</p>-->
            <!--                    <AppNavbarLink icon="hourglass-half" to="invoice-time-dues" variant="warning">-->
            <!--                        Délais de paiement des factures-->
            <!--                    </AppNavbarLink>-->

            <!--                    <AppNavbarLink icon="comments-dollar" to="vat-messages" variant="warning">-->
            <!--                        Messages TVA-->
            <!--                    </AppNavbarLink>-->
            <!--                    <AppNavbarLink icon="ruler-horizontal" to="units" variant="warning">-->
            <!--                        Unités-->
            <!--                    </AppNavbarLink>-->
            <!--                    <AppNavbarLink icon="people-group" to="teams" variant="warning">-->
            <!--                        Equipes-->
            <!--                    </AppNavbarLink>-->
            <!--                                </template>-->
            </AppNavbarItem>
            <!--            <AppNavbarItem v-if="user.isItAdmin" id="it" icon="laptop" title="Informatique">-->
            <!--                <AppDropdownItem disabled variant="warning">-->
            <!--                    Administrateur-->
            <!--                </AppDropdownItem>-->
            <!--                <a :href="database" class="dropdown-item text-warning" target="_blank">-->
            <!--                    <Fa icon="database"/>-->
            <!--                    Base de données-->
            <!--                </a>-->
            <!--                <a :href="api" class="dropdown-item text-warning" target="_blank">-->
            <!--                    <Fa icon="database"/>-->
            <!--                    Application Programming Interface (API)-->
            <!--                </a>-->
            <!--            </AppNavbarItem>-->
            <!--            <AppNavbarItem v-if="user.isLogisticsReader" id="logistics" icon="boxes" title="Logistique">-->
            <!--                            <AppDropdownItem disabled variant="success">-->
            <!--                                Lecteur-->
            <!--                            </AppDropdownItem>-->
            <!--                            <AppNavbarLink icon="shuttle-van" to="carriers" variant="success">-->
            <!--                                Transporteurs-->
            <!--                            </AppNavbarLink>-->
            <!--                <template v-if="user.isLogisticsWriter">-->
            <!--                    <AppDropdownItem disabled variant="info">-->
            <!--                        Contributeur-->
            <!--                    </AppDropdownItem>-->
            <!--                    <template v-if="user.isLogisticsAdmin">-->
            <!--                                    <AppDropdownItem disabled variant="warning">-->
            <!--                                        Administrateur-->
            <!--                                    </AppDropdownItem>-->
            <!--                                <AppNavbarLink icon="file-contract" to="incoterms" variant="warning">-->
            <!--                                    Incoterms-->
            <!--                                </AppNavbarLink>-->
            <!--                                <AppNavbarLink icon="warehouse" to="warehouse-list" variant="warning">-->
            <!--                                    Entrepots-->
            <!--                                </AppNavbarLink>-->
            <!--                    </template>-->
            <!--                </template>-->
            <!--            </AppNavbarItem>-->
            <AppNavbarItem v-if="user.isProductionReader" id="production" icon="industry" title="Production">
                <AppNavbarLink to="label-template-list" icon="tags" :variant="variantProduction">
                    Modèles d'étiquette
                </AppNavbarLink>
                <AppNavbarLink icon="tags" to="etiquette-list" :variant="variantProduction">
                    Etiquettes Générées
                </AppNavbarLink>
            <!--                <AppDropdownItem disabled variant="success">-->
            <!--                    Lecteur-->
            <!--                </AppDropdownItem>-->
            <!--                <AppNavbarLink icon="oil-well" to="engines" variant="success">-->
            <!--                    Equipements-->
            <!--                </AppNavbarLink>-->
            <!--                <AppNavbarLink icon="map-marked" to="zones" variant="success">-->
            <!--                    Zones-->
            <!--                </AppNavbarLink>-->
            <!--                <template v-if="user.isProductionAdmin">-->
            <!--                    <AppDropdownItem disabled variant="warning">-->
            <!--                        Administrateur-->
            <!--                    </AppDropdownItem>-->
            <!--                    <AppNavbarLink icon="oil-well" to="manufacturers" variant="warning">-->
            <!--                        Fabricants Equipement-->
            <!--                    </AppNavbarLink>-->
            <!--                    <AppNavbarLink icon="oil-well" to="manufacturer-engines" variant="warning">-->
            <!--                        Equipements de référence-->
            <!--                    </AppNavbarLink>-->
            <!--                    <p>Catégories d'événements des équipements (engine-events)</p>-->
            <!--                    <AppNavbarLink icon="wrench" to="engine-groups" variant="warning">-->
            <!--                        Groupes d'équipements-->
            <!--                    </AppNavbarLink>-->
            <!--                    <AppNavbarLink icon="gear" to="production parameters" variant="warning">-->
            <!--                        Paramètres-->
            <!--                    </AppNavbarLink>-->
            <!--                </template>-->
            </AppNavbarItem>
            <!--            <AppNavbarItem v-if="user.isProjectReader" id="project" icon="project-diagram" title="Projet">-->
            <!--                <AppDropdownItem disabled variant="success">-->
            <!--                    Lecteur-->
            <!--                </AppDropdownItem>-->
            <!--                <AppNavbarLink icon="atom" to="project-operations" variant="success">-->
            <!--                    Opérations-->
            <!--                </AppNavbarLink>-->
            <!--                <template v-if="user.isProjectAdmin">-->
            <!--                    <AppDropdownItem disabled variant="warning">-->
            <!--                        Administrateur-->
            <!--                    </AppDropdownItem>-->
            <!--                    <AppNavbarLink icon="layer-group" to="product-families" variant="warning">-->
            <!--                        Familles de produits-->
            <!--                    </AppNavbarLink>-->
            <!--                    <AppNavbarLink brands icon="elementor" to="operation-types" variant="warning">-->
            <!--                        Types d'opérations-->
            <!--                    </AppNavbarLink>-->
            <!--                    <AppNavbarLink icon="gear" to="project parameters" variant="warning">-->
            <!--                        Paramètres-->
            <!--                    </AppNavbarLink>-->
            <!--                </template>-->
            <!--            </AppNavbarItem>-->
            <!--            <AppNavbarItem v-if="user.isQualityReader" id="quality" icon="certificate" title="Qualité">-->
            <!--                <AppDropdownItem disabled variant="success">-->
            <!--                    Lecteur-->
            <!--                </AppDropdownItem>-->
            <!--                <AppNavbarLink icon="check-circle" to="component-reference-values" variant="success">-->
            <!--                    Relevés qualités composants-->
            <!--                </AppNavbarLink>-->
            <!--                <template v-if="user.isQualityAdmin">-->
            <!--                    <AppDropdownItem disabled variant="warning">-->
            <!--                        Administrateur-->
            <!--                    </AppDropdownItem>-->
            <!--                    <AppNavbarLink brands icon="elementor" to="reject-types" variant="warning">-->
            <!--                        Catégories de rejets de production-->
            <!--                    </AppNavbarLink>-->
            <!--                    <AppNavbarLink brands icon="elementor" to="quality-types" variant="warning">-->
            <!--                        Critères qualités-->
            <!--                    </AppNavbarLink>-->
            <!--                </template>-->
            <!--            </AppNavbarItem>-->
            <!--            <AppNavbarItem v-if="user.isHrReader" id="hr" icon="male" title="RH">-->
            <!--                <AppDropdownItem disabled variant="success">-->
            <!--                    Lecteur-->
            <!--                </AppDropdownItem>-->
            <!--                <AppNavbarLink icon="user-graduate" to="out-trainers" variant="success">-->
            <!--                    Formateurs extérieurs-->
            <!--                </AppNavbarLink>-->
            <!--                <template v-if="user.isHrAdmin">-->
            <!--                    <AppDropdownItem disabled variant="warning">-->
            <!--                        Administrateur-->
            <!--                    </AppDropdownItem>-->
            <!--                    <AppNavbarLink brands icon="elementor" to="event-types" variant="warning">-->
            <!--                        Catégories d'événements des employés-->
            <!--                    </AppNavbarLink>-->
            <!--                    <AppNavbarLink icon="clock" to="time-slots" variant="warning">-->
            <!--                        Plages horaires-->
            <!--                    </AppNavbarLink>-->
            <!--                    <AppNavbarLink icon="gear" to="hr parameters" variant="warning">-->
            <!--                        Paramètres-->
            <!--                    </AppNavbarLink>-->
            <!--                    <AppNavbarLink icon="signal" to="skill-types" variant="warning">-->
            <!--                        Types de Compétences-->
            <!--                    </AppNavbarLink>-->
            <!--                </template>-->
            <!--            </AppNavbarItem>-->
            <!--            <AppNavbarItem v-if="user.isSellingReader" id="selling" icon="euro-sign" title="Ventes">-->
            <!--                <AppDropdownItem disabled variant="success">-->
            <!--                    Lecteur-->
            <!--                </AppDropdownItem>-->
            <!--                &lt;!&ndash; <AppNavbarLink icon="user-graduate" to="out-trainers" variant="success">-->
            <!--                    Formateurs extérieurs-->
            <!--                </AppNavbarLink> &ndash;&gt;-->
            <!--                <template v-if="user.isSellingAdmin">-->
            <!--                    <AppDropdownItem disabled variant="warning">-->
            <!--                        Administrateur-->
            <!--                    </AppDropdownItem>-->
            <!--                    <AppNavbarLink icon="gear" to="selling parameters" variant="warning">-->
            <!--                        Paramètres-->
            <!--                    </AppNavbarLink>-->
            <!--                </template>-->
            <!--            </AppNavbarItem>-->
        </ul>
        <div class="align-items-center d-flex flex-row">
            <AppSuspense variant="white">
                <AppNotifications/>
            </AppSuspense>
            <AppNavbarUser/>
        </div>
    </div>
</template>
