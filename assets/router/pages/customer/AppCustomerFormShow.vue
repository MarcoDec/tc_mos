<script setup>
    import {computed, ref} from 'vue'
    import MyTree from '../../../components/MyTree.vue'
    import generateCustomer from '../../../stores/customers/customer'
    import generateCustomerContact from '../../../stores/customers/customerContact'
    import generateSocieties from '../../../stores/societies/societie'
    import {useCustomerAttachmentStore} from '../../../stores/customers/customerAttachment'
    import {useCustomerContactsStore} from '../../../stores/customers/customerContacts'
    import {useCustomerStore} from '../../../stores/customers/customers'
    import {useIncotermStore} from '../../../stores/incoterm/incoterm'
    import useOptions from '../../../stores/option/options'
    import {useSocietyStore} from '../../../stores/societies/societies'
    import {useSuppliersStore} from '../../../stores/supplier/suppliers'

    const isError = ref(false)
    const isShow = ref(false)
    const violations = ref([])
    const fecthOptions = useOptions('countries')
    const fecthCompanyOptions = useOptions('companies')
    await fecthOptions.fetchOp()
    await fecthCompanyOptions.fetchOp()
    const fetchCustomerStore = useCustomerStore()
    const fetchCustomerAttachmentStore = useCustomerAttachmentStore()
    const fetchSocietyStore = useSocietyStore()
    const fecthIncotermStore = useIncotermStore()
    const fecthSuppliersStore = useSuppliersStore()
    const fecthCustomerContactsStore = useCustomerContactsStore()
    await fetchCustomerStore.fetch()
    await fetchCustomerAttachmentStore.fetch()
    await fetchCustomerStore.fetchInvoiceTime()
    await fetchSocietyStore.fetch()
    await fecthIncotermStore.fetch()
    await fecthSuppliersStore.fetchVatMessage()

    const societyId = Number(fetchCustomerStore.customer.society.match(/\d+/))
    const customerId = Number(fetchCustomerStore.customer.id)
    await fetchSocietyStore.fetchById(societyId)
    await fecthCustomerContactsStore.fetchBySociety(societyId)
    const itemsTable = computed(() =>
        fecthCustomerContactsStore.itemsSocieties.reduce(
            (acc, curr) => acc.concat(curr),
            []
        ))
    const dataCustomers = computed(() =>
        ({...fetchCustomerStore.customer, ...fetchSocietyStore.item}))
    console.log('dataCustomers', dataCustomers)
    console.log('fetchCustomerStore', fetchCustomerStore)
    console.log('fecthCustomerContactsStore', fecthCustomerContactsStore)
    const customerAttachment = computed(() =>
        fetchCustomerAttachmentStore.customerAttachment.map(attachment => ({
            icon: 'file-contract',
            id: attachment['@id'],
            label: attachment.url.split('/').pop(), // get the filename from the URL
            url: attachment.url
        })))
    const treeData = computed(() => {
        const data = {
            children: customerAttachment.value,
            icon: 'folder',
            id: 1,
            label: `Attachments (${customerAttachment.value.length})`
        }
        return data
    })

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
    const optionsCompany = computed(() =>
        fecthCompanyOptions.options.map(op => {
            const text = op.text
            const value = op['@id']
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
        {label: 'Qualité', name: 'Qualité', type: 'number'},
        {label: 'Nb PPM', name: 'ppmRate', type: 'number'},
        {label: 'url *', name: 'getUrl', type: 'text'},
        {label: 'ident', name: 'getUsername', type: 'text'},
        {label: 'password', name: 'getPassword', type: 'password'}
    ]
    const Logistiquefields = [
        {label: 'Nombre de bordereau ', name: 'nbDeliveries', type: 'number'},
        {label: 'DuréeTransport', name: 'conveyanceDuration', type: 'measure'},
        {label: 'Encours maximum', name: 'outstandingMax', type: 'measure'},
        {label: 'Url *', name: 'getUrl', type: 'text'},
        {label: 'Ident', name: 'getUsername', type: 'text'},
        {label: 'Password', name: 'getPassword', type: 'password'},
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
        {label: 'Commande minimum', name: 'orderMin', type: 'measure'}
    ]
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
        {label: 'nb de factures', name: 'nbInvoices', type: 'number'},
        {label: 'url *', name: 'getUrl', type: 'text'},
        {label: 'ident', name: 'getUsername', type: 'text'},
        {label: 'password', name: 'getPassword', type: 'password'},
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
    const Géneralitésfields = [
        {label: 'Langue', name: 'language', type: 'text'},
        {label: 'Serie', name: 'siren', type: 'text'},
        {
            label: 'Compagnies',
            name: 'administeredBy',
            options: {
                label: value =>
                    optionsCompany.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsCompany.value
            },
            type: 'select'
        },
        {label: 'Web', name: 'web', type: 'text'},
        {label: 'Note', name: 'notes', type: 'textarea'},
        {label: 'Accusé de réception', name: 'ar', type: 'boolean'},
        {label: 'Equivalence', name: 'equivalentEnabled', type: 'boolean'}
    ]
    const Fichiersfields = [{label: 'Fichier', name: 'file', type: 'file'}]
    function updateFichiers(value) {
        console.log('updateFichiers value==', value)
        //const customerId = Number(value['@id'].match(/\d+/)[0])
        const form = document.getElementById('addFichiers')
        const formData = new FormData(form)
        console.log('formData**', formData.get('file'))

        const data = {
            category: 'doc',
            customer: `/api/customers/${customerId}`,
            file: formData.get('file')
        }
        console.log('data Fichiers**', data)

        fetchCustomerAttachmentStore.ajout(data)
        customerAttachment.value = computed(() =>
            fetchCustomerAttachmentStore.customerAttachment.map(attachment => ({
                icon: 'file-contract',
                id: attachment['@id'],
                label: attachment.url.split('/').pop(), // get the filename from the URL
                url: attachment.url
            })))
        treeData.value = {
            children: customerAttachment.value,
            icon: 'folder',
            id: 1,
            label: `Attachments (${customerAttachment.value.length})`

        }
    }
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
        await fetchCustomerStore.fetch()
    }
    async function updateLogistique(value) {
        const form = document.getElementById('addLogistique')
        const formData = new FormData(form)

        const data = {
            // accountingPortal: {
            //   password: "afef",
            //   url: "https://www.monsite.fr",
            //   username: "afef",
            // },
            conveyanceDuration: {
                code: formData.get('conveyanceDuration-code'),
                value: JSON.parse(formData.get('conveyanceDuration-value'))
            },
            nbDeliveries: JSON.parse(formData.get('nbDeliveries')),
            outstandingMax: {
                code: formData.get('outstandingMax-code'),
                value: JSON.parse(formData.get('outstandingMax-value'))
            }
        }
        const dataSociety = {
            incoterms: formData.get('incotermsValue'),
            orderMin: {
                code: formData.get('orderMin-code'),
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

        await item.update(data)
        //await fetchSocietyStore.update(dataSociety, societyId)
        const itemSoc = generateSocieties(value)
        await itemSoc.update(dataSociety)
        await fetchCustomerStore.fetch()
        console.log('data===', data)
        console.log('dataSociety===', dataSociety)
    }
    async function updateComp(value) {
        const form = document.getElementById('addComptabilite')
        const formData = new FormData(form)
        const dataSociety = {
            accountingAccount: formData.get('accountingAccount'),
            forceVat: formData.get('forceVat'),
            invoiceMin: {
                code: formData.get('invoiceMin-code'),
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

        //await fetchSocietyStore.update(dataSociety, societyId)
        const itemSoc = generateSocieties(value)
        await itemSoc.update(dataSociety)
        await fetchSocietyStore.fetch()
        await fetchCustomerStore.fetch()
    }
    async function updateGeneral(value) {
        console.log('value', value)
        const form = document.getElementById('addGeneralites')
        const formData = new FormData(form)
        console.log('form', formData.get('notes').length)

        const data = {

            administeredBy: [formData.get('administeredBy')],
            equivalentEnabled: JSON.parse(formData.get('equivalentEnabled')),
            language: formData.get('language'),
            notes: formData.get('notes') ? formData.get('notes') : null

        }
        const dataSociety = {
            ar: JSON.parse(formData.get('ar')),
            siren: formData.get('siren'),
            web: formData.get('web')
        }
        const item = generateCustomer(value)
        await item.updateMain(data)
        const itemSoc = generateSocieties(value)
        await itemSoc.update(dataSociety)
        await fetchSocietyStore.fetch()
        await fetchCustomerStore.fetch()
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

        await fetchCustomerStore.fetch()
        const itemSoc = generateSocieties(value)
        await itemSoc.update(dataSociety)
        await fetchSocietyStore.fetch()
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
        console.log('ddddd', data)
        console.log('inputValues.society', inputValues)
        try {
            await fecthCustomerContactsStore.ajout(data, societyId)
            isError.value = false
        } catch (error) {
            if (error === 'Internal Server Error') {
                const err = {message: 'Internal Server Error'}
                violations.value.push(err)
            } else {
                violations.value = error
                isError.value = true
            }
        }
    }
    async function deleted(id) {
        await fecthCustomerContactsStore.deleted(id)
    }
    async function updateSuppliers(inputValues) {
        console.log('inpiuutttt', inputValues)
        console.log('id', inputValues.society)
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
            isError.value = false
        } catch (error) {
            await fecthCustomerContactsStore.fetchBySociety(societyId)
            itemsTable.value = fecthCustomerContactsStore.itemsSocieties.reduce(
                (acc, curr) => acc.concat(curr),
                []
            )
            if (error === 'Internal Server Error') {
                const err = {message: 'Internal Server Error'}
                violations.value.push(err)
            } else {
                violations.value = error
                isError.value = true
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
            <AppCardShow
                id="addGeneralites"
                :fields="Géneralitésfields"
                :component-attribute="dataCustomers"
                @update="updateGeneral(dataCustomers)"/>
        </AppTab>
        <AppTab
            id="gui-start-files"
            title="Fichiers"
            icon="laptop"
            tabs="gui-start">
            <AppCardShow
                id="addFichiers"
                :fields="Fichiersfields"
                :component-attribute="dataCustomers"
                @update="updateFichiers(dataCustomers)"/>

            <MyTree :node="treeData"/>
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
            title="Adresses"
            icon="location-dot"
            tabs="gui-start">
            <AppCardShow
                id="addAdresses"
                :fields="Adressefields"
                :component-attribute="dataCustomers"
                @update="updateAdresse(dataCustomers)"/>
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

            <div v-if="isError" class="alert alert-danger" role="alert">
                <div v-for="violation in violations" :key="violation">
                    <li>{{ violation.message }}</li>
                </div>
            </div>
        </AppTab>
    </AppTabs>
</template>
