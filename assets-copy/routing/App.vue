<script lang="ts" setup>
    import {onMounted, ref} from 'vue'
    import {useNamespacedActions, useState} from 'vuex-composition-helpers'
    import {ActionTypes} from '../store/security'
    import type {Actions} from '../store/security'
    import type {RootState} from '../store/index'
    import router from './router'

    const connected = ref(false)

    const spinner = useState<RootState>(['spinner']).spinner
    const connect = useNamespacedActions<Actions>('users', [ActionTypes.CONNECT])[ActionTypes.CONNECT]
    onMounted(async () => {
        try {
            await connect()
        } catch (err: unknown) {
            await router.push({name: 'login'})
        }
        connected.value = true
    })
</script>

<template>
    <AppTopNavbar/>
    <AppContainer>
        <RouterView v-if="connected"/>
    </AppContainer>
    <div v-if="spinner" class="overlay">
        <div class="spinner spinner-border spinner-border-sm"/>
    </div>
</template>

<style scoped>
.overlay {
  background-color: #EFEFEF;
  position: fixed;
  width: 100%;
  height: 100%;
  z-index: 1000;
  top: 0px;
  left: 0px;
  opacity: .5;
  filter: alpha(opacity=50);
}

.spinner {
  display: block;
  position: fixed;
  z-index: 1031;
  top: 50%;
  right: 50%;
}
</style>
