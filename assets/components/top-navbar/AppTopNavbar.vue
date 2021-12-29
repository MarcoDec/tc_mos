<script lang="ts" setup>
    import type {Actions, Getters, State} from '../../store/security'
    import {
        useMutations,
        useNamespacedActions,
        useNamespacedGetters,
        useNamespacedState
    } from 'vuex-composition-helpers'
    import {ActionTypes} from '../../store/security'
    import {MutationTypes as MutationSpinner} from '../../store'
    import type {Mutations as MutationsSpinner} from '../../store'
    import {useRouter} from 'vue-router'

    const hasUser = useNamespacedGetters<Getters>('security', ['hasUser']).hasUser
    const loader = useMutations<MutationsSpinner>([MutationSpinner.SPINNER])[MutationSpinner.SPINNER]
    const logout = useNamespacedActions<Actions>('security', [ActionTypes.LOGOUT_USERS])[ActionTypes.LOGOUT_USERS]
    const name = useNamespacedState<State>('security', ['username']).username
    const router = useRouter()

    async function handleLogout(): Promise<void> {
        loader()
        try {
            await logout()
            await router.push({name: 'login'})
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
