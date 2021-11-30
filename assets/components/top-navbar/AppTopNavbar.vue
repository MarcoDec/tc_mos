<script lang="ts" setup>
    import type {Actions, State} from '../../store/security'
    import {
      useNamespacedActions,
      useNamespacedGetters,
      useNamespacedMutations,
      useNamespacedState
    } from 'vuex-composition-helpers'
    import {ActionTypes, Mutations, MutationTypes} from '../../store/security'
    import {useRouter} from 'vue-router'

    const hasUser = useNamespacedGetters('users', ['hasUser']).hasUser
    const logout = useNamespacedActions<Actions>('users', [ActionTypes.LOGOUT_USERS])[ActionTypes.LOGOUT_USERS]
    const name = useNamespacedState<State>('users', ['username']).username
    const error = useNamespacedMutations<Mutations>('users', [MutationTypes.LOGOUT])[MutationTypes.LOGOUT]
    const router = useRouter()

    async function onLogout(): Promise<void> {
        await logout()
        await router.push({name: 'login'})
        error()
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
            <AppBtn variant="danger" @click="onLogout">
                <Fa icon="sign-out-alt"/>
            </AppBtn>
        </div>
    </AppNavbar>
</template>
