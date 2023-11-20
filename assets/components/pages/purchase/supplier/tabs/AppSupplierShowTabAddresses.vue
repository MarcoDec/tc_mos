<script setup>
    import generateSupplier from '../../../../../stores/purchase/supplier/supplier'
    import {useSuppliersStore} from '../../../../../stores/purchase/supplier/suppliers'
    import {toRefs} from 'vue'

    const {optionsCountries} = toRefs(defineProps({
        optionsCountries: {required: true, type: Array}
    }))
    const fetchSuppliersStore = useSuppliersStore()
    const Adressefields = [
        {label: 'Email', name: 'getEmail', type: 'text'},
        {label: 'Adresse', name: 'getAddress', type: 'text'},
        {label: 'Complément d\'adresse', name: 'getAddress2', type: 'text'},
        {label: 'Code postal', name: 'getPostal', type: 'text'},
        {label: 'Ville', name: 'getCity', type: 'text'},
        {
            label: 'Pays',
            name: 'getCountry',
            options: {
                label: value =>
                    optionsCountries.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsCountries.value
            },
            type: 'select'
        },
        {label: 'Fax', name: 'getPhone', type: 'text'}
    ]

    async function updateAddress(value) {
        const form = document.getElementById('addAdresses')
        const formData = new FormData(form)

        const data = {
            address: {
                address: formData.get('getAddress'),
                address2: formData.get('getAddress2'),
                city: formData.get('getCity'),
                country: formData.get('getCountry'),
                email: formData.get('getEmail'),
                phoneNumber: formData.get('getPhone'),
                zipCode: formData.get('getPostal')
            }
        }

        const item = generateSupplier(value)
        await item.updateMain(data)
    }
</script>

<template>
    <AppCardShow
        id="addAdresses"
        :fields="Adressefields"
        :component-attribute="fetchSuppliersStore.supplier"
        @update="updateAddress(fetchSuppliersStore.supplier)"/>
</template>
