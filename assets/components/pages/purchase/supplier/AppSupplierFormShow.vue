<script setup>
    import {computed, ref} from 'vue'
    import AppSupplierShowTabAccounting from './AppSupplierShowTabAccounting.vue'
    import AppSupplierShowTabFichiers from './AppSupplierShowTabFichiers.vue'
    import AppSupplierShowTabGeneral from './AppSupplierShowTabGeneral.vue'
    import AppSupplierShowTabPurchase from './AppSupplierShowTabPurchase.vue'
    import AppSupplierShowTabQuality from './AppSupplierShowTabQuality.vue'
    import generateSupplier from '../../../../stores/supplier/supplier'
    import generateSupplierContact from '../../../../stores/supplier/supplierContact'
    import {useIncotermStore} from '../../../../stores/incoterm/incoterm'
    import useOptions from '../../../../stores/option/options'
    import {useRoute} from 'vue-router'
    import {useSocietyStore} from '../../../../stores/societies/societies'
    import {useSupplierContactsStore} from '../../../../stores/supplier/supplierContacts'
    import {useSuppliersStore} from '../../../../stores/supplier/suppliers'

    const route = useRoute()
    const idSupplier = route.params.id_supplier

    //Création des variables locales
    const isError2 = ref(false)
    const isShow = ref(false)
    const violations2 = ref([])
    //Création des Stores
    const fetchCurrencyOptions = useOptions('currencies')
    const fetchCompanyOptions = useOptions('companies')
    const fetchOptions = useOptions('countries')
    const fetchSuppliersStore = useSuppliersStore()
    const fetchIncotermStore = useIncotermStore()
    const fetchSocietyStore = useSocietyStore()
    const fetchSupplierContactsStore = useSupplierContactsStore()
    //Chargement du Fournisseur courant
    await fetchSuppliersStore.fetchOne(idSupplier)

    //Chargement des informations liées
    await fetchSuppliersStore.fetchVatMessage()
    await fetchIncotermStore.fetch()
    await fetchSocietyStore.fetch()
    await fetchCurrencyOptions.fetchOp()
    await fetchOptions.fetchOp()
    await fetchCompanyOptions.fetchOp()

    const optionsCountries = computed(() =>
        fetchOptions.options.map(op => {
            const text = op.text
            const value = op.id
            const optionList = {text, value}
            return optionList
        }))
    const societyId = Number(fetchSuppliersStore.supplier.society.match(/\d+/))
    const supplierId = Number(fetchSuppliersStore.supplier.id)
    await fetchSocietyStore.fetchById(societyId)
    await fetchSupplierContactsStore.fetchBySociety(societyId)

    const itemsTable = computed(() =>
        fetchSupplierContactsStore.itemsSocieties.reduce(
            (acc, curr) => acc.concat(curr),
            []
        ))

    // const dataSuppliers = computed(() =>
    //     Object.assign(fetchSuppliersStore.suppliers, fetchSocietyStore.item))
    const managed = computed(() => {
        const data = {managedCopper: fetchSocietyStore.society.copper.managed}
        return data
    })
    fetchSocietyStore.society.orderMin.code = 'EUR'
    fetchSocietyStore.society.invoiceMin.code = 'EUR'

    const list = computed(() => ({...fetchSocietyStore.society, ...managed.value}))
    const listSuppliers = computed(() => ({
        ...fetchSuppliersStore.supplier,
        ...list.value,
        ...fetchSocietyStore.vatMessageValue,
        ...fetchSocietyStore.incotermsValue
    }))

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
                    optionsCountries.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsCountries.value
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
    async function ajout(inputValues) {
        const data = {
            address: {
                address: inputValues.address ?? '',
                address2: inputValues.address2 ?? '',
                city: inputValues.city ?? '',
                country: inputValues.country ?? '',
                email: inputValues.email ?? '',

                // phoneNumber: inputValues.getPhone ?? "",
                zipCode: inputValues.zipCode ?? ''
            },
            // default: true,
            // kind: "comptabilité",
            mobile: inputValues.mobile ?? '',
            name: inputValues.name ?? '',
            society: `/api/suppliers/${supplierId}`,
            surname: inputValues.surname ?? ''
        }
        try {
            await fetchSupplierContactsStore.ajout(data, societyId)

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
        await fetchSupplierContactsStore.deleted(id)
    }
    async function updateSuppliers(inputValues) {
        const dataUpdate = {
            address: {
                address: inputValues.address ?? '',
                address2: inputValues.address2 ?? '',
                city: inputValues.city ?? '',
                country: inputValues.country ?? '',
                email: inputValues.email ?? '',
                // phoneNumber: inputValues.getPhone ?? "",
                zipCode: inputValues.zipCode ?? ''
            },
            // default: true,
            // kind: "comptabilité",
            mobile: inputValues.mobile ?? '',
            name: inputValues.name ?? '',
            society: `/api/suppliers/${supplierId}`,
            surname: inputValues.surname ?? ''
        }
        try {
            const item = generateSupplierContact(inputValues)
            await item.update(dataUpdate)
            isError2.value = false
        } catch (error) {
            await fetchSupplierContactsStore.fetchBySociety(societyId)
            itemsTable.value = fetchSupplierContactsStore.itemsSocieties.reduce(
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
    <AppTabs id="gui-start" class="gui-start-content">
        <AppSupplierShowTabGeneral/>
        <AppSupplierShowTabFichiers/>
        <AppSupplierShowTabQuality :component-attribute="listSuppliers"/>
        <AppSupplierShowTabPurchase/>
        <AppSupplierShowTabAccounting :component-attribute="listSuppliers"/>
        <AppTab
            id="gui-start-addresses"
            title="Adresses"
            icon="location-dot"
            tabs="gui-start">
            <AppCardShow
                id="addAdresses"
                :fields="Adressefields"
                :component-attribute="fetchSuppliersStore.supplier"
                @update="updateAddress(fetchSuppliersStore.supplier)"/>
        </AppTab>
        <AppTab
            id="gui-start-contacts"
            title="Contacts"
            icon="file-contract"
            tabs="gui-start">
            <AppCollectionTable
                v-if="!isShow"
                id="addContacts"
                :fields="fieldsSupp"
                :items="itemsTable"
                @ajout="ajout"
                @deleted="deleted"
                @update="updateSuppliers"/>

            <div v-if="isError2" class="alert alert-danger" role="alert">
                <div v-for="violation in violations2" :key="violation">
                    <li>{{ violation.propertyPath }} {{ violation.message }}</li>
                </div>
            </div>
        </AppTab>
    </AppTabs>
</template>
