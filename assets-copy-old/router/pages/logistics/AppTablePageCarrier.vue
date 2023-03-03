<script setup>
    import AppTablePage from '../AppTablePage'
    import generateOptions from '../../../stores/options'

    defineProps({icon: {required: true, type: String}, title: {required: true, type: String}})

    const options = generateOptions('countries', 'code')
    await options.fetch()
    const fields = [
        {label: 'Nom', name: 'name', sort: true, update: true},
        {label: 'Adresse', name: 'address.address', sort: true, update: true},
        {label: 'Complément d\'adresse', name: 'address.address2', sort: true, update: true},
        {label: 'Ville', name: 'address.city', sort: true, update: true},
        {label: 'Code postal', name: 'address.zipCode', sort: false, update: true},
        {label: 'Pays', name: 'address.country', options, sort: true, type: 'select', update: true},
        {label: 'Numéro de téléphone', name: 'address.phoneNumber', sort: false, update: true},
        {label: 'E-mail', name: 'address.email', sort: true, update: true}
    ]
</script>

<template>
    <AppTablePage :fields="fields" :icon="icon" :title="title"/>
</template>
