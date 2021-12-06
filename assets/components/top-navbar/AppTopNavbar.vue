<script lang="ts" setup>
    import type {Actions, State} from '../../store/security'
    import {useNamespacedActions, useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'
    import {ActionTypes} from '../../store/security'
    import {useRouter} from 'vue-router'
    import Dropdown from '../bootstrap-5/navbar/Dropdown.vue'

    const hasUser = useNamespacedGetters('users', ['hasUser']).hasUser
    const logout = useNamespacedActions<Actions>('users', [ActionTypes.LOGOUT_USERS])[ActionTypes.LOGOUT_USERS]
    const name = useNamespacedState<State>('users', ['username']).username
    const router = useRouter()

    async function onLogout(): Promise<void> {
        await logout()
        await router.push({name: 'login'})
    }
</script>

<template>
    <AppNavbar id="nav">
        <AppNavbarBrand to="home">
            T-Concept
        </AppNavbarBrand>
        <div v-if="hasUser">
        <Dropdown title="Achats" />
        
       
        <div  class="text-white" id="logout">
            <Fa icon="user-circle"/>
            {{ name }}
            <AppBtn variant="danger" @click="onLogout">
                <Fa icon="sign-out-alt"/>
            </AppBtn>
        </div>
        </div>
    </AppNavbar>
</template>
<style scoped>
#nav {
  display: flex;
  align-items: center;
  justify-content: center;
}
#nav .menu-item {
  color: #FFF;
  padding: 10px 20px;
  position: relative;
  text-align: center;
  border-bottom: 3px solid transparent;
  display: flex;
  transition: 0.4s;
  margin-right: 1660px;
  margin-top: 4px;
}
#nav .menu-item.active,
#nav .menu-item:hover {
  background-color: #444;
  border-bottom-color: #FF5858;
}
#nav .menu-item a {
  color: inherit;
  text-decoration: none;
}
#logout{
  float: right;
  margin-top: -35px;
}
</style>