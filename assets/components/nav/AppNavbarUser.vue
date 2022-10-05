<script setup>
    import {useMachine} from '../../composable/xstate'
    import {useRouter} from 'vue-router'
    import useUser from '../../stores/security'

    const router = useRouter()
    const {send, state} = useMachine({
        id: 'logout',
        initial: 'btn',
        states: {
            btn: {on: {logout: {target: 'loading'}}},
            loading: {type: 'final'}
        }
    })
    const user = useUser()

    async function logout() {
        send('logout')
        await user.logout()
        await router.push({name: 'login'})
    }
</script>

<template>
    <div class="text-white">
        <span class="me-1 navbar-text">
            <Fa icon="user-circle"/>
        </span>
        <span class="me-1 navbar-text">
            {{ user.name }}
        </span>
        <AppOverlay :spinner="state.matches('loading')" class="navbar-text" tag="span">
            <AppBtn
                :disabled="state.matches('loading')"
                icon="sign-out-alt"
                label="Déconnexion"
                variant="danger"
                @click="logout"/>
        </AppOverlay>
    </div>
</template>
