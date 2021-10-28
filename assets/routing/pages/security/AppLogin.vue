<script lang="ts" setup>
    import {onMounted, ref} from 'vue'
    import type FormRepository from '../../../store/repository/bootstrap-5/form/FormRepository'
    import {useRepo} from '../../../store/store'

    const forms = ref<FormRepository | null>(null)
    const form = 'login-form'
    const formRegistered = ref(false)
    const id = 'login'

    onMounted(async () => {
        forms.value = await useRepo({repo: 'forms', vueComponent: id})
        if (forms.value !== null) {
            await forms.value.persist({
                fields: [
                    {label: 'Identifiant', name: 'username'},
                    {label: 'Mot de passe', name: 'password', type: 'password'}
                ],
                id: form,
                vueComponents: [id]
            })
            formRegistered.value = true
        }
    })
</script>

<template>
    <AppRow :id="id">
        <AppCard class="bg-blue col">
            <AppForm v-if="formRegistered" :id="form" action="/api/login" submit-label="Connexion"/>
        </AppCard>
    </AppRow>
</template>
