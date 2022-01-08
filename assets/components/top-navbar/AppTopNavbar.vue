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
                <AppNavbarLink icon="layer-group" to="families">
                    Familles
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
