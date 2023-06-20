<script setup>
    import {useComponentListStore} from '../../../../stores/component/components'
    import {useRoute} from 'vue-router'

    const route = useRoute()
    const idComponent = route.params.id_component
    const useFetchComponentStore = useComponentListStore()
    //await useFetchComponentStore.fetchOne(idComponent)
    const Géneralitésfields = [
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Index', name: 'index', type: 'number'},
        {label: 'Note', name: 'notes', type: 'textarea'}
    ]
    async function updateGeneral() {
        const form = document.getElementById('addGeneralites')
        const formData = new FormData(form)
        const data = {
            index: formData.get('index'),
            name: formData.get('name'),
            notes: formData.get('notes')
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

