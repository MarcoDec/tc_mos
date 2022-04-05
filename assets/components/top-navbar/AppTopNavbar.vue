<script lang="ts" setup>
    import type {Actions, Getters, State} from '../../store/security'
    import {useNamespacedActions, useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'
    import {useRouter} from 'vue-router'

    const hasUser = useNamespacedGetters<Getters>('security', ['hasUser']).hasUser
    const logout = useNamespacedActions<Actions>('security', ['logout']).logout
    const name = useNamespacedState<State>('security', ['username']).username
    const router = useRouter()

    async function handleLogout(): Promise<void> {
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
            <AppNavbarItem id="nav-purchase" icon="shopping-bag" title="Achats">
                <AppNavbarLink icon="layer-group" to="component-families">
                    Familles de composants
                </AppNavbarLink>
                 <AppNavbarLink icon="route" to="needs">
                    Calcul des besoins
                </AppNavbarLink>
            </AppNavbarItem>
            <AppNavbarItem id="nav-purchase" icon="industry" title="Production">
                <AppNavbarLink icon="layer-group" to="product-families">
                    Familles de produits
                </AppNavbarLink>
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
