<script lang="ts" setup>
import {ActionTypes, Mutations, MutationTypes, State} from '../../../store/security'
    import type {Actions} from '../../../store/security'
    import type {FormField} from '../../../types/bootstrap-5'
    import {ref} from 'vue'
    import router from '../../router'
    import {
      useNamespacedActions,
      useNamespacedGetters,
      useNamespacedMutations,
      useNamespacedState
    } from 'vuex-composition-helpers'

    const fields: FormField[] = [
        {label: 'Identifiant', name: 'username'},
        {label: 'Mot de passe', name: 'password', type: 'password'}
    ]

    const formData = ref<{password: string | null, username: string | null}>({password: null, username: null})
    const fetchUsers = useNamespacedActions<Actions>('users', [ActionTypes.FETCH_USERS])[ActionTypes.FETCH_USERS]
    const error = useNamespacedMutations<Mutations>('users', [MutationTypes.ERROR])[MutationTypes.ERROR]
    const showError = useNamespacedState<State>('users', ['error']).error

    async function handleClick(): Promise<void> {
      try {
        await fetchUsers(formData.value)
        await router.push({name: 'home'})
      }
      catch (e) {
        error()
      }
    }
</script>

<template>
    <AppRow>

      <AppCard class="bg-red"  v-if="showError">
        <span>Identifiants invalides.</span>
      </AppCard>
        <AppCard class="bg-blue col">
            <AppForm v-model:values="formData" :fields="fields" @submit="handleClick"/>
        </AppCard>
    </AppRow>
</template>
