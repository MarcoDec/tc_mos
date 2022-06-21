<script setup>
    import AppTablePage from '../../AppTablePage'
    import generateOptions from '../../../../stores/options'
    import {onUnmounted} from 'vue'

    defineProps({icon: {required: true, type: String}, title: {required: true, type: String}})

    const options = generateOptions('units')
    await options.fetch()
    const fields = [
        {label: 'Nom', name: 'name', search: true, sort: true, update: true},
        {label: 'Description', name: 'description', search: true, sort: true, update: true},
        {label: 'Unité', name: 'unit', options, search: true, sort: true, sortName: 'unit.name', type: 'select', update: true},
        {label: 'Familles', name: 'familiesName', search: false, sort: false, update: false}
    ]

    onUnmounted(() => options.dispose())
</script>

<template>
    <AppTablePage :fields="fields" :icon="icon" :title="title"/>
</template>
