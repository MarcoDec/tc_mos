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
    const login = useNamespacedActions<Actions>('security', ['login']).login

    async function handleClick(): Promise<void> {
        await login(formData.value)
        await router.push({name: 'home'})
    }
</script>

<template>
    <AppRow>
        <AppCard class="bg-blue col" title="Connexion">
            <AppForm id="login" v-model="formData" :fields="fields" @submit="handleClick">
                <AppBtn type="submit">
                    Connexion
                </AppBtn>
            </AppForm>
        </AppCard>
    </AppRow>
</template>
