<script lang="ts" setup>
    import {ActionTypes, MutationTypes} from '../../store/security'
    import type {Actions, Getters, Mutations, State} from '../../store/security'
    import {
        useMutations,
        useNamespacedActions,
        useNamespacedGetters,
        useNamespacedMutations,
        useNamespacedState
    } from 'vuex-composition-helpers'
    import {MutationTypes as MutationSpinner} from '../../store'
    import type {Mutations as MutationsSpinner} from '../../store'
    import {useRouter} from 'vue-router'

    const error = useNamespacedMutations<Mutations>('users', [MutationTypes.LOGOUT])[MutationTypes.LOGOUT]
    const hasUser = useNamespacedGetters<Getters>('users', ['hasUser']).hasUser
    const loader = useMutations<MutationsSpinner>([MutationSpinner.SPINNER])[MutationSpinner.SPINNER]
    const logout = useNamespacedActions<Actions>('users', [ActionTypes.LOGOUT_USERS])[ActionTypes.LOGOUT_USERS]
    const name = useNamespacedState<State>('users', ['username']).username
    const router = useRouter()

    async function handleLogout(): Promise<void> {
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
        <div v-if="hasUser" class="text-white">
            <Fa icon="user-circle"/>
            {{ name }}
            <AppBtn variant="danger" @click="handleLogout">
                <Fa icon="sign-out-alt"/>
            </AppBtn>
        </div>
    </AppNavbar>
</template>
