<script setup>
    import {computed, ref} from 'vue'
    import AppShowCustomerTabGeneral from '../../../components/pages/selling/customer/AppShowCustomerTabGeneral.vue'
    import AppTabFichiers from '../../../components/tab/AppTabFichiers.vue'
    import generateCustomer from '../../../stores/customers/customer'
    import generateCustomerContact from '../../../stores/customers/customerContact'
    import generateSocieties from '../../../stores/societies/societie'
    import {useCustomerAttachmentStore} from '../../../stores/customers/customerAttachment'
    import {useCustomerContactsStore} from '../../../stores/customers/customerContacts'
    import {useCustomerStore} from '../../../stores/customers/customers'
    import {useIncotermStore} from '../../../stores/incoterm/incoterm'
    import useOptions from '../../../stores/option/options'
    import {useRoute} from 'vue-router'
    import {useSocietyStore} from '../../../stores/societies/societies'
    import {useSuppliersStore} from '../../../stores/supplier/suppliers'

    const route = useRoute()
    const idCustomer = route.params.id_customer
    const isError2 = ref(false)
    const isShow = ref(false)
    const violations2 = ref([])
    const fecthOptions = useOptions('countries')
    await fecthOptions.fetchOp()
    const fetchCustomerStore = useCustomerStore()
    const fetchSocietyStore = useSocietyStore()
    const fecthIncotermStore = useIncotermStore()
    const fecthSuppliersStore = useSuppliersStore()
    const fetchCustomerAttachmentStore = useCustomerAttachmentStore()
    const fecthCustomerContactsStore = useCustomerContactsStore()
    await fetchCustomerStore.fetchOne(idCustomer)
    await fetchCustomerStore.fetchInvoiceTime()
    await fetchSocietyStore.fetch()
    await fecthIncotermStore.fetch()
    await fecthSuppliersStore.fetchVatMessage()
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
    const dataCustomers = computed(() => ({
        ...fetchCustomerStore.customer,
        ...fetchSocietyStore.society,
        ...fetchSocietyStore.incotermsValue,
        ...fetchSocietyStore.vatMessageValue
    }))
    const optionsVatMessageForce = [
        {
            text: 'TVA par défaut selon le pays du client',
            value: 'TVA par défaut selon le pays du client'
        },
        {text: 'Force AVEC TVA', value: 'Force AVEC TVA'},
        {text: 'Force SANS TVA', value: 'Force SANS TVA'}
    ]
    const optionsIncoterm = computed(() =>
        fecthIncotermStore.incoterms.map(incoterm => {
            const text = incoterm.name
            const value = incoterm['@id']
            const optionList = {text, value}
            return optionList
        }))
    const optionsCountries = computed(() =>
        fecthOptions.options.map(op => {
            const text = op.text
            const value = op.id
            const optionList = {text, value}
            return optionList
        }))
    const optionsVat = computed(() =>
        fecthSuppliersStore.vatMessage.map(op => {
            const text = op.name
            const value = op['@id']
            const optionList = {text, value}
            return optionList
        }))
    const optionsInvoice = computed(() =>
        fetchCustomerStore.invoicesData.map(invoice => {
            const text = invoice.name
            const value = invoice['@id']
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
    const Qualitéfields = [
        {label: 'Nb PPM', name: 'ppmRate', type: 'number'},
        {label: 'Url *', name: 'getUrl', type: 'text'},
        {label: 'Ident', name: 'getUsername', type: 'text'},
        {label: 'Password', name: 'getPassword', type: 'text'}
        //  {
        //   children: [
        //       { label: "Nb PPM", name: "ppmRate", type: "number" },

        //     { label: "Url1 *", name: "getUrl", type: "text" },
        //     { label: "Ident1", name: "getUsername", type: "text" },
        //     { label: "Password1", name: "getPassword", type: "text" },
        //   ],

        //   label: "",
        //   mode: "fieldset",
        //   name: "",
        // },
    ]
    const Logistiquefields = computed(() => [
        {
            label: 'Nombre de bons de livraison mensuel ',
            name: 'nbDeliveries',
            type: 'number'
        },
        {label: 'DuréeTransport', measure: {code: 'j', value: 'valeur'}, name: 'conveyanceDuration', type: 'measure'},
        {label: 'Encours maximum', measure: {code: 'U', value: 'valeur'}, name: 'outstandingMax', type: 'measure'},
        {label: 'Commande minimum', measure: {code: 'U', value: 'valeur'}, name: 'orderMin', type: 'measure'},
        {
            label: 'Incoterm',
            name: 'incotermsValue',
            options: {
                label: value =>
                    optionsIncoterm.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsIncoterm.value
            },
            type: 'select'
        },
        {label: 'Url *', name: 'getUrl', type: 'text'},
        {label: 'Ident', name: 'getUsername', type: 'text'},
        {label: 'Password', name: 'getPassword', type: 'text'}
        //   {
        //     children: [
        //       { label: "Url *", name: "getUrl", type: "text" },
        //       { label: "Ident", name: "getUsername", type: "text" },
        //       { label: "Password", name: "getPassword", type: "text" },
        //     ],

        //     label: "",
        //     mode: "fieldset",
        //     name: "webPo",
        //   },
    ])
    const Comptabilitéfields = [
        {label: 'compte de comptabilité', name: 'accountingAccount', type: 'text'},
        {
            label: 'forcer la TVA',
            name: 'forceVat',
            options: {
                label: value =>
                    optionsVatMessageForce.find(option => option.type === value)?.text
                    ?? null,
                options: optionsVatMessageForce
            },
            type: 'select'
        },
        {
            label: 'montant de la facture minimum',
            measure: {code: 'Devise', value: 'valeur'},
            name: 'invoiceMin',
            type: 'measure'
        },
        {
            label: 'message TVA',
            name: 'vatMessageValue',
            options: {
                label: value =>
                    optionsVat.value.find(option => option.type === value)?.text ?? null,
                options: optionsVat.value
            },
            type: 'select'
        },
        {
            label: 'conditions de paiement',
            name: 'paymentTerms',
            options: {
                label: value =>
                    optionsInvoice.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsInvoice.value
            },
            type: 'select'
        },
        {label: 'Nombre de factures mensuel', name: 'nbInvoices', type: 'number'},
        {label: 'url *', name: 'getUrl', type: 'text'},
        {label: 'ident', name: 'getUsername', type: 'text'},
        {label: 'password', name: 'getPassword', type: 'text'},
        {label: 'Envoi facture par email', name: 'invoiceByEmail', type: 'boolean'}
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
    async function updateQte(value) {
        const form = document.getElementById('addQualite')
        const formData = new FormData(form)
        const data = {ppmRate: JSON.parse(formData.get('ppmRate'))}
        const dataAccounting = {
            accountingPortal: {
                password: formData.get('getPassword'),
                url: formData.get('getUrl'),
                username: formData.get('getUsername')
            }
        }

        const item = generateCustomer(value)
        await item.updateAccounting(dataAccounting)
        const itemSoc = generateSocieties(value)
        await itemSoc.update(data)
        await fetchCustomerStore.fetchOne(idCustomer)
    }
    async function updateLogistique(value) {
        const form = document.getElementById('addLogistique')
        const formData = new FormData(form)

        const data = {
            conveyanceDuration: {
                code: 'j',
                value: JSON.parse(formData.get('conveyanceDuration-value'))
            },
            nbDeliveries: JSON.parse(formData.get('nbDeliveries')),
            outstandingMax: {
                code: 'EUR',
                value: JSON.parse(formData.get('outstandingMax-value'))
            }
        }
        const dataSociety = {
            incoterms: formData.get('incotermsValue'),
            orderMin: {
                code: 'EUR',
                value: JSON.parse(formData.get('orderMin-value'))
            }
        }
        const dataAccounting = {
            accountingPortal: {
                password: formData.get('getPassword'),
                url: formData.get('getUrl'),
                username: formData.get('getUsername')
            }
        }

        const item = generateCustomer(value)
        await item.updateAccounting(dataAccounting)
        //await fetchCustomerStore.update(dataAccounting, customerId);

        await item.update(data)
        await fetchSocietyStore.update(dataSociety, societyId)
        // const itemSoc = generateSocieties(value)
        // await itemSoc.update(dataSociety)
        await fetchCustomerStore.fetchOne(idCustomer)
    }
    async function updateComp(value) {
        const form = document.getElementById('addComptabilite')
        const formData = new FormData(form)
        const dataSociety = {
            accountingAccount: formData.get('accountingAccount'),
            forceVat: formData.get('forceVat'),
            invoiceMin: {
                code: 'EUR',
                value: JSON.parse(formData.get('invoiceMin-value'))
            },
            vatMessage: formData.get('vatMessageValue')
        }
        const dataCustomer = {
            accountingPortal: {
                password: formData.get('getPassword'),
                url: formData.get('getUrl'),
                username: formData.get('getUsername')
            },
            invoiceByEmail: JSON.parse(formData.get('invoiceByEmail')),
            nbInvoices: JSON.parse(formData.get('nbInvoices')),
            paymentTerms: formData.get('paymentTerms')
        }

        const item = generateCustomer(value)
        await item.updateAccounting(dataCustomer)

        await fetchSocietyStore.update(dataSociety, societyId)
        //   const itemSoc = generateSocieties(value);
        //   await itemSoc.update(dataSociety);
        await fetchSocietyStore.fetchById(societyId)
        await fetchCustomerStore.fetchOne(idCustomer)
    }
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
            <AppCardShow
                id="addQualite"
                :fields="Qualitéfields"
                :component-attribute="dataCustomers"
                @update="updateQte(dataCustomers)"/>
        </AppTab>
        <AppTab
            id="gui-start-purchase-logistics"
            title="Logistique"
            icon="pallet"
            tabs="gui-start">
            <AppCardShow
                id="addLogistique"
                :fields="Logistiquefields"
                :component-attribute="dataCustomers"
                @update="updateLogistique(dataCustomers)"/>
        </AppTab>
        <AppTab
            id="gui-start-accounting"
            title="Comptabilité"
            icon="industry"
            tabs="gui-start">
            <AppCardShow
                id="addComptabilite"
                :fields="Comptabilitéfields"
                :component-attribute="dataCustomers"
                @update="updateComp(dataCustomers)"/>
        </AppTab>
        <AppTab
            id="gui-start-addresses"
            title="Adresse"
            icon="location-dot"
            tabs="gui-start">
            <AppCardShow
                id="addAdresses"
                :fields="Adressefields"
                :component-attribute="fetchCustomerStore.customer"
                @update="updateAdresse(fetchCustomerStore.customer)"/>
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

