<script lang="ts" setup>
    import {defineProps, onMounted, onUnmounted, ref} from 'vue'
    import type {Actions} from '../../../store'
    import AppCollectionTablePage from './AppCollectionTablePage.vue'
    import type {TableField} from '../../../types/app-collection-table'
    import {generateColors} from '../../../store/colors'
    import {useActions} from 'vuex-composition-helpers'

    const loaded = ref(false)
    const props = defineProps<{fields: TableField[], icon: string, modulePath: string, title: string}>()
    const {registerModule, unregisterModule} = useActions<Actions>(['registerModule', 'unregisterModule'])

    onMounted(async () => {
        let generated = null
        switch (props.modulePath) {
            case 'colors':
                generated = generateColors()
        }
        if (generated !== null)
            await registerModule({module: generated, path: props.modulePath})
        loaded.value = true
    })

    onUnmounted(async () => unregisterModule(props.modulePath))
</script>

<template>
    <AppCollectionTablePage v-if="loaded" :fields="fields" :icon="icon" :module-path="modulePath" :title="title"/>
</template>
