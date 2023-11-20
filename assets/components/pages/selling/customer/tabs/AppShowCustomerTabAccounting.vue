<script setup>
    import {computed, ref, toRefs} from 'vue'
    import generateCustomer from '../../../../../stores/selling/customers/customer'
    import {useCustomerStore} from '../../../../../stores/selling/customers/customers'
    import {useInvoiceTimeDuesStore} from '../../../../../stores/management/invoiceTimeDues'
    import {useSocietyStore} from '../../../../../stores/management/societies/societies'
    import {useVatMessagesStore} from '../../../../../stores/management/vatMessages'

    const {dataCustomers, dataSociety} = toRefs(defineProps({
        dataCustomers: {required: true, type: Object},
        dataSociety: {required: true, type: Object}
    }))
    const fetchCustomerStore = useCustomerStore()
    const fetchSocietyStore = useSocietyStore()
    const fetchVatMessageStore = useVatMessagesStore()
    const fetchInvoiceTimeDuesStore = useInvoiceTimeDuesStore()
    const optionsVatMessageForce = [
        {
            text: 'TVA par défaut selon le pays du client',
            value: 'TVA par défaut selon le pays du client'
        },
        {text: 'Force AVEC TVA', value: 'Force AVEC TVA'},
        {text: 'Force SANS TVA', value: 'Force SANS TVA'}
    ]
    await fetchVatMessageStore.fetchVatMessage()
    const optionsVat = computed(() =>
        fetchVatMessageStore.vatMessage.map(op => {
            const text = op.name
            const value = op['@id']
            return {text, value}
        }))
    await fetchInvoiceTimeDuesStore.fetchInvoiceTime()
    const optionsInvoice = computed(() =>
        fetchInvoiceTimeDuesStore.invoicesData.map(invoice => {
            const text = invoice.name
            const value = invoice['@id']
            return {text, value}
        }))

    const accountingFields = [
        // {
        //     children: [
        // {label: 'TVA intracommunautaire', name: 'vat', type: 'text'},
        // {
        //     label: 'Mode forçage TVA',
        //     name: 'forceVat',
        //     options: {
        //         label: value =>
        //             optionsVatMessageForce.find(option => option.type === value)?.text
        //             ?? null,
        //         options: optionsVatMessageForce
        //     },
        //     type: 'select'
        // },
        // {
        //     label: 'Message TVA',
        //     name: 'vatMessageValue',
        //     options: {
        //         label: value =>
        //             optionsVat.value.find(option => option.type === value)?.text ?? null,
        //         options: optionsVat.value
        //     },
        //     type: 'select'
        // }
        //     ],
        //     label: 'Gestion TVA',
        //     mode: 'fieldset',
        //     name: 'Gestion TVA'
        // },
        {label: 'TVA intracommunautaire', name: 'vat', type: 'text'},
        {
            label: 'Mode forçage TVA',
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
            label: 'Message TVA',
            name: 'vatMessage',
            options: {
                label: value =>
                    optionsVat.value.find(option => option.type === value)?.text ?? null,
                options: optionsVat.value
            },
            type: 'select'
        },
        // {
        //     children: [
        // {
        //     label: 'Conditions de paiement',
        //     name: 'paymentTerms',
        //     options: {
        //         label: value =>
        //             optionsInvoice.value.find(option => option.type === value)?.text
        //             ?? null,
        //         options: optionsInvoice.value
        //     },
        //     type: 'select'
        // },
        // {
        //     label: 'Montant minimum factures',
        //     measure: {code: 'Devise', value: 'valeur'},
        //     name: 'invoiceMin',
        //     type: 'measure'
        // },
        // {label: 'Envoi factures par email', name: 'invoiceByEmail', type: 'boolean'},
        // {label: 'Nb. max. factures par mois', name: 'nbInvoices', type: 'number'},
        // {label: 'Compte de comptabilité', name: 'accountingAccount', type: 'text'}
        //     ],
        //     label: 'Facturation',
        //     mode: 'fieldset',
        //     name: 'Facturation'
        // },
        {
            label: 'Conditions de paiement',
            name: 'paymentTerms',
            options: {
                label: value =>
                    optionsInvoice.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsInvoice.value
            },
            type: 'select'
        },
        {
            label: 'Montant minimum factures',
            measure: {code: 'Devise', value: 'valeur'},
            name: 'invoiceMin',
            type: 'measure'
        },
        {label: 'Envoi factures par email', name: 'invoiceByEmail', type: 'boolean'},
        {label: 'Nb. max. factures par mois (<255)', name: 'nbInvoices', type: 'number'},
        {label: 'Compte de comptabilité', name: 'accountingAccount', type: 'text'},
        // {
        //     children: [
        // {label: 'Url', name: 'getUrl', type: 'text'},
        // {label: 'Login', name: 'getUsername', type: 'text'},
        // {label: 'Mot de passe', name: 'getPassword', type: 'text'}
        //     ],
        //     label: 'Portail Web',
        //     mode: 'fieldset',
        //     name: 'Portail Web'
        // }
        {label: 'Url', name: 'url', type: 'text'},
        {label: 'Login', name: 'username', type: 'text'},
        {label: 'Mot de passe', name: 'password', type: 'text'}
    ]
    const localData = ref({})
    localData.value = {
        accountingAccount: dataSociety.value.accountingAccount,
        forceVat: dataSociety.value.forceVat,
        // accountingPortal: {
        //     password: props.dataCustomers.accountingPortal.password,
        //     url: props.dataCustomers.accountingPortal.url,
        //     username: props.dataCustomers.accountingPortal.username
        // },
        invoiceByEmail: dataCustomers.value.invoiceByEmail,
        invoiceMin: {
            code: 'EUR',
            value: dataSociety.value.invoiceMin.value
        },
        nbInvoices: dataCustomers.value.nbInvoices,
        password: dataCustomers.value.accountingPortal.password,
        paymentTerms: dataCustomers.value.paymentTerms,
        url: dataCustomers.value.accountingPortal.url,
        username: dataCustomers.value.accountingPortal.username,
        vat: dataSociety.value.vat,
        vatMessage: dataSociety.value.vatMessage
    }
    async function updateComp() {
        const localDataSociety = {
            accountingAccount: localData.value.accountingAccount,
            forceVat: localData.value.forceVat,
            invoiceMin: {
                code: 'EUR',
                value: parseFloat(localData.value.invoiceMin.value)
            },
            vat: localData.value.vat,
            vatMessage: localData.value.vatMessage
        }
        const dataCustomer = {
            accountingPortal: {
                password: localData.value.password,
                url: localData.value.url,
                username: localData.value.username
            },
            invoiceByEmail: localData.value.invoiceByEmail,
            nbInvoices: parseInt(localData.value.nbInvoices),
            paymentTerms: localData.value.paymentTerms
        }

        const item = generateCustomer(dataCustomers.value)
        await item.updateAccounting(dataCustomer)

        await fetchSocietyStore.update(localDataSociety, dataSociety.value.id)
        await fetchSocietyStore.fetchById(dataSociety.value.id)
        await fetchCustomerStore.fetchOne(dataCustomers.value.id)
    }
    function updateLocalData(value) {
        localData.value = value
    }
</script>

<template>
    <AppCardShow
        id="addAccounting"
        :fields="accountingFields"
        :component-attribute="localData"
        @update="updateComp"
        @update:model-value="updateLocalData"/>
</template>
