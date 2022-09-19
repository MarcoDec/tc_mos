<script setup>
    import AppTablePage from '../../AppTablePage'
    import generateOptions from '../../../../stores/options'
    import {onUnmounted} from 'vue'

    defineProps({icon: {required: true, type: String}, title: {required: true, type: String}})

    const options = generateOptions('companies')
    await options.fetch()
    const fields = [
        {create: true, label: 'Nom', name: 'name', search: true, sort: true, update: true},
        {create: true, label: 'Compagnie', name: 'company', options, search: true, sort: false, type: 'select', update: true}
    ]

    onUnmounted(() => options.dispose())
</script>

<template>
    <AppTablePage :fields="fields" :icon="icon" :title="title"/>
</template>
