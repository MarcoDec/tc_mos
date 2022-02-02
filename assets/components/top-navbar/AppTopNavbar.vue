<script lang="ts" setup>
    import type {Actions, Getters, State} from '../../store/security'
    import {useNamespacedActions, useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'
    import {useRouter} from 'vue-router'

    const hasUser = useNamespacedGetters<Getters>('security', ['hasUser']).hasUser
    const logout = useNamespacedActions<Actions>('security', ['logout']).logout
    const username = useNamespacedState<State>('security', ['username']).username
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
        <div v-if="hasUser" class="text-white">
          <div>
            <Dropdown title="Achat"/>
          </div>
            <Fa icon="user-circle"/>
            {{ username }}
            <AppBtn variant="danger" @click="handleLogout">
                <Fa icon="sign-out-alt"/>
            </AppBtn>
        </div>
    </AppNavbar>
</template>
