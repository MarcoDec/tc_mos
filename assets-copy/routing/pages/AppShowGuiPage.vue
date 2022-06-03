<script setup>
    import {computed, onMounted, onUnmounted, ref} from 'vue'
    import {useRepo, useRouter} from '../../composition'
    import AppShowGui from '../../components/gui/AppShowGui.vue'
    import {GuiRepository} from '../../store/modules'

    const {id} = useRouter()
    const repo = useRepo(GuiRepository)
    const gui = computed(() => repo.find(id))
    const mount = ref(false)

    onMounted(() => {
        repo.save({id})
        mount.value = true
    })

    onUnmounted(() => {
        repo.destroy(id)
    })
</script>

<template>
    <AppShowGui v-if="mount" :gui="gui"/>
</template>
