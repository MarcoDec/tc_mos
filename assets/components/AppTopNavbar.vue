<script setup>
    import {useNamespacedActions, useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'
    import {useRouter} from 'vue-router'

    const {
        hasUser,
        isItAdmin,
        isManagementAdmin,
        isManagementReader,
        isPurchaseAdmin,
        isPurchaseReader
    } = useNamespacedGetters('security', [
        'hasUser',
        'isItAdmin',
        'isManagementAdmin',
        'isManagementReader',
        'isPurchaseAdmin',
        'isPurchaseReader'
    ])
    const logout = useNamespacedActions('security', ['logout']).logout
    const name = useNamespacedState('security', ['username']).username
    const router = useRouter()

    async function handleLogout() {
        await logout()
        await router.push({name: 'login'})
    }
</script>

<template>
    <AppNavbar>
        <AppNavbarBrand to="home">
            T-Concept
        </AppNavbarBrand>
        <AppNavbarCollapse>
            <AppNavbarItem v-if="isPurchaseReader" id="nav-purchase" icon="shopping-bag" title="Achats">
                <template v-if="isPurchaseAdmin">
                    <AppDropdownItem variant="warning">
                        Administrateur
                    </AppDropdownItem>
                    <AppNavbarLink disabled icon="magnet" to="attribute-list" variant="danger">
                        Attributs
                    </AppNavbarLink>
                    <AppNavbarLink icon="layer-group" to="component-families" variant="warning">
                        Familles de composants
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="isManagementReader" id="nav-purchase" icon="sitemap" title="Direction">
                <template v-if="isManagementAdmin">
                    <AppDropdownItem variant="warning">
                        Administrateur
                    </AppDropdownItem>
                    <AppNavbarLink icon="palette" to="color-list" variant="warning">
                        Couleurs
                    </AppNavbarLink>
                </template>
                <!-- AppNavbarLink icon="hourglass-half" to="invoiceTimeDue-list">
                    Délai de paiement d'une facture
                </AppNavbarLink>
                <AppNavbarLink icon="print" to="Printer-list">
                    Imprimante
                </AppNavbarLink>
                <AppNavbarLink icon="ruler-horizontal" to="Unit-list">
                    Unité
                </AppNavbarLink>
                <AppNavbarLink icon="comments-dollar" to="VatMessage-list">
                    Message TVA
                </AppNavbarLink -->
            </AppNavbarItem>
            <AppNavbarItem v-if="isItAdmin" id="nav-purchase" icon="laptop" title="Informatique">
                <AppDropdownItem variant="warning">
                    Administrateur
                </AppDropdownItem>
                <a class="dropdown-item text-warning" href="/api">
                    <Fa icon="server"/>
                    API
                </a>
                <!-- AppNavbarLink icon="laptop-code" to="ITRequest-list">
                    Demande
                </AppNavbarLink -->
            </AppNavbarItem>
            <!-- AppNavbarItem id="nav-purchase" icon="boxes" title="Logistique">
                <AppNavbarLink icon="shuttle-van" to="Carrier-list">
                    Transporteur
                </AppNavbarLink>
                <AppNavbarLink icon="file-contract" to="Incoterms-list">
                    Incoterms
                </AppNavbarLink>
            </AppNavbarItem>
            <AppNavbarItem id="nav-purchase" icon="industry" title="Production">
                <AppNavbarLink icon="map-marked" to="Zone-list">
                    Zone
                </AppNavbarLink>
                <AppNavbarLink icon="wrench" to="Group-list">
                    Groupe d'équipements
                </AppNavbarLink>
                <AppNavbarLink icon="calendar-day" to="EngineEvent-list">
                    Catégorie d'événement des équipements
                </AppNavbarLink>
            </AppNavbarItem>
            <AppNavbarItem id="nav-purchase" icon="industry" title="Projet">
                <AppNavbarLink icon="layer-group" to="product-families">
                    Familles de produits
                </AppNavbarLink>
                <AppNavbarLink icon="atom" to="operation-list">
                    Opération
                </AppNavbarLink>
                <AppNavbarLink brands icon="elementor" to="OperationType-list">
                    Type d'opération
                </AppNavbarLink>
            </AppNavbarItem>
            <AppNavbarItem id="nav-purchase" icon="certificate" title="Qualité">
                <AppNavbarLink icon="check-circle" to="ComponentReferenceValue-list">
                    Relevé qualité composant
                </AppNavbarLink>
                <AppNavbarLink brands icon="elementor" to="RejectType-list">
                    Catégorie de rejet de production
                </AppNavbarLink>
                <AppNavbarLink brands icon="elementor" to="QualityType-list">
                    Critère qualité
                </AppNavbarLink>
            </AppNavbarItem>
            <AppNavbarItem id="nav-purchase" icon="male" title="RH">
                <AppNavbarLink icon="user-graduate" to="OutTrainer-list">
                    Formateur extérieur
                </AppNavbarLink>
                <AppNavbarLink brands icon="elementor" to="EmployeeEventType-list">
                    Catégories d'événement des employés
                </AppNavbarLink>
                <AppNavbarLink icon="clock" to="TimeSlot-list">
                    Plage horaire
                </AppNavbarLink>
            </AppNavbarItem -->
        </AppNavbarCollapse>
        <div v-if="hasUser" class="text-white">
            <Fa icon="user-circle"/>
            {{ name }}
            <AppBtn variant="danger" @click="handleLogout">
                <Fa icon="sign-out-alt"/>
            </AppBtn>
        </div>
    </AppNavbar>
</template>
