<script lang="ts" setup>
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
    const {code, error: showError, msgError}
        = useNamespacedState<State>('users', ['code', 'error', 'msgError'])

    async function handleClick(): Promise<void> {
        await fetchUsers(formData.value)
        await router.push({name: 'home'})
    }
</script>

<template>
    <AppRow>
        <AppCard class="bg-blue col">
            <AppForm id="login" v-model="formData" :fields="fields" @submit="handleClick"/>
        </AppCard>
    </AppRow>
</template>
