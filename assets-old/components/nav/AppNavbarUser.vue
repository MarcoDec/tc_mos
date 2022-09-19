<script setup>
    import {createMachine} from 'xstate'
    import {useMachine} from '@xstate/vue'
    import {useRouter} from 'vue-router'
    import useUserStore from '../../stores/hr/employee/user'

    const router = useRouter()
    const {send, state} = useMachine(createMachine({
        id: 'logout',
        initial: 'btn',
        states: {
            btn: {on: {logout: {target: 'loading'}}},
            loading: {type: 'final'}
        }
    }))
    const user = useUserStore()

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
        <AppOverlay id="logout" :spinner="state.matches('loading')" class="navbar-text" tag="span">
            <AppBtn
                :disabled="state.matches('loading')"
                icon="sign-out-alt"
                title="Déconnexion"
                variant="danger"
                @click="logout"/>
        </AppOverlay>
    </div>
</template>
