<script setup>
    import AppTablePage from '../AppTablePage'
    import generateOptions from '../../../stores/options'

    defineProps({icon: {required: true, type: String}, title: {required: true, type: String}})

    const options = generateOptions('countries', 'code')
    await options.fetch()
    const fields = [
        {label: 'Nom', name: 'name'},
        {label: 'Prénom', name: 'surname'},
        {label: 'Adresse', name: 'address.address'},
        {label: 'Complément d\'adresse', name: 'address.address2'},
        {label: 'Ville', name: 'address.city'},
        {label: 'Code postal', name: 'address.zipCode'},
        {label: 'Pays', name: 'address.country', options, type: 'select'},
        {label: 'Numéro de téléphone', name: 'address.phoneNumber'},
        {label: 'E-mail', name: 'address.email'}
    ]
</script>

<template>
    <AppTablePage :fields="fields" :icon="icon" :title="title"/>
</template>
