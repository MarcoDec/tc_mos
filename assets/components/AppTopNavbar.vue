<script setup>
    import {useRepo, useRouter} from '../composition'
    import AppDropdownItem from './bootstrap-5/navbar/AppDropdownItem'
    import AppNavbar from './bootstrap-5/navbar/AppNavbar'
    import AppNavbarBrand from './bootstrap-5/navbar/AppNavbarBrand'
    import AppNavbarCollapse from './bootstrap-5/navbar/AppNavbarCollapse'
    import AppNavbarItem from './bootstrap-5/navbar/AppNavbarItem.vue'
    import AppNavbarLink from './bootstrap-5/navbar/AppNavbarLink'
    import {EmployeeRepository} from '../store/modules'
    import {computed} from 'vue'

    const repo = useRepo(EmployeeRepository)
    const {router} = useRouter()
    const hasUser = computed(() => repo.hasUser)
    const user = computed(() => repo.user)

    async function logout() {
        await repo.logout('login')
        router.push({name: 'login'})
    }
</script>

<template>
    <AppNavbar>
        <AppNavbarBrand to="home">
            T-Concept
        </AppNavbarBrand>
        <template v-if="hasUser">
            <AppNavbarCollapse>
                <AppNavbarItem v-if="user.isProjectReader" id="purchase" icon="shopping-bag" title="Achats">
                    <template v-if="user.isProjectAdmin">
                        <AppDropdownItem variant="warning">
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
                        <AppDropdownItem variant="warning">
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
                <AppNavbarItem v-if="user.isItAdmin" id="it" icon="laptop" title="Informatique">
                    <AppDropdownItem variant="warning">
                        Administrateur
                    </AppDropdownItem>
                    <a class="dropdown-item text-warning" href="/api" target="_blank">
                        <Fa icon="server"/>
                        API
                    </a>
                    <a class="dropdown-item text-warning" href="http://localhost:8080" target="_blank">
                        <Fa icon="database"/>
                        Base de données
                    </a>
                </AppNavbarItem>
                <AppNavbarItem v-if="user.isProjectReader" id="project" icon="industry" title="Projet">
                    <template v-if="user.isProjectAdmin">
                        <AppDropdownItem variant="warning">
                            Administrateur
                        </AppDropdownItem>
                        <AppNavbarLink icon="layer-group" to="product-families" variant="warning">
                            Familles de produits
                        </AppNavbarLink>
                    </template>
                </AppNavbarItem>
            </AppNavbarCollapse>
            <div class="text-white">
                <Fa icon="user-circle"/>
                {{ user.name }}
                <AppBtn icon="sign-out-alt" title="Déconnexion" variant="danger" @click="logout"/>
            </div>
        </template>
    </AppNavbar>
</template>
