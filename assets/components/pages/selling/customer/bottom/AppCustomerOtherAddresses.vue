<script setup>
    import {computed, ref} from 'vue'
    import api from '../../../../../api'
    import {AppCollectionTable} from '../../../../bootstrap-5/app-collection-table'

    const props = defineProps({
        customerId: {required: true, type: Number},
        optionsCountries: {required: true, type: Array}
    })

    const customerAddresses = ref({})
    async function updateTable() {
        await api(`/api/customer-addresses?customer=/api/customers/${props.customerId}`, 'GET').then(response => {
            customerAddresses.value = response['hydra:member']
            //console.log('customerAddresses', customerAddresses.value)
            }
        )
    }
    await updateTable()
    const isError2 = ref(false)
    const violations2 = ref([])

    const typesAddress = [
        {
            value: 'DeliveryAddress',
            text: 'Livraison'
        },
        {
            value: 'BillingAddress',
            text: 'Facturation'
        }
    ]
    const addressTableFields = [
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Adresse', name: 'address.address', type: 'text'},
        {label: 'Complément d\'adresse', name: 'address.address2', type: 'text'},
        {label: 'Code postal', name: 'address.zipCode', type: 'text'},
        {label: 'Ville', name: 'address.city', type: 'text'},
        {label: 'Pays', name: 'address.country', type: 'text'},
        {
            label: 'Type',
            name: '@type',
            options: {
                label: value => typesAddress.find(option => option.value === value)?.text ?? null,
                options: typesAddress
            },
            type: 'select'
        }
    ]

    async function ajout(inputValues) {
        console.log('ajout', inputValues)
        const data = {
            address: {
                address: inputValues['address.address'] ?? '',
                address2: inputValues['address.address2'] ?? '',
                city: inputValues['address.city'] ?? '',
                country: inputValues['address.country'] ?? '',
                zipCode: inputValues['address.zipCode'] ?? ''
            },
            name: inputValues.name ?? '',
            customer: `/api/customers/${props.customerId}`,
        }
        let  baseUrl = ''
        switch (inputValues['@type']) {
            case 'DeliveryAddress':
                baseUrl = '/api/delivery-addresses'
                break
            case 'BillingAddress':
                baseUrl = '/api/billing-addresses'
                break
            default:
                window.alert('Merci de renseigner le type d\'adresse')
                return
        }
        try {
            await api(baseUrl, 'POST', data)
            isError2.value = false
            await updateTable()
        } catch (error) {
            if (Array.isArray(error)) {
                violations2.value = error
                isError2.value = true
            } else {
                const err = {
                    message: error
                }
                violations2.value.push(err)
                isError2.value = true
            }
        }
    }
    async function deleted(id) {
        console.log('delete', id)
        await api(`/api/customer-addresses/${id}`, 'DELETE')
        await updateTable()
    }
    async function updateCustomerAddress(inputValues) {
        console.log('update inputValues', inputValues)
        const dataUpdate = {
            address: {
                address: inputValues['address.address'] ?? '',
                address2: inputValues['address.address2'] ?? '',
                city: inputValues['address.city'] ?? '',
                country: inputValues['address.country'] ?? '',
                zipCode: inputValues['address.zipCode'] ?? ''
            },
            name: inputValues.name ?? ''
        }
        try {
            await api(inputValues['@id'], 'PATCH', dataUpdate)
            isError2.value = false
            await updateTable()
        } catch (error) {
            console.log('error', error)
            if (Array.isArray(error)) {
                violations2.value = error
                isError2.value = true
                emit('error', {violation: violations2.value})
            } else {
                const err = {
                    message: error
                }
                violations2.value.push(err)
                isError2.value = true
            }
        }
    }
</script>

<template>
    <AppCollectionTable
        id="addAddress"
        :allowed-actions="{add: true, cancel: true, search: false}"
        :fields="addressTableFields"
        :items="customerAddresses"
        current-page=""
        first-page=""
        form="addAddress"
        last-page=""
        next-page=""
        previous-page=""
        user=""
        @ajout="ajout"
        @deleted="deleted"
        @update="updateCustomerAddress"/>
</template>

<style scoped>

</style>