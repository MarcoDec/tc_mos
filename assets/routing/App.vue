<script setup>
    import {computed, onMounted} from 'vue'
    import {useRepo, useRouter} from '../composition'
    import AppTopNavbar from '../components/AppTopNavbar.vue'
    import {FiniteStateMachineRepository} from '../store/modules'

    const {route} = useRouter()
    const safeId = computed(() => route.name)
    const stateMachine = useRepo(FiniteStateMachineRepository)

    onMounted(() => {
        stateMachine.create('login')
    })
</script>

<template>
    <AppTopNavbar/>
    <AppContainer>
        <RouterView :key="safeId"/>
    </AppContainer>
</template>
