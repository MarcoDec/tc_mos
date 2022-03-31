<script setup>
    import {ref} from 'vue'
    import router from '../../router'
    import {useNamespacedActions} from 'vuex-composition-helpers'

    const fields = [
        {label: 'Identifiant', name: 'username'},
        {label: 'Mot de passe', name: 'password', type: 'password'}
    ]
    const formData = ref({password: null, username: null})
    const login = useNamespacedActions('security', ['login']).login

    async function handleClick() {
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
