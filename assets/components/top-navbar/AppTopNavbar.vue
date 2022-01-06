<script lang="ts" setup>
    import {ActionTypes, MutationTypes} from '../../store/security'
    import type {Actions, Mutations, State} from '../../store/security'
    import {
        useMutations,
        useNamespacedActions,
        useNamespacedGetters,
        useNamespacedMutations,
        useNamespacedState
    } from 'vuex-composition-helpers'
    import {MutationTypes as MutationSpinner} from '../../store/mutation'
    import {useRouter} from 'vue-router'

    const hasUser = useNamespacedGetters('users', ['hasUser']).hasUser
    const logout = useNamespacedActions<Actions>('users', [ActionTypes.LOGOUT_USERS])[ActionTypes.LOGOUT_USERS]
    const name = useNamespacedState<State>('users', ['username']).username
    const error = useNamespacedMutations<Mutations>('users', [MutationTypes.LOGOUT])[MutationTypes.LOGOUT]
    const loader = useMutations([MutationSpinner.SPINNER])[MutationSpinner.SPINNER]

    const router = useRouter()

    async function onLogout(): Promise<void> {
        loader()
        try {
            await logout()
            await router.push({name: 'login'})
        } catch (e) {
            error()
        } finally {
            loader()
        }
    }
</script>

<template>
    <AppNavbar>
        <AppNavbarBrand to="home">
            T-Concept
        </AppNavbarBrand>
        <AppNavbarCollapse v-if="hasUser">
            <AppNavbarItem id="nav-purchase" icon="shopping-bag" title="Achats">
                <AppNavbarLink icon="layer-group" to="attribute-list">
                    Attribut
                </AppNavbarLink>
            </AppNavbarItem>
            <AppNavbarItem id="nav-purchase" icon="sitemap" title="Direction">
                <AppNavbarLink icon="palette" to="color-list">
                    Couleur
                </AppNavbarLink>
                <AppNavbarLink icon="hourglass-half" to="invoiceTimeDue-list">
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
                </AppNavbarLink>
            </AppNavbarItem>
            <AppNavbarItem id="nav-purchase" icon="laptop" title="Informatique">
                <AppNavbarLink icon="laptop-code" to="ITRequest-list">
                    Demande
                </AppNavbarLink>
            </AppNavbarItem>
            <AppNavbarItem id="nav-purchase" icon="boxes" title="Logistique">
                <AppNavbarLink icon="shuttle-van" to="Carrier-list">
                    Transporteur
                </AppNavbarLink>
                <AppNavbarLink icon="contract" to="Incoterms-list">
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
            <AppNavbarItem id="nav-purchase" icon="project-diagram" title="Projet">
                <AppNavbarLink icon="elementor" to="OperationType-list">
                    Type d'opération
                </AppNavbarLink>
                <AppNavbarLink icon="atom" to="operation-list">
                    Opération
                </AppNavbarLink>
            </AppNavbarItem>
            <AppNavbarItem id="nav-purchase" icon="certificate" title="Qualité">
                <AppNavbarLink icon="check-circle" to="ComponentReferenceValue-list">
                    Relevé qualité composant
                </AppNavbarLink>
                <AppNavbarLink icon="elementor" to="RejectType-list">
                    Catégorie de rejet de production
                </AppNavbarLink><AppNavbarLink icon="elementor" to="QualityType-list">
                    Critère qualité
                </AppNavbarLink>
            </AppNavbarItem>
            <AppNavbarItem id="nav-purchase" icon="male" title="RH">
                <AppNavbarLink icon="user-graduate" to="OutTrainer-list">
                    Formateur extérieur
                </AppNavbarLink>
                <AppNavbarLink icon="elementor" to="EmployeeEventType-list">
                    Catégories d'événement des employés
                </AppNavbarLink>
                <AppNavbarLink icon="clock" to="TimeSlot-list">
                    Plage horaire
                </AppNavbarLink>
            </AppNavbarItem>
        </AppNavbarCollapse>
        <div v-if="hasUser">
            <div class="text-white">
                <Fa icon="user-circle"/>
                {{ name }}
                <AppBtn variant="danger" @click="onLogout">
                    <Fa icon="sign-out-alt"/>
                </AppBtn>
            </div>
        </div>
    </AppNavbar>
</template>
