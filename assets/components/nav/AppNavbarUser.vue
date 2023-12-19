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
    const TUNISIE_TIMEZONE = {country: 'Tunis', timezone: 'Africa/Tunis'}
    const FRANCE_TIMEZONE = {country: 'France', timezone: 'Europe/Paris'}
    const SUISSE_TIMEZONE = {country: 'Suisse', timezone: 'Europe/Zurich'}
    const MOLDAVIE_TIMEZONE = {country: 'Moldavie', timezone: 'Europe/Bucharest'}

    let timezones = []
    const user = useUser()
    switch (user.company) {
        case '/api/companies/1': //France
            timezones = [FRANCE_TIMEZONE, TUNISIE_TIMEZONE, SUISSE_TIMEZONE, MOLDAVIE_TIMEZONE]
            break
        case '/api/companies/2': //MG2C
            timezones = [SUISSE_TIMEZONE, FRANCE_TIMEZONE, TUNISIE_TIMEZONE, MOLDAVIE_TIMEZONE]
            break
        case '/api/companies/3': //Tunisie Concept
            timezones = [TUNISIE_TIMEZONE, SUISSE_TIMEZONE, FRANCE_TIMEZONE, MOLDAVIE_TIMEZONE]
            break
        default: //WHETEC
            timezones = [MOLDAVIE_TIMEZONE, TUNISIE_TIMEZONE, SUISSE_TIMEZONE, FRANCE_TIMEZONE]
    }

    async function logout() {
        send('logout')
        await user.logout()
        await router.push({name: 'login'})
    }
</script>

<template>
    <div class="text-white">
        <ul class="align-items-center d-flex flex-row ms-auto mt-0 navbar-nav">
            <span class="user">
                <AppNavbarItem id="user" class="m-0 navbar-text p-0" icon="user-circle" :title="user.name">
                    <div v-for="tz in timezones" :key="tz.country" class="timelink">
                        <AppNavbarLinkTime :timezone="tz.timezone" :country="tz.country"/>
                    </div>
                </AppNavbarItem>
            </span>
            <AppOverlay :spinner="state.matches('loading')" class="navbar-text p-0" tag="span">
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
    margin-bottom: 10px;
}
.user{
    margin-right:31px;
}
</style>
