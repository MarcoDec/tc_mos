<script setup>
    import {useRepo, useRouter} from '../../composition'
    import {EmployeeRepository} from '../../store/modules'
    import {computed} from 'vue'

    const fields = [
        {label: 'Identifiant', name: 'username'},
        {label: 'Mot de passe', name: 'password', type: 'password'}
    ]

    const {id, router} = useRouter()
    const repo = useRepo(EmployeeRepository)
    const form = computed(() => `${id}-form`)

    async function submit(body) {
        await repo.login(id, body)
        router.push({name: 'home'})
    }
</script>

<template>
    <AppRow :id="id">
        <AppCard class="bg-blue col" title="Connexion">
            <AppForm :id="form" :fields="fields" :state-machine="id" @submit="submit">
                <AppBtn type="submit">
                    Connexion
                </AppBtn>
            </AppForm>
        </AppCard>
    </AppRow>
</template>
