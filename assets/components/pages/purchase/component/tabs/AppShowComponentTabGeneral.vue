<script setup>
    import useOptions from '../../../../../stores/option/options'
    import {useComponentListStore} from '../../../../../stores/purchase/component/components'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idComponent = route.params.id_component
    const useFetchComponentStore = useComponentListStore()
    const componentFamilyOptions = useOptions('component-families')
    await componentFamilyOptions.fetchOp()
    //await useFetchComponentStore.fetchOne(idComponent)
    const Géneralitésfields = [
        {label: 'Famille', name: 'family', type: 'select', options: {
            label: value => componentFamilyOptions.options.find(option => option['@id'] === value)?.text ?? null,
            options: componentFamilyOptions.options
        }},
        {label: 'Indice', name: 'index', type: 'number'},
        {label: 'Désignation', name: 'name', type: 'text'},
        {label: 'Notes', name: 'notes', type: 'textarea'}
    ]
    async function updateGeneral() {
        const form = document.getElementById('addGeneralites')
        const formData = new FormData(form)
        const data = {
            index: formData.get('index'),
            name: formData.get('name'),
            notes: formData.get('notes'),
            family: formData.get('family')
        }
        await useFetchComponentStore.updateAdmin(data, idComponent) //pour index et name
        await useFetchComponentStore.updateMain(data, idComponent) //pour notes
        await useFetchComponentStore.fetchOne(idComponent)
    }
</script>

<template>
    <AppCardShow
        id="addGeneralites"
        :fields="Géneralitésfields"
        :component-attribute="useFetchComponentStore.component"
        @update="updateGeneral"/>
</template>

