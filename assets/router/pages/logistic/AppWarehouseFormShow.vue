<script setup>
    import {computed, ref} from 'vue'
    //import {useComponentListStore} from '../../../stores/component/components'
    //import {useComponentShowStore} from '../../../stores/component/componentAttributesList'
    // import useOptions from '../../../stores/option/options'

    //const fecthOptions = useOptions('units')
    //await fecthOptions.fetchOp()
    //const useFetchComponentStore = useComponentListStore()
    /*const useComponentStore = useComponentShowStore()
    const fetchComponentAttachment = useComponentAttachmentStore()
    await useComponentStore.fetch()
    await fetchComponentAttachment.fetch()
    await useFetchComponentStore.fetch()*/
    const guiRatio = ref(0.5)
    const guiRatioPercent = computed(() => `${guiRatio.value * 100}%`)
    const Generalitesfields = [
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Index', name: 'index', type: 'number'},
        {label: 'Note', name: 'notes', type: 'textarea'}
    ]
        // function updateGeneral() {
        //     const componentId = Number()
        //     const form = document.getElementById('addGeneralites')
        //     const formData = new FormData(form)
        //     const data = {
        //         index: formData.get('index'),
        //         name: formData.get('name'),
        //      notes: formData.get('notes')
        //     }
        //     useFetchComponentStore.updateAdmin(data, componentId)
        //     useFetchComponentStore.updateMain(data, componentId)
        //     useFetchComponentStore.fetch()
        // }
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab
            id="gui-start-main"
            active
            title="Généralité"
            icon="pencil"
            tabs="gui-start">
            <AppCardShow
                id="addGeneralites"
                :fields="Generalitesfields"/>
                <!--mettre dans AppCardShow @update="updateGeneral()"-->
        </AppTab>
    </AppTabs>
</template>

<style scoped>
    .gui {
        --gui-ratio: v-bind(guiRatioPercent);
    }
</style>
