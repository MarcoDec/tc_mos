<script lang="ts" setup>
    import {ActionTypes} from '../../../store/security'
    import type {Actions} from '../../../store/security'
    import type {FormField} from '../../../types/bootstrap-5'
    import {ref} from 'vue'
    import router from '../../router'
    import {useNamespacedActions} from 'vuex-composition-helpers'

    const fields: FormField[] = [
        {label: 'Identifiant', name: 'username'},
        {label: 'Mot de passe', name: 'password', type: 'password'}
    ]

    const formData = ref<{password: string | null, username: string | null}>({password: null, username: null})
    const fetchUsers = useNamespacedActions<Actions>('users', [ActionTypes.FETCH_USERS])[ActionTypes.FETCH_USERS]

    async function handleClick(): Promise<void> {
        await fetchUsers(formData.value)
        await router.push({name: 'home'})
    }
</script>

<template>
    <AppRow>
        <AppCol>
            <AppCard class="bg-blue">
                <AppForm v-model:values="formData" :fields="fields" @submit="handleClick"/>
            </AppCard>
        </AppCol>
    </AppRow>
</template>
