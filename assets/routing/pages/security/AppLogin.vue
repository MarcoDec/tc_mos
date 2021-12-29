<script lang="ts" setup>
    import {useMutations, useNamespacedActions} from 'vuex-composition-helpers'
    import {ActionTypes} from '../../../store/security'
    import type {Actions} from '../../../store/security'
    import type {FormField} from '../../../types/bootstrap-5'
    import {MutationTypes as MutationTypesSpinner} from '../../../store'
    import type {Mutations as MutationsSpinner} from '../../../store'
    import {ref} from 'vue'
    import router from '../../router'

    const fields: FormField[] = [
        {label: 'Identifiant', name: 'username'},
        {label: 'Mot de passe', name: 'password', type: 'password'}
    ]
    const fetchUsers = useNamespacedActions<Actions>('security', [ActionTypes.LOGIN])[ActionTypes.LOGIN]
    const formData = ref<{password: string | null, username: string | null}>({password: null, username: null})
    const loader = useMutations<MutationsSpinner>([MutationTypesSpinner.SPINNER])[MutationTypesSpinner.SPINNER]

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
        <AppCard class="bg-blue col">
            <AppForm v-model="formData" :fields="fields" @submit="handleClick">
                <AppBtn type="submit">
                    Connexion
                </AppBtn>
            </AppForm>
        </AppCard>
    </AppRow>
</template>
