<script setup>
    import {onMounted, onUnmounted, ref} from 'vue'
    import AppShowGui from './AppShowGui.vue'
    import {gui} from '../../store/gui'
    import {useActions} from 'vuex-composition-helpers'

    const {registerModule, unregisterModule} = useActions(['registerModule', 'unregisterModule'])
    const wrapper = ref(false)

    onMounted(async () => {
        await registerModule({module: gui, path: 'gui'})
        wrapper.value = true
    })

    onUnmounted(async () => {
        await unregisterModule('gui')
    })
</script>

<template>
    <AppShowGui v-if="wrapper"/>
</template>
