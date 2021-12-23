<script lang="ts" setup>
    import type {Actions, State} from '../../../store/security'
    import {useMutations, useNamespacedActions, useNamespacedState} from 'vuex-composition-helpers'
    import {ActionTypes} from '../../../store/security'
    import type {FormField} from '../../../types/bootstrap-5'
    import {MutationTypes} from '../../../store/mutation'
    import {ref} from 'vue'
    import router from '../../router'

    const fields: FormField[] = [
        {label: 'Identifiant', name: 'username'},
        {label: 'Mot de passe', name: 'password', type: 'password'}
    ]

    const formData = ref<{password: string | null, username: string | null}>({password: null, username: null})
    const fetchUsers = useNamespacedActions<Actions>('users', [ActionTypes.LOGIN])[ActionTypes.LOGIN]
    const {code, error: showError, msgError}
        = useNamespacedState<State>('users', ['code', 'error', 'msgError'])

    const loader = useMutations([MutationTypes.SPINNER])[MutationTypes.SPINNER]
    async function handleClick(): Promise<void> {
        loader()
        try {
            await fetchUsers(formData.value)
            await router.push({name: 'home'})
        } finally {
            loader()
        }
    }
</script>

<template>
    <AppRow>
        <div v-if="showError" class="alert alert-danger" role="alert">
            <span class="badge bg-danger">Erreur {{ code }}</span>
            {{ msgError }}
        </div>
        <AppCard class="bg-blue col">
            <AppForm v-model:values="formData" :fields="fields" @submit="handleClick">
                <template #buttons>
                    <AppBtn class="float-end" type="submit">
                        Connexion
                    </AppBtn>
                </template>
            </AppForm>
        </AppCard>
    </AppRow>
</template>
