<script setup>
    import AppTablePage from '../../AppTablePage'
    import generateOptions from '../../../../stores/options'

    defineProps({brands: {type: Boolean}, icon: {required: true, type: String}, title: {required: true, type: String}})

    const options = generateOptions('employee-current-places', 'value')
    await options.fetch()
    const fields = [
        {label: 'Nom', name: 'name'},
        {label: 'Vers le statut', name: 'toStatus', options, type: 'select'}
    ]
</script>

<template>
    <AppTablePage :brands="brands" :fields="fields" :icon="icon" :title="title"/>
</template>
