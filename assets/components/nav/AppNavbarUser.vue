<script setup>
    import AppNavbarItem from './AppNavbarItem.vue'
    import AppNavbarLinkTime from '../bootstrap-5/navbar/AppNavbarLinkTime.vue'
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

    const country = 'Tunis'

    const timezones = [
        {country: 'Tunis', timezone: 'Africa/Tunis'},
        {country: 'France', timezone: 'Europe/Paris'}
    ]
    const userTimezone = timezones.find(tz => tz.country === country)

    async function logout() {
        send('logout')
        await user.logout()
        await router.push({name: 'login'})
    }
</script>

<template>
    <div class="text-white">
        <ul class="me-auto navbar-nav">
            <span class="user">
                <AppNavbarItem id="user" class="navbar-text" icon="user-circle" :title="user.name">
                    <div class="timelink">
                        <AppNavbarLinkTime :timezone="userTimezone.timezone" :country="userTimezone.country"/>
                    </div>
                    <div v-for="tz in timezones" :key="tz.country" class="timelink">
                        <AppNavbarLinkTime v-if="tz.country !== country" :timezone="tz.timezone" :country="tz.country"/>
                    </div>
                </AppNavbarItem>
            </span>

            <AppOverlay :spinner="state.matches('loading')" class="navbar-text" tag="span">
                <AppBtn
                    :disabled="state.matches('loading')"
                    icon="sign-out-alt"
                    label="Déconnexion"
                    variant="danger"
                    @click="logout"/>
            </AppOverlay>
        </ul>
    </div>
</template>

<style>
.timelink{
    width: 130px;
    height: 50px;
}
.user{
    margin-right:31px;
}
</style>
