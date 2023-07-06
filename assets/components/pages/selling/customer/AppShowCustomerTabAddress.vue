<script setup>
    import generateCustomer from '../../../../stores/customers/customer'
    import {useCustomerStore} from '../../../../stores/customers/customers'
    import {useSocietyStore} from '../../../../stores/societies/societies'

    const props = defineProps({
        optionsCountries: {required: true, type: Object}
    })
    const fetchCustomerStore = useCustomerStore()
    const fetchSocietyStore = useSocietyStore()
    const addressFields = [
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
                    props.optionsCountries.value.find(option => option.type === value)?.text
                    ?? null,
                options: props.optionsCountries.value
            },
            type: 'select'
        },
        {label: 'Fax', name: 'getPhone', type: 'text'}
    ]
    async function updateAdresse(value) {
        const form = document.getElementById('addAdresses')
        const formData = new FormData(form)
        const dataSociety = {
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
        const item = generateCustomer(value)
        await item.updateMain(dataSociety)
        await fetchSocietyStore.fetch()
        await fetchCustomerStore.fetchOne(idCustomer)
    }
</script>

<template>
    <AppCardShow
        id="addAdresses"
        :fields="addressFields"
        :component-attribute="fetchCustomerStore.customer"
        @update="updateAdresse(fetchCustomerStore.customer)"/>
</template>
