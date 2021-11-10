<script lang="ts" setup>

import AppNavbar from "../bootstrap-5/navbar/AppNavbar.vue";
import AppNavbarBrand from "../bootstrap-5/navbar/AppNavbarBrand.vue";
import {useNamespacedActions, useNamespacedGetters, useNamespacedState} from "vuex-composition-helpers";
import {Actions, ActionTypes} from "../../store/security/actions";
import {State} from "../../store/security";
import {useRouter} from "vue-router";

const router = useRouter()
const logoutUser = useNamespacedActions<Actions>('users', [ActionTypes.LOGOUT_USERS])[ActionTypes.LOGOUT_USERS]
const nameUser = useNamespacedState<State>('users', ['username']).username
const hasUser = useNamespacedGetters('users',['hasUser']).hasUser

function logout() {
  logoutUser()
  router.push('login')
}

</script>

<template>
  <AppNavbar>
    <AppNavbarBrand>T-Concept</AppNavbarBrand>
    <template v-if="hasUser">
      <div>
        <font-awesome-icon class="avatar" icon="user-circle"/>
        <AppNavbarBrand>{{ nameUser }}</AppNavbarBrand>
        <button class="icon" @click="logout"><font-awesome-icon icon="sign-out-alt"/></button></div>

    </template>
  </AppNavbar>
</template>

<style lang="scss" scoped>
.icon {
  background-color: #dc3545;
  border-color: #dc3545;
}
.avatar {
  background-color: grey;
  width: 46px;
  height: 38px;
  margin-bottom: -10px;
}


</style>

/*import {h, resolveComponent} from 'vue'
import type {VNode} from 'vue'

export default function AppTopNavbar(): VNode {
return h(
resolveComponent('AppNavbar'),
() => h(resolveComponent('AppNavbarBrand'), {href: '/'}, () => 'T-Concept')
)
}*/