<script setup>
    import {computed, onMounted, onUnmounted} from 'vue'
    import useOptions from '../../../../../../stores/option/options'
    import {useComponentListStore} from '../../../../../../stores/purchase/component/components'
    import {useRoute} from 'vue-router'
    import AppSuspense from '../../../../../AppSuspense.vue'

    const emits = defineEmits(['updated'])
    const route = useRoute()
    const idComponent = route.params.id_component
    const useFetchComponentStore = useComponentListStore()
    const componentFamilyOptions = useOptions('component-families')
    //await useFetchComponentStore.fetchOne(idComponent)
    const generalFields = computed(() => [
        {label: 'Famille', name: 'family', type: 'select', options: {
            label: value => componentFamilyOptions.options.find(option => option['@id'] === value)?.text ?? null,
            options: componentFamilyOptions.options
        }},
        {label: 'Indice', name: 'index', type: 'text'},
        {label: 'Désignation', name: 'name', type: 'text'},
        {label: 'Notes', name: 'notes', type: 'textarea'}
    ])
    async function updateGeneral() {
        const form = document.getElementById('addGeneralites')
        const formData = new FormData(form)
        const dataAdmin = {
            index: formData.get('index'),
            name: formData.get('name'),
            family: formData.get('family')
        }
        const dataMain = {
            notes: formData.get('notes')
        }
        const promises = []
        promises.push(useFetchComponentStore.updateAdmin(dataAdmin, idComponent))
        promises.push(useFetchComponentStore.updateMain(dataMain, idComponent))
        Promise.all(promises).then(() => {
            console.log('updateGeneral')
            useFetchComponentStore.fetchOne(idComponent)
            emits('updated')
        })
    }
    onMounted(async () => {
        await componentFamilyOptions.fetchOp()
        console.log('onMounted')
        await useFetchComponentStore.fetchOne(idComponent)
        console.log('onMounted afterFetch')
    })
    onUnmounted(() => {
        useFetchComponentStore.reset()
        componentFamilyOptions.resetItems()
    })
</script>

<template>
    <AppSuspense>
        <AppCardShow
            v-if="componentFamilyOptions.items.length > 0"
            id="addGeneralites"
            class="font-small mx-2 mt-1 width70"
            :fields="generalFields"
            :component-attribute="useFetchComponentStore.component"
            title="Informations générales"
            @update="updateGeneral"/>
    </AppSuspense>
</template>

