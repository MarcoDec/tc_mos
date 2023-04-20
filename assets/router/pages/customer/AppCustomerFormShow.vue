<script setup>
    import {computed, h, reactive, ref, toRefs} from 'vue'
    import {useCustomerStore} from '../../../stores/customers/customers'
    import {useCustomerAttachmentStore} from '../../../stores/customers/customerAttachment'
    import generateCustomer from '../../../stores/customers/customer'
    import useOptions from '../../../stores/option/options'
    import {useSocietyStore} from '../../../stores/societies/societies'
    import {useIncotermStore} from '../../../stores/incoterm/incoterm'
    import generateSocieties from '../../../stores/societies/societie'
    import {useSuppliersStore} from '../../../stores/supplier/suppliers'
    import Fa from '../../../components/Fa'
    import MyTree from '../../../components/MyTree.vue'

    const fecthOptions = useOptions('countries')
    await fecthOptions.fetch()

    const fetchCustomerStore = useCustomerStore()
    const fetchCustomerAttachmentStore = useCustomerAttachmentStore()
    const fecthSocietyStore = useSocietyStore()
    const fecthIncotermStore = useIncotermStore()
    const fecthSuppliersStore = useSuppliersStore()
    await fetchCustomerStore.fetch()
    await fetchCustomerAttachmentStore.fetch()
    await fetchCustomerStore.fetchInvoiceTime()
    await fecthSocietyStore.fetch()
    await fecthIncotermStore.fetch()
    await fecthSuppliersStore.fetchVatMessage()

    const societyId = Number(fetchCustomerStore.customer.society.match(/\d+/))
    await fecthSocietyStore.fetchById(societyId)
    const dataCustomers = computed(() =>
        Object.assign(fetchCustomerStore.customer, fecthSocietyStore.item))

    const customerAttachment = computed(() =>
        fetchCustomerAttachmentStore.customerAttachment.map(attachment => ({
            id: attachment['@id'],
            label: attachment.url.split('/').pop(), // get the filename from the URL
            icon: 'file-contract',
            url: attachment.url
        })))
    const treeData = {
        id: 1,
        label: 'Attachments',
        icon: 'folder',
        children: customerAttachment.value
    }

    const selectedAttachment = ref(null)

    const openAttachment = node => {
        if (node.url) {
            selectedAttachment.value = node.url
        }
    }
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
    const options = [
        {text: 'aaaaa', value: 'aaaaa'},
        {text: 'bbbb', value: 'bbbb'}
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
        {label: 'forcer la TVA', name: 'forceVat', type: 'text'},
        {
            label: 'montant de la facture minimum',
            name: 'invoiceMin',
            type: 'measure'
        },
        {
            label: 'message TVA',
            name: 'vatMsg',
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
    const Géneralitésfields = [{label: 'Note', name: 'notes', type: 'textarea'}]
    const Fichiersfields = [{label: 'Fichier', name: 'file', type: 'file'}]
    function updateFichiers(value) {
        console.log('updateFichiers value==', value)
        const customerId = Number(value['@id'].match(/\d+/)[0])
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
    }
    async function updateQte(value) {
        console.log('value', value)
        const form = document.getElementById('addQualite')
        const formData = new FormData(form)
        const data = {ppmRate: JSON.parse(formData.get('ppmRate'))}
        console.log('data===', data)
        await fecthSocietyStore.update(data, societyId)

        await fetchCustomerStore.fetch()
    }
    async function updateLogistique(value) {
        console.log('value', value)
        const customerId = Number(value['@id'].match(/\d+/)[0])
        const form = document.getElementById('addLogistique')
        const formData = new FormData(form)

        const data = {
            accountingPortal: {
                password: 'afef',
                url: 'https://www.monsite.fr',
                username: 'afef'
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
        const item = generateCustomer(value)

        await item.update(data)
        await fecthSocietyStore.update(dataSociety, societyId)

        await fetchCustomerStore.fetch()
        console.log('data===', data)
        console.log('dataSociety===', dataSociety)
    }
    async function updateComp(value) {
        console.log('value', value)
        const form = document.getElementById('addComptabilite')
        const formData = new FormData(form)
        const dataSociety = {
            accountingAccount: formData.get('accountingAccount'),
            forceVat: 'TVA par défaut selon le pays du client',
            invoiceMin: {
                code: formData.get('invoiceMin-code'),
                value: JSON.parse(formData.get('invoiceMin-value'))
            },
            vatMessage: formData.get('vatMsg')
        }
        const dataCustomer = {
            nbInvoices: JSON.parse(formData.get('nbInvoices')),
            paymentTerms: formData.get('paymentTerms')
        }

        console.log('dataSociety', dataSociety)
        console.log('dataCustomer------>', dataCustomers)
        const item = generateCustomer(value)
        await item.updateAccounting(dataCustomer)

        await fecthSocietyStore.update(dataSociety, societyId)

        await fecthSocietyStore.fetch()
        await fetchCustomerStore.fetch()
    }
    async function updateGeneral(value) {
        console.log('value', value)
        const customerId = Number(value['@id'].match(/\d+/)[0])
        const form = document.getElementById('addGeneralites')
        const formData = new FormData(form)
        console.log('form', formData.get('notes'))
        const data = {
            notes: formData.get('notes')
        }
        const item = generateCustomer(value)

        await item.updateMain(data)

        await fetchCustomerStore.fetch()
    }

    async function updateAdresse(value) {
        const societyId = Number(value['@id'].match(/\d+/))
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

        await fetchCustomerStore.fetch()
        await fecthSocietyStore.update(data, societyId)
        await fecthSocietyStore.fetch()
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

            <MyTree :node="treeData" @node-click="openAttachment"/>
            <div v-if="selectedAttachment">
                Selected Attachment: {{ selectedAttachment }}
            </div>
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
            <AppCardShow id="addContacts"/>
        </AppTab>
    </AppTabs>
</template>
