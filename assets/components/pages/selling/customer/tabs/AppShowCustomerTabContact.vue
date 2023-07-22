<script setup>
    import {computed, ref} from 'vue'
    import generateCustomerContact from '../../../../../stores/customers/customerContact'
    import {useCustomerContactsStore} from '../../../../../stores/customers/customerContacts'
    import {useCustomerStore} from '../../../../../stores/customers/customers'

    const emit = defineEmits(['error'])
    const fetchCustomersStore = useCustomerStore()
    const fetchCustomerContactsStore = useCustomerContactsStore()
    const customerId = Number(fetchCustomersStore.customer.id)
    await fetchCustomerContactsStore.fetchBySociety(customerId)
    const isShow = ref(false)
    const itemsTable = computed(() =>
        fetchCustomerContactsStore.itemsSocieties.reduce(
            (acc, curr) => acc.concat(curr),
            []
        ))
    //Création des variables locales
    const isError2 = ref(false)
    const violations2 = ref([])
    const typesContact = [
        'comptabilité',
        'chiffrage',
        'direction',
        'ingénierie',
        'fabrication',
        'achat',
        'qualité',
        'commercial',
        'approvisionnement'
    ]
    const fieldsSupp = [
        {
            label: 'Nom ',
            name: 'name',
            type: 'text'
        },
        {
            label: 'Prénom ',
            name: 'surname',
            type: 'text'
        },
        {
            label: 'Mobile ',
            name: 'mobile',
            type: 'text'
        },
        {
            label: 'Email ',
            name: 'email',
            type: 'text'
        },
        {
            label: 'Type',
            name: 'kind',
            options: {
                label: 'Sélectionner un type',
                options: typesContact.map(item => ({
                    text: item,
                    value: item
                }))
            },
            type: 'select'
        }
    ]
    async function ajout(inputValues) {
        const data = {
            address: {
                //address: inputValues.address ?? '',
                // address2: inputValues.address2 ?? '',
                // city: inputValues.city ?? '',
                // country: inputValues.country ?? '',
                email: inputValues.email ?? ''
                // phoneNumber: inputValues.getPhone ?? "",
                //zipCode: inputValues.zipCode ?? ''
            },
            // default: true,
            kind: inputValues.kind ?? '',
            mobile: inputValues.mobile ?? '',
            name: inputValues.name ?? '',
            society: `/api/customers/${customerId}`,
            surname: inputValues.surname ?? ''
        }
        try {
            await fetchCustomerContactsStore.ajout(data, customerId)

            isError2.value = false
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
        await fetchCustomerContactsStore.deleted(id)
    }
    async function updateCustomer(inputValues) {
        const dataUpdate = {
            address: {
                // address: inputValues.address ?? '',
                // address2: inputValues.address2 ?? '',
                // city: inputValues.city ?? '',
                // country: inputValues.country ?? '',
                email: inputValues.email ?? ''
                // phoneNumber: inputValues.getPhone ?? "",
                //zipCode: inputValues.zipCode ?? ''
            },
            // default: true,
            // kind: "comptabilité",
            kind: inputValues.kind ?? '',
            mobile: inputValues.mobile ?? '',
            name: inputValues.name ?? '',
            society: `/api/customers/${customerId}`,
            surname: inputValues.surname ?? ''
        }
        try {
            const item = generateCustomerContact(inputValues)
            await item.update(dataUpdate)
            isError2.value = false
        } catch (error) {
            await fetchCustomerContactsStore.fetchBySociety(customerId)
            itemsTable.value = fetchCustomerContactsStore.itemsSocieties.reduce(
                (acc, curr) => acc.concat(curr),
                []
            )
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
        v-if="!isShow"
        id="addContacts"
        :allowed-actions="{add: true, cancel: true, search: false}"
        :fields="fieldsSupp"
        :items="itemsTable"
        @ajout="ajout"
        @deleted="deleted"
        @update="updateCustomer"/>
</template>
