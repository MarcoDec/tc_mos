<script setup>
    import {computed} from 'vue'
    import generateCustomer from '../../../../stores/customers/customer'
    import {useCustomerStore} from '../../../../stores/customers/customers'
    import {useSuppliersStore} from '../../../../stores/supplier/suppliers'

    /*const props = */defineProps({
        dataCustomers: {required: true, type: Object}
    })
    const fecthSuppliersStore = useSuppliersStore()
    const fetchCustomerStore = useCustomerStore()
    await fecthSuppliersStore.fetchVatMessage()
    // const dataCustomers = computed(() => ({
    //     ...fetchCustomerStore.customer,
    //     ...fetchSocietyStore.society,
    //     ...fetchSocietyStore.incotermsValue,
    //     ...fetchSocietyStore.vatMessageValue
    // }))
    const optionsVatMessageForce = [
        {
            text: 'TVA par défaut selon le pays du client',
            value: 'TVA par défaut selon le pays du client'
        },
        {text: 'Force AVEC TVA', value: 'Force AVEC TVA'},
        {text: 'Force SANS TVA', value: 'Force SANS TVA'}
    ]
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
</script>

<template>
    <AppCardShow
        id="addComptabilite"
        :fields="Comptabilitéfields"
        :component-attribute="dataCustomers"
        @update="updateComp(dataCustomers)"/>
</template>
