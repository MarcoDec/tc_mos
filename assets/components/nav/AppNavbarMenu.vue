<script setup>
    import AppNavbarItem from './AppNavbarItem.vue'
    import AppNavbarLink from './link/AppNavbarLink.vue'
    import AppNavbarUser from './AppNavbarUser.vue'
    import AppNotifications from './notification/AppNotifications.vue'
    import useUser from '../../stores/security'

    const database = `${location.protocol}//${location.hostname}:8080`
    const user = useUser()
</script>

<template>
    <div class="collapse navbar-collapse">
        <ul class="me-auto navbar-nav">
            <AppNavbarItem v-if="user.isPurchaseReader" id="purchase" icon="shopping-bag" title="Achats">
                <template v-if="user.isPurchaseAdmin">
                    <AppDropdownItem disabled variant="warning">
                        Administrateur
                    </AppDropdownItem>
                    <AppNavbarLink icon="magnet" to="attributes" variant="warning">
                        Attributs
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
            </AppNavbarItem>
        </ul>
    </div>
    <AppSuspense variant="white">
        <AppNotifications/>
    </AppSuspense>
    <AppNavbarUser/>
</template>
