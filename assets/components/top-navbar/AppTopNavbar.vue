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
        <AppNavbarCollapse>
            <AppNavbarItem id="nav-purchase" icon="shopping-bag" title="Achats">
                <AppNavbarLink icon="layer-group" to="families">
                    Familles
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
