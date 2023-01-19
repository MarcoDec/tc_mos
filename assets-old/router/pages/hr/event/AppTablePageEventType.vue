<script setup>
    import AppTablePage from '../../AppTablePage'
    import generateOptions from '../../../../stores/options'

    defineProps({brands: {type: Boolean}, icon: {required: true, type: String}, title: {required: true, type: String}})

    const options = generateOptions('employee-states', 'value')
    await options.fetch()
    const fields = [
        {create: true, label: 'Nom', name: 'name', search: true, sort: true, update: true},
        {create: true, label: 'Vers le statut', name: 'toStatus', options, search: true, sort: false, type: 'select', update: true}
    ]
</script>

<template>
    <AppTablePage :brands="brands" :fields="fields" :icon="icon" :title="title"/>
</template>
