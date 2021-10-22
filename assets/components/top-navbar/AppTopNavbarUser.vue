<script lang="ts" setup>
    import type User from '../../store/entity/security/User'
    import {defineProps} from 'vue'
    import router from '../../routing/router'
    import {useManager} from '../../store/repository/RepositoryManager'

    defineProps<{user: User}>()
    const manager = useManager()

    async function logout(): Promise<void> {
        await manager.users.disconnect()
        router.push({name: 'login'})
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
