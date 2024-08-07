<script setup>
    import {computed, ref} from 'vue'
    import generateCustomer from '../../../../../stores/selling/customers/customer'
    import {useCustomerStore} from '../../../../../stores/selling/customers/customers'
    import {useInvoiceTimeDuesStore} from '../../../../../stores/management/invoiceTimeDues'
    import {useSocietyStore} from '../../../../../stores/management/societies/societies'
    import {useVatMessagesStore} from '../../../../../stores/management/vatMessages'

    const props = defineProps({
        dataCustomers: {required: true, type: Object},
        dataSociety: {required: true, type: Object}
    })
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
    const accountingPortalFields = [
        {label: 'Url', name: 'url', type: 'text'},
        {label: 'Login', name: 'username', type: 'text'},
        {label: 'Mot de passe', name: 'password', type: 'text'}
    ]
    const accountingFields = [
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
        {label: 'Compte de comptabilité', name: 'accountingAccount', type: 'text'}
    ]
    const localPortalData = ref({})
    localPortalData.value = {
        url: props.dataCustomers.accountingPortal.url,
        username: props.dataCustomers.accountingPortal.username,
        password: props.dataCustomers.accountingPortal.password
    }
    const localData = ref({})
    localData.value = {
        accountingAccount: props.dataSociety.accountingAccount,
        forceVat: props.dataSociety.forceVat,
        invoiceByEmail: props.dataCustomers.invoiceByEmail,
        invoiceMin: {
            code: 'EUR',
            value: props.dataSociety.invoiceMin.value
        },
        nbInvoices: props.dataCustomers.nbInvoices,
        paymentTerms: props.dataCustomers.paymentTerms,
        vat: props.dataSociety.vat,
        vatMessage: props.dataSociety.vatMessage
    }
    async function updatePortalComp() {
        const dataCustomer = {
            accountingPortal: {
                password: localData.value.password,
                url: localData.value.url,
                username: localData.value.username
            }
        }
        const item = generateCustomer(props.dataCustomers)
        await item.updateAccounting(dataCustomer)
        await fetchCustomerStore.fetchOne(props.dataCustomers.id)
    }
    async function updateComp() {
        const dataSociety = {
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
            invoiceByEmail: localData.value.invoiceByEmail,
            nbInvoices: parseInt(localData.value.nbInvoices),
            paymentTerms: localData.value.paymentTerms
        }

        const item = generateCustomer(props.dataCustomers)
        await item.updateAccounting(dataCustomer)

        await fetchSocietyStore.update(dataSociety, props.dataSociety.id)
        await fetchSocietyStore.fetchById(props.dataSociety.id)
        await fetchCustomerStore.fetchOne(props.dataCustomers.id)
    }
    function updateLocalPortalData(value) {
        localPortalData.value = value
    }
    function updateLocalData(value) {
        localData.value = value
    }
</script>

<template>
    <div class="tabAccounting">
        <AppCardShow
            id="addAccounting"
            class="accountingItem"
            :fields="accountingFields"
            :component-attribute="localData"
            title="Paramètres comptabilité"
            @update="updateComp"
            @update:model-value="updateLocalData"/>
        <AppCardShow
            id="addAccountingPortal"
            class="accountingItem"
            :fields="accountingPortalFields"
            :component-attribute="localPortalData"
            title="Portail Comptabilité"
            @update="updatePortalComp"
            @update:model-value="updateLocalPortalData"/>
    </div>
</template>

<style scoped>
    div.tabAccounting {
        display: flex;
        justify-content: center;
        flex-wrap: wrap
    }
    .accountingItem {
        min-width: 500px;
        margin-left: 20px;
        margin-bottom: 20px;
    }
</style>
