<script setup>
    import {onMounted, onUnmounted, ref} from 'vue'
    import AppCollectionTablePage from './AppCollectionTablePage.vue'
    import {generateColors} from '../../../store/colors'
    import {useActions} from 'vuex-composition-helpers'

    const loaded = ref(false)
    const props = defineProps({
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        modulePath: {required: true, type: String},
        title: {required: true, type: String}
    })
    const {registerModule, unregisterModule} = useActions(['registerModule', 'unregisterModule'])

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
