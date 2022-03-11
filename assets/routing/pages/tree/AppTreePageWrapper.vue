<script lang="ts" setup>
    import {defineProps, onMounted, onUnmounted, provide, ref} from 'vue'
    import type {Actions} from '../../../store'
    import AppTreePage from './AppTreePage.vue'
    import type {FormField} from '../../../types/bootstrap-5'
    import {generateItem} from '../../../store/tree/item'
    import {generateTree} from '../../../store/tree'
    import {useActions} from 'vuex-composition-helpers'

    const loaded = ref(false)
    const modulePath: [string, ...string[]] = ['families']
    const props = defineProps<{extraFields?: FormField[], title: string, type: string, url: string}>()
    const {registerModule, unregisterModule} = useActions<Actions>(['registerModule', 'unregisterModule'])

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
            module: generateItem(
                firstItem,
                moduleName,
                {
                    '@context': '',
                    '@id': '0',
                    '@type': '',
                    code: 'Familles',
                    id: 0,
                    name: props.type
                },
                props.url,
                {opened: true, selected: false}
            ),
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
