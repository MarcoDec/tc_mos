<script lang="ts" setup>
    import {onMounted, onUnmounted, ref} from 'vue'
    import AppShowGui from './AppShowGui.vue'
    import {gui} from '../../store/gui'
    import store from '../../store'

    const wrapper = ref(false)
    onMounted(() => {
        store.registerModule('gui', gui)
        wrapper.value = true
    })
    onUnmounted(() => {
        store.unregisterModule('gui')
    })
</script>

<template>
    <AppShowGui v-if="wrapper"/>
</template>
