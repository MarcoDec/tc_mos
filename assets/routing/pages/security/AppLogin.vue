<script lang="ts" setup>
    import {ActionTypes} from '../../../store/security/actions'
    import type {Actions} from '../../../store/security/actions'
    import AppForm from '../../../components/bootstrap-5/form/AppForm.vue'
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
        router.push('home')

    }
</script>

<template>
    <AppRow>
        <AppCard class="bg-blue col">
            <AppForm v-model:values="formData" :fields="fields" @submit="handleClick"/>
        </AppCard>
    </AppRow>
</template>
