<script setup>
    import AppTablePage from '../AppTablePage'
    import generateOptions from '../../../stores/options'

    defineProps({icon: {required: true, type: String}, title: {required: true, type: String}})

    const options = generateOptions('countries', 'code')
    await options.fetch()
    const fields = [
        {label: 'Nom', name: 'name', sort: true},
        {label: 'Prénom', name: 'surname', sort: true},
        {label: 'Adresse', name: 'address.address', sort: true},
        {label: 'Complément d\'adresse', name: 'address.address2', sort: true},
        {label: 'Ville', name: 'address.city', sort: true},
        {label: 'Code postal', name: 'address.zipCode', sort: false},
        {label: 'Pays', name: 'address.country', options, sort: true, type: 'select'},
        {label: 'Numéro de téléphone', name: 'address.phoneNumber', sort: false},
        {label: 'E-mail', name: 'address.email', sort: true}
    ]
</script>

<template>
    <AppTablePage :fields="fields" :icon="icon" :title="title"/>
</template>
