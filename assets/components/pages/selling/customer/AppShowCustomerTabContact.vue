<script setup>
    import {computed, ref} from 'vue'
    import AppCollectionTable from '../../../bootstrap-5/app-collection-table/AppCollectionTable.vue'
    import generateCustomerContact from '../../../../stores/customers/customerContact'
    import {useCustomerContactsStore} from '../../../../stores/customers/customerContacts'

    const props = defineProps({
        optionsCountries: {required: true, type: Object}
    })
    const isShow = ref(false)
    const isError2 = ref(false)
    const violations2 = ref([])
    const fecthCustomerContactsStore = useCustomerContactsStore()
    const itemsTable = computed(() =>
        fecthCustomerContactsStore.itemsSocieties.reduce(
            (acc, curr) => acc.concat(curr),
            []
        ))
    const fieldsSupp = [
        {
            label: 'Nom ',
            name: 'name',
            type: 'text'
        },
        {
            label: 'Prenom ',
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
            label: 'Adresse',
            name: 'address',
            type: 'text'
        },
        {
            label: 'Complément d\'adresse ',
            name: 'address2',
            type: 'text'
        },
        {
            label: 'Pays',
            name: 'country',
            options: {
                label: value =>
                    props.optionsCountries.value.find(option => option.type === value)?.text
                    ?? null,
                options: props.optionsCountries.value
            },
            type: 'select'
        },
        {
            label: 'Ville ',
            name: 'city',
            type: 'text'
        },
        {
            label: 'Code Postal ',
            name: 'zipCode',
            type: 'text'
        }
    ]
    async function ajout(inputValues) {
        const data = {
            address: {
                address: inputValues.address ?? '',
                address2: inputValues.address2 ?? '',
                city: inputValues.city ?? '',
                country: inputValues.country ?? '',
                email: inputValues.email ?? '',
                zipCode: inputValues.zipCode ?? ''
            },
            mobile: inputValues.mobile ?? '',
            name: inputValues.name ?? '',
            society: `/api/customers/${customerId}`,
            surname: inputValues.surname ?? ''
        }

        try {
            await fecthCustomerContactsStore.ajout(data, societyId)
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
        await fecthCustomerContactsStore.deleted(id)
    }
    async function updateSuppliers(inputValues) {
        const dataUpdate = {
            address: {
                address: inputValues.address ?? '',
                address2: inputValues.address2 ?? '',
                city: inputValues.city ?? '',
                country: inputValues.country ?? '',
                email: inputValues.email ?? '',
                zipCode: inputValues.zipCode ?? ''
            },
            mobile: inputValues.mobile ?? '',
            name: inputValues.name ?? '',
            society: inputValues.society,
            surname: inputValues.surname ?? ''
        }
        try {
            const item = generateCustomerContact(inputValues)
            await item.update(dataUpdate)
            isError2.value = false
        } catch (error) {
            await fecthCustomerContactsStore.fetchBySociety(societyId)
            itemsTable.value = fecthCustomerContactsStore.itemsSocieties.reduce(
                (acc, curr) => acc.concat(curr),
                []
            )
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
</script>

<template>
    <AppCollectionTable
        v-if="!isShow"
        id="addContacts"
        :fields="fieldsSupp"
        :items="itemsTable"
        current-page=""
        first-page=""
        form=""
        last-page=""
        next-page=""
        previous-page=""
        user=""
        @ajout="ajout"
        @deleted="deleted"
        @update="updateSuppliers"/>
    <div v-if="isError2" class="alert alert-danger" role="alert">
        <div v-for="violation in violations2" :key="violation">
            <li>{{ violation.propertyPath }} {{ violation.message }}</li>
        </div>
    </div>
</template>
