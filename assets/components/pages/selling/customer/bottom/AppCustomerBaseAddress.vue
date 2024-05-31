<script setup>
    import generateCustomer from '../../../../../stores/selling/customers/customer'
    import {computed, ref} from 'vue'
    import {useCustomerStore} from '../../../../../stores/selling/customers/customers'

    const props = defineProps({
        optionsCountries: {required: true, type: Array}
    })
    const addressLocalData = ref({})
    const fetchCustomersStore = useCustomerStore()
    const addressFields = computed(() => [
        {
            label: '',
            name: 'wrap1',
            mode: 'wrap',
            wrapWidth: '40%',
            wrapMinWidth: '300px',
            fontSize: '0.7rem',
            children: [
                {label: 'Email', name: 'getEmail', type: 'text'},
                {label: 'Fax', name: 'getPhone', type: 'text'}
            ]
        },
        {
            label: '',
            name: 'wrap3',
            mode: 'wrap',
            wrapWidth: '90%',
            wrapMinWidth: '300px',
            fontSize: '0.7rem',
            children: [
                {label: 'Adresse', name: 'getAddress', type: 'text'},
                {label: 'Complément d\'adresse', name: 'getAddress2', type: 'text'}
            ]
        },
        {
            label: '',
            name: 'wrap2',
            mode: 'wrap',
            wrapWidth: '30%',
            wrapMinWidth: '250px',
            fontSize: '0.7rem',
            children: [
                {label: 'Code postal', name: 'getPostal', type: 'text'},
                {label: 'Ville', name: 'getCity', type: 'text'},
                {
                    label: 'Pays',
                    name: 'getCountry',
                    options: {
                        label: value =>
                            props.optionsCountries.find(option => option.type === value)?.text
                            ?? null,
                        options: props.optionsCountries
                    },
                    type: 'select'
                }
            ]
        }
    ])

    async function updateAddress() {
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

        const item = generateCustomer(fetchCustomersStore.customer)
        await item.updateMain(fetchCustomersStore.customer.id, data)
    }
    async function updateAdressLocalDataFromApi() {
        await fetchCustomersStore.fetchOne(fetchCustomersStore.customer.id)
        addressLocalData.value = {
            wrap1: {
                getEmail: fetchCustomersStore.customer.email,
                getPhone: fetchCustomersStore.customer.phoneNumber
            },
            wrap3: {
                getAddress: fetchCustomersStore.customer.address.address,
                getAddress2: fetchCustomersStore.customer.address.address2
            },
            wrap2: {
                getPostal: fetchCustomersStore.customer.address.zipCode,
                getCity: fetchCustomersStore.customer.address.city,
                getCountry: fetchCustomersStore.customer.address.country
            }
        }
    }
    await updateAdressLocalDataFromApi()
</script>

<template>
    <AppCardShow
        id="mainAddress"
        :fields="addressFields"
        :component-attribute="addressLocalData"
        @update="updateAddress(fetchCustomersStore.customer)"/>
</template>
