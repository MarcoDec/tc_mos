<script setup>
    import {onMounted, onUnmounted, provide, ref} from 'vue'
    import AppTreePage from './AppTreePage.vue'
    import {generateItem} from '../../../store/tree/item'
    import {generateTree} from '../../../store/tree'
    import {useActions} from 'vuex-composition-helpers'

    const loaded = ref(false)
    const modulePath = ['families']
    const props = defineProps({
        extraFields: {default: () => [], type: Array},
        title: {required: true, type: String},
        type: {required: true, type: String},
        url: {required: true, type: String}
    })
    const {registerModule, unregisterModule} = useActions(['registerModule', 'unregisterModule'])

    const moduleName = modulePath.join('/')
    provide('moduleName', moduleName)
    const firstItem = `${moduleName}/0`
    provide('firstItem', firstItem)

    onMounted(async () => {
        await registerModule({
            module: generateTree(modulePath, props.url),
            path: modulePath
        })
        await registerModule({
            module: generateItem(firstItem, moduleName, {
                '@context': '',
                '@id': '0',
                '@type': '',
                code: 'Familles',
                id: 0,
                name: props.type
            }, props.url, {opened: true, selected: false}),
            path: firstItem.split('/')
        })
        loaded.value = true
    })

    onUnmounted(async () => {
        await unregisterModule(modulePath)
    })
</script>

<template>
    <AppTreePage v-if="loaded" :extra-fields="extraFields">
        {{ title }}
    </AppTreePage>
</template>
