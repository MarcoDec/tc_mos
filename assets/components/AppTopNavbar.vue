<script setup>
    import {useNamespacedActions, useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'
    import {useRouter} from 'vue-router'

    const {
        hasUser,
        isProductionAdmin,
        isProductionReader,
        isPurchaseAdmin,
        isPurchaseReader
    } = useNamespacedGetters('security', [
        'hasUser',
        'isProductionAdmin',
        'isProductionReader',
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
                    <AppNavbarLink icon="layer-group" to="component-families" variant="warning">
                        Familles de composants
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
            <AppNavbarItem v-if="isProductionReader" id="nav-purchase" icon="industry" title="Projet">
                <template v-if="isProductionAdmin">
                    <AppDropdownItem variant="warning">
                        Administrateur
                    </AppDropdownItem>
                    <AppNavbarLink icon="layer-group" to="product-families" variant="warning">
                        Familles de produits
                    </AppNavbarLink>
                </template>
            </AppNavbarItem>
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
