<script setup>
    import {computed, ref} from 'vue'
    import api from '../../../../../api'
    import generateCustomer from '../../../../../stores/selling/customers/customer'
    import {useCustomerStore} from '../../../../../stores/selling/customers/customers'
    import {AppCollectionTable} from '../../../../bootstrap-5/app-collection-table'

    const props = defineProps({
        optionsCountries: {required: true, type: Array}
    })
    const fetchCustomersStore = useCustomerStore()
    const customerAddresses = computed(async () => {
        const response = await api('/api/customer-addresses', 'GET')
        return response['hydra:member']
        }
        )
    const addressLocalData = ref({})
    const addressFields = computed(()=> [
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
            wrapMinWidth: '300px',
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
    const typesAddress = [
        'facturation',
        'livraison'
    ]
    const addressTableFields = [
        {label: 'Adresse', name: 'address', type: 'text'},
        {label: 'Complément d\'adresse', name: 'address2', type: 'text'},
        {label: 'Code postal', name: 'zipCode', type: 'text'},
        {label: 'Ville', name: 'city', type: 'text'},
        {label: 'Pays', name: 'country', type: 'text'},,
        {
            label: 'Type',
            name: 'kind',
            options: {
                label: 'Sélectionner un type',
                options: typesAddress.map(item => ({
                    text: item,
                    value: item
                }))
            },
            type: 'select'
        }
    ]
    async function updateAdressLocalDataFromApi() {
        await fetchCustomersStore.fetchOne(fetchCustomersStore.customer.id)
        addressLocalData.value = {
            wrap1: {
                getEmail: fetchCustomersStore.customer.email,
                getPhone: fetchCustomersStore.customer.phoneNumber
            },
            wrap3: {
                getAddress: fetchCustomersStore.customer.address.address,
                getAddress2: fetchCustomersStore.customer.address.address2,
            },
            wrap2: {
                getPostal: fetchCustomersStore.customer.address.zipCode,
                getCity: fetchCustomersStore.customer.address.city,
                getCountry: fetchCustomersStore.customer.address.country
            }
        }
    }
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
    await updateAdressLocalDataFromApi()
</script>

<template>
    <div class="row">
        <div class="col-6">
            <AppCardShow
                id="mainAddress"
                :fields="addressFields"
                :component-attribute="addressLocalData"
                @update="updateAddress(fetchCustomersStore.customer)"/>
        </div>
        <div class="col-6">
            <AppCollectionTable
                id="addAddress"
                :allowed-actions="{add: true, cancel: true, search: false}"
                :fields="addressTableFields"
                :items="customerAddresses"
            />
        </div>
    </div>

</template>
