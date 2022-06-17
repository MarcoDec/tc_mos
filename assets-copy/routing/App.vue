<script lang="ts" setup>
    import {onMounted, ref} from 'vue'
    import {ActionTypes} from '../store/security'
    import type {Actions} from '../store/security'
    import {useNamespacedActions} from 'vuex-composition-helpers'

    const connected = ref(false)

    onMounted(async () => {
        await useNamespacedActions<Actions>('users', [ActionTypes.CONNECT])[ActionTypes.CONNECT]()
        connected.value = true
    })
</script>

<template>
    <AppTopNavbar/>
    <AppContainer>
        <RouterView v-if="connected"/>
    </AppContainer>
</template>
