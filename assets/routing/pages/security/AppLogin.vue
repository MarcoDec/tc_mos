<script lang="ts" setup>
    import {ActionTypes} from '../../../store/security/actions'
    import type {Actions} from '../../../store/security/actions'
    import {ref} from 'vue'
    import {useNamespacedActions} from 'vuex-composition-helpers'
    import AppForm from '../../../components/bootstrap-5/form/AppForm.vue'
    import type {FormField} from '../../../types/bootstrap-5'
    import router from "../../router";

    const fields: FormField[] = [
        {label: 'Identifiant', name: 'username'},
        {label: 'Mot de passe', name: 'password', type: 'password'}

    ]
    const password = ref<string | null>(null)
    const username = ref<string | null>(null)
    const formData = ref<{ password:string|null , username:string|null }>({ password:null , username:null})
    const fetchUsers = useNamespacedActions<Actions>('users', [ActionTypes.FETCH_USERS])[ActionTypes.FETCH_USERS]

    async function handleClick(): Promise<void> {
        await fetchUsers(formData.value)

        router.push('home')

    }

</script>

<template>
  <AppRow>
        <AppCard class="bg-blue col">
            <AppForm :fields="fields" @submit="handleClick" v-model:values="formData" />
        </AppCard>
    </AppRow>

</template>
