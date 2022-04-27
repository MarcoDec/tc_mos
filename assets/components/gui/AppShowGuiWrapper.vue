<script setup>
    import {onMounted, onUnmounted, ref} from 'vue'
    import AppShowGui from './AppShowGui.vue'
    import {gui} from '../../store/gui'
    import {useStore} from 'vuex'

    const store = useStore()
    const wrapper = ref(false)

    onMounted(async () => {
        store.registerModule('gui', gui)
        wrapper.value = true
    })

    onUnmounted(async () => {
        store.unregisterModule('gui')
    })
</script>

<template>
    <AppShowGui v-if="wrapper"/>
</template>
