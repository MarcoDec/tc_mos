<script setup>
    import AppTablePage from '../AppTablePage'
    import generateOptions from '../../../stores/options'

    defineProps({icon: {required: true, type: String}, title: {required: true, type: String}})

    const options = generateOptions('countries', 'code')
    await options.fetch()
    const fields = [
        {create: true, label: 'Nom', name: 'name', search: true, sort: true, update: true},
        {create: true, label: 'Adresse', name: 'address.address', search: true, sort: true, update: true},
        {create: true, label: 'Complément d\'adresse', name: 'address.address2', search: true, sort: true, update: true},
        {create: true, label: 'Ville', name: 'address.city', search: true, sort: true, update: true},
        {create: true, label: 'Code postal', name: 'address.zipCode', search: true, sort: false, update: true},
        {create: true, label: 'Pays', name: 'address.country', options, search: true, sort: true, type: 'select', update: true},
        {create: true, label: 'Numéro de téléphone', name: 'address.phoneNumber', search: true, sort: false, update: true},
        {create: true, label: 'E-mail', name: 'address.email', search: true, sort: true, update: true}
    ]
</script>

<template>
    <AppTablePage :fields="fields" :icon="icon" :title="title"/>
</template>
