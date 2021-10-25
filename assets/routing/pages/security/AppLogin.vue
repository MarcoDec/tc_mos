<script lang="ts" setup>
    import type {UserState} from '../../../store/entity/security/User'
    import {onUnmounted} from 'vue'
    import {useManager} from '../../../store/repository/RepositoryManager'
    import {useRouter} from 'vue-router'

    const form = 'login'
    const id = 'app-login'
    const manager = useManager()
    const router = useRouter()

    manager.forms.persist(id, form, [
        {label: 'Identifiant', name: 'username'},
        {label: 'Mot de passe', name: 'password', type: 'password'}
    ])

    function connect(user: UserState): void {
        manager.users.connect(user)
        router.push({name: 'home'})
    }

    onUnmounted(async () => {
        await manager.clear(id)
    })
</script>

<template>
    <AppRow>
        <AppCard class="bg-blue col">
            <AppForm :id="form" action="/api/login" @success="connect"/>
        </AppCard>
    </AppRow>
</template>
