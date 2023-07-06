<script setup>
    import {computed, ref} from 'vue'
    import AppShowCustomerTabAccounting
        from '../../../components/pages/selling/customer/AppShowCustomerTabAccounting.vue'
    import AppShowCustomerTabAddress from '../../../components/pages/selling/customer/AppShowCustomerTabAddress.vue'
    import AppShowCustomerTabGeneral from '../../../components/pages/selling/customer/AppShowCustomerTabGeneral.vue'
    import AppShowCustomerTabLogistic from '../../../components/pages/selling/customer/AppShowCustomerTabLogistic.vue'
    import AppShowCustomerTabQuality from '../../../components/pages/selling/customer/AppShowCustomerTabQuality.vue'
    import AppTabFichiers from '../../../components/tab/AppTabFichiers.vue'
    import generateCustomerContact from '../../../stores/customers/customerContact'
    import {useCustomerAttachmentStore} from '../../../stores/customers/customerAttachment'
    import {useCustomerContactsStore} from '../../../stores/customers/customerContacts'
    import {useCustomerStore} from '../../../stores/customers/customers'
    import useOptions from '../../../stores/option/options'
    import {useRoute} from 'vue-router'
    import {useSocietyStore} from '../../../stores/societies/societies'

    const route = useRoute()
    const idCustomer = route.params.id_customer
    const isError2 = ref(false)
    const isShow = ref(false)
    const violations2 = ref([])
    const fecthOptions = useOptions('countries')
    await fecthOptions.fetchOp()
    const fetchCustomerStore = useCustomerStore()
    const fetchSocietyStore = useSocietyStore()
    const fetchCustomerAttachmentStore = useCustomerAttachmentStore()
    const fecthCustomerContactsStore = useCustomerContactsStore()
    await fetchCustomerStore.fetchOne(idCustomer)
    await fetchCustomerStore.fetchInvoiceTime()
    await fetchSocietyStore.fetch()
    const societyId = Number(fetchCustomerStore.customer.society.match(/\d+/))
    const customerId = Number(fetchCustomerStore.customer.id)
    await fetchSocietyStore.fetchById(societyId)
    await fecthCustomerContactsStore.fetchBySociety(societyId)
    fetchSocietyStore.society.orderMin.code = 'EUR'
    fetchCustomerStore.customer.outstandingMax.code = 'EUR'
    const itemsTable = computed(() =>
        fecthCustomerContactsStore.itemsSocieties.reduce(
            (acc, curr) => acc.concat(curr),
            []
        ))
    // const dataSuppliers = computed(() =>
    //     Object.assign(fetchSuppliersStore.suppliers, fetchSocietyStore.society))

    const optionsCountries = computed(() =>
        fecthOptions.options.map(op => {
            const text = op.text
            const value = op.id
            const optionList = {text, value}
            return optionList
        }))

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

                // phoneNumber: inputValues.getPhone ?? "",
                zipCode: inputValues.zipCode ?? ''
            },
            // default: true,
            // kind: "comptabilité",
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
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab
            id="gui-start-main"
            active
            title="Généralités"
            icon="pencil"
            tabs="gui-start">
            <Suspense><AppShowCustomerTabGeneral/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-files"
            title="Fichiers"
            icon="laptop"
            tabs="gui-start">
            <Suspense>
                <AppTabFichiers
                    attachment-element-label="customer"
                    :element-api-url="`/api/customers/${customerId}`"
                    :element-attachment-store="fetchCustomerAttachmentStore"
                    :element-id="customerId"
                    element-parameter-name="CUSTOMER_ATTACHMENT_CATEGORIES"
                    :element-store="useCustomerStore"/>
            </Suspense>
        </AppTab>
        <AppTab
            id="gui-start-quality"
            title="Qualité"
            icon="certificate"
            tabs="gui-start">
            <Suspense><AppShowCustomerTabQuality/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-purchase-logistics"
            title="Logistique"
            icon="pallet"
            tabs="gui-start">
            <Suspense><AppShowCustomerTabLogistic/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-accounting"
            title="Comptabilité"
            icon="industry"
            tabs="gui-start">
            <Suspense><AppShowCustomerTabAccounting/></Suspense>
        </AppTab>
        <AppTab
            id="gui-start-addresses"
            title="Adresse"
            icon="location-dot"
            tabs="gui-start">
            <Suspense>
                <AppShowCustomerTabAddress
                    :options-countries="optionsCountries"/>
            </Suspense>
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

<style scoped>
div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
.gui-start-content {
    font-size: 14px;
}
#gui-start-production, #gui-start-droits {
    padding-bottom: 150px;
}
</style>

