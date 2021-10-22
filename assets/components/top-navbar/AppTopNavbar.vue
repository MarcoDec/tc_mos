<script lang="ts" setup>
    import {computed} from 'vue'
    import {useManager} from '../../store/repository/RepositoryManager'

    const repo = useManager().users
    const user = computed(() => repo.current)
    const hasUser = computed(() => user.value !== null)
</script>

<template>
    <AppNavbar>
        <AppNavbarBrand href="/" title="T-Concept"/>
        <AppNavbarCollapse>
            <AppTopNavbarUser v-if="hasUser" :user="user"/>
        </AppNavbarCollapse>
    </AppNavbar>
</template>
