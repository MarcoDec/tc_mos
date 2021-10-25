<script lang="ts" setup>
    import type User from '../../store/entity/security/User'
    import {defineProps} from 'vue'
    import {useManager} from '../../store/repository/RepositoryManager'
    import {useRouter} from 'vue-router'

    defineProps<{user: User}>()
    const manager = useManager()
    const router = useRouter()

    async function logout(): Promise<void> {
        await manager.users.disconnect()
        console.debug('logout', {
            hasLogin: router.hasRoute('login'),
            router,
            routes: router.getRoutes()
        })
        await router.push({name: 'login'})
        console.debug('logout', 'finished')
    }
</script>

<template>
    <AppNavbarNav class="ms-auto">
        <AppNavbarText>
            {{ user.username }}
        </AppNavbarText>
        <AppBtn class="ms-1" variant="danger" @click="logout">
            <FontAwesomeIcon icon="sign-out-alt"/>
        </AppBtn>
    </AppNavbarNav>
</template>
