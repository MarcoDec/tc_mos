<script lang="ts" setup>
    import {onMounted, onUnmounted, provide, ref} from 'vue'
    import type {Actions} from '../../../../../store'
    import AppComponentFamilies from './AppComponentFamilies.vue'
    import {generateFamilies} from '../../../../../store/purchase/component/families'
    import {useActions} from 'vuex-composition-helpers'

    const modulePath: [string, ...string[]] = ['families']
    const {registerModule, unregisterModule} = useActions<Actions>(['registerModule', 'unregisterModule'])
    const loaded = ref(false)

    provide('modulePath', `${modulePath.join('/')}/0`)

    onMounted(async () => {
        await registerModule({module: generateFamilies(modulePath), path: modulePath})
        loaded.value = true
    })

    onUnmounted(async () => {
        await unregisterModule(modulePath)
    })
</script>

<template>
    <AppComponentFamilies v-if="loaded"/>
</template>
