<script lang="ts" setup>
    import {ActionTypes, MutationTypes} from '../../store/security'
    import type {Actions, Mutations, State} from '../../store/security'
    import {
        useNamespacedActions,
        useNamespacedGetters,
        useNamespacedMutations,
        useNamespacedState
    } from 'vuex-composition-helpers'
    import AppNotifications from '../bootstrap-5/notification/AppNotifications.vue'
    import {useRouter} from 'vue-router'

    const hasUser = useNamespacedGetters('users', ['hasUser']).hasUser
    const logout = useNamespacedActions<Actions>('users', [ActionTypes.LOGOUT_USERS])[ActionTypes.LOGOUT_USERS]
    const name = useNamespacedState<State>('users', ['username']).username
    const error = useNamespacedMutations<Mutations>('users', [MutationTypes.LOGOUT])[MutationTypes.LOGOUT]
    const router = useRouter()

    async function onLogout(): Promise<void> {
        await logout()
        await router.push({name: 'login'})
        error()
    }
</script>

<template>
    <AppNavbar>
        <AppNavbarBrand to="home">
            T-Concept
        </AppNavbarBrand>
        <div v-if="hasUser" class="text-white">
            <AppNotifications/>
            <div>
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
.text-white {
  display: flex;
  flex-direction: row;
  align-items: center;
}

.dropbtn {
  background-color: #4CAF50;
  color: white;
  padding: 16px;
  font-size: 16px;
  border: none;
  cursor: pointer;
}

.dropdown {
  position: relative;
  display: flex;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f9f9f9;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
}

.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

.dropdown-content a:hover {
  background-color: #f1f1f1
}

.dropdown:hover .dropdown-content {
  display: block;
}

.dropdown:hover .dropbtn {
  background-color: #3e8e41;
}

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

#logout {
  float: right;
  margin-top: -35px;
}
</style>
