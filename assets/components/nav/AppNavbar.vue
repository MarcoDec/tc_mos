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
<!--                <AppDropdownItem disabled variant="success">-->
<!--                    Lecteur-->
<!--                </AppDropdownItem>-->
<!--                <AppNavbarLink icon="user-tag" to="supplier-show" variant="success">-->
<!--                    Fournisseur-->
<!--                </AppNavbarLink>-->
<!--                <AppNavbarLink icon="user-tag" to="supplier-list" variant="warning">-->
<!--                    Fournisseur List-->
<!--                </AppNavbarLink>-->
<!--                <AppNavbarLink icon="user-tag" to="component-list" variant="warning">-->
<!--                    Composant List-->
<!--                </AppNavbarLink>-->
<!--                <AppNavbarLink icon="user-tag" to="employee-list" variant="warning">-->
<!--                    Employee List-->
<!--                </AppNavbarLink>-->
<!--                <AppNavbarLink icon="user-tag" to="customer-list" variant="warning">-->
<!--                    Client List-->
<!--                </AppNavbarLink>-->
<!--                <AppNavbarLink icon="user-tag" to="product-list" variant="warning">-->
<!--                    Product List-->
<!--                </AppNavbarLink>-->
<!--                <AppNavbarLink icon="user-tag" to="manufacturingOrder-list" variant="warning">-->
<!--                    manufacturingOrder List-->
<!--                </AppNavbarLink>-->
<!--                <template v-if="user.isPurchaseAdmin">-->
<!--                    <AppDropdownItem disabled variant="warning">-->
<!--                        Administrateur-->
<!--                    </AppDropdownItem>-->
<!--                    <AppNavbarLink disabled icon="magnet" to="attributes" variant="danger">-->
<!--                        Attributs-->
<!--                    </AppNavbarLink>-->
<!--                    <AppNavbarLink icon="layer-group" to="component-families" variant="warning">-->
<!--                        Familles de composants-->
<!--                    </AppNavbarLink>-->
<!--                </template>-->
<!--            </AppNavbarItem>-->
<!--            <AppNavbarItem v-if="user.isManagementReader" id="management" icon="sitemap" title="Direction">-->
<!--                <template v-if="user.isManagementAdmin">-->
<!--                    <AppDropdownItem disabled variant="warning">-->
<!--                        Administrateur-->
<!--                    </AppDropdownItem>-->
<!--                    <AppNavbarLink icon="palette" to="colors" variant="warning">-->
<!--                        Couleurs-->
<!--                    </AppNavbarLink>-->
<!--                    <AppNavbarLink icon="hourglass-half" to="invoice-time-dues" variant="warning">-->
<!--                        Délais de paiement des factures-->
<!--                    </AppNavbarLink>-->
<!--                    <AppNavbarLink icon="euro-sign" to="currencies" variant="warning">-->
<!--                        Devises-->
<!--                    </AppNavbarLink>-->
<!--                    <AppNavbarLink disabled icon="print" to="printers" variant="danger">-->
<!--                        Imprimantes-->
<!--                    </AppNavbarLink>-->
<!--                    <AppNavbarLink icon="comments-dollar" to="vat-messages" variant="warning">-->
<!--                        Messages TVA-->
<!--                    </AppNavbarLink>-->
<!--                    <AppNavbarLink icon="ruler-horizontal" to="units" variant="warning">-->
<!--                        Unités-->
<!--                    </AppNavbarLink>-->
<!--                </template>-->
<!--            </AppNavbarItem>-->
<!--            <AppNavbarItem id="it" icon="laptop" title="Informatique">-->
<!--                <AppNavbarLink disabled icon="laptop-code" to="it-requests" variant="danger">-->
<!--                    Demandes-->
<!--                </AppNavbarLink>-->
<!--                <template v-if="user.isItAdmin">-->
<!--                    <AppDropdownItem disabled variant="warning">-->
<!--                        Administrateur-->
<!--                    </AppDropdownItem>-->
<!--                    <a class="dropdown-item text-warning" href="/api" target="_blank">-->
<!--                        <Fa icon="server"/>-->
<!--                        API-->
<!--                    </a>-->
<!--                    <a :href="database" class="dropdown-item text-warning" target="_blank">-->
<!--                        <Fa icon="database"/>-->
<!--                        Base de données-->
<!--                    </a>-->
<!--                </template>-->
<!--            </AppNavbarItem>-->
<!--            <AppNavbarItem v-if="user.isLogisticsReader" id="logistics" icon="boxes" title="Logistique">-->
<!--                <AppDropdownItem disabled variant="success">-->
<!--                    Lecteur-->
<!--                </AppDropdownItem>-->
<!--                <AppNavbarLink icon="shuttle-van" to="carriers" variant="success">-->
<!--                    Transporteurs-->
<!--                </AppNavbarLink>-->
<!--                <template v-if="user.isLogisticsAdmin">-->
<!--                    <AppDropdownItem disabled variant="warning">-->
<!--                        Administrateur-->
<!--                    </AppDropdownItem>-->
<!--                    <AppNavbarLink icon="file-contract" to="incoterms" variant="warning">-->
<!--                        Incoterms-->
<!--                    </AppNavbarLink>-->
<!--                </template>-->
<!--            </AppNavbarItem>-->
<!--            <AppNavbarItem-->
<!--                    v-if="user.isProductionReader" id="production" icon="industry"-->
<!--                    title="Production">-->
<!--                <AppDropdownItem disabled variant="success">-->
<!--                    Lecteur-->
<!--                </AppDropdownItem>-->
<!--                <AppNavbarLink disabled icon="map-marked" to="zones" variant="danger">-->
<!--                    Zones-->
<!--                </AppNavbarLink>-->
<!--                <template v-if="user.isProductionAdmin">-->
<!--                    <AppDropdownItem disabled variant="warning">-->
<!--                        Administrateur-->
<!--                    </AppDropdownItem>-->
<!--                    <AppNavbarLink disabled icon="calendar-day" to="engine-events" variant="danger">-->
<!--                        Catégories d'événements des équipements-->
<!--                    </AppNavbarLink>-->
<!--                    <AppNavbarLink icon="wrench" to="engine-groups" variant="warning">-->
<!--                        Groupes d'équipements-->
<!--                    </AppNavbarLink>-->
<!--                </template>-->
<!--            </AppNavbarItem>-->
<!--            <AppNavbarItem v-if="user.isProjectReader" id="project" icon="project-diagram" title="Projet">-->
<!--                <AppDropdownItem disabled variant="success">-->
<!--                    Lecteur-->
<!--                </AppDropdownItem>-->
<!--                <AppNavbarLink disabled icon="atom" to="operations" variant="danger">-->
<!--                    Opérations-->
<!--                </AppNavbarLink>-->
<!--                <template v-if="user.isProjectAdmin">-->
<!--                    <AppDropdownItem disabled variant="warning">-->
<!--                        Administrateur-->
<!--                    </AppDropdownItem>-->
<!--                    <AppNavbarLink icon="layer-group" to="product-families" variant="warning">-->
<!--                        Familles de produits-->
<!--                    </AppNavbarLink>-->
<!--                    <AppNavbarLink brands disabled icon="elementor" to="operation-types" variant="danger">-->
<!--                        Types d'opérations-->
<!--                    </AppNavbarLink>-->
<!--                </template>-->
<!--            </AppNavbarItem>-->
<!--            <AppNavbarItem v-if="user.isQualityReader" id="quality" icon="certificate" title="Qualité">-->
<!--                <AppDropdownItem disabled variant="success">-->
<!--                    Lecteur-->
<!--                </AppDropdownItem>-->
<!--                <AppNavbarLink-->
<!--                        disabled icon="check-circle" to="component-reference-values"-->
<!--                        variant="danger">-->
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
<!--                </template>-->
<!--            </AppNavbarItem>-->
<!--        </ul>-->
<!--    </div>-->
<!--    <AppNavbarUser/>-->
<!--</template>-->
