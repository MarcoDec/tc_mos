<script setup>
    import {computed, ref} from 'vue-demi'
    import AppFormJS from '../../../form/AppFormJS.js'
    import AppTab from '../../../tabs/AppTab.vue'
    import AppTabs from '../../../tabs/AppTabs.vue'
    import useOptions from '../../../../stores/option/options'
    import {useCustomersStore} from '../../../../stores/customer/customers'
    import {useInvoiceTimeDuesStore} from '../../../../stores/management/invoiceTimeDues'
    defineProps({
        title: {required: true, type: String},
        target: {required: true, type: String},
        modalId: {required: true, type: String}
    })

    const storeCustomersList = useCustomersStore()
    let violations = []
    let success = []
    const isPopupVisible = ref(false)
    const isCreatedPopupVisible = ref(false)

    const fecthOptionsSociety = useOptions('societies')
    await fecthOptionsSociety.fetchOp()
    const optionsSociety = computed(() =>
        fecthOptionsSociety.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))

    const storeinvoiceTimeDuesList = useInvoiceTimeDuesStore()
    await storeinvoiceTimeDuesList.invoiceTimeDuesOption()
    const optionsInvoiceTimeDue = storeinvoiceTimeDuesList.invoiceTimeDuesOptions
    // const fecthOptionsInvoiceTimeDue = useOptions('invoice-time-dues')
    // await fecthOptionsInvoiceTimeDue.fetchOp()
    // const optionsInvoiceTimeDue = computed(() =>
    //     fecthOptionsInvoiceTimeDue.options.map(op => {
    //         const text = op.text
    //         const value = op.value
    //         return {text, value}
    //     }))

    const fetchCurrencyOptions = useOptions('currencies')
    await fetchCurrencyOptions.fetchOp()
    const optionsCurrency = fetchCurrencyOptions.options.map(op => {
        const text = op.text
        const value = op.value
        return {text, value}
    })

    // const fetchCountryOptions = useOptions('countries')
    // await fetchCountryOptions.fetchOp()
    // const optionsCountry = fetchCountryOptions.options.map(op => {
    //     const text = op.text
    //     const value = op.value
    //     return {text, value}
    // })

    const fields = computed(() => [
        {label: 'Société', name: 'society', options: {label: value => optionsSociety.value.find(option => option.type === value)?.text ?? null, options: optionsSociety.value}, type: 'select'},
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Adresse', name: 'address', type: 'text'},
        {label: 'complément d\'adresse', name: 'address2', type: 'text'},
        {label: 'ville', name: 'city', type: 'text'},
        {label: 'Code postal', name: 'zipCode', type: 'text'},
        // {label: 'Pays*', name: 'country',options: {label: value =>optionsCountry.value.find(option => option.type === value)?.text ?? null, options: optionsCountry.value}, type: 'select'},
        {label: 'Pays*', name: 'country', type: 'text'},
        {label: 'Téléphone', name: 'phoneNumber', type: 'text'},
        {label: 'Email', name: 'email', type: 'text'},
        {label: 'modalités de paiement', name: 'paymentTerms', options: {label: value => optionsInvoiceTimeDue.find(option => option.type === value)?.text ?? null, options: optionsInvoiceTimeDue}, type: 'select'}
    ])

    const fieldsComp = computed(() => [
        {label: 'Devise', name: 'currency', options: {label: value => optionsCurrency.find(option => option.type === value)?.text ?? null, options: optionsCurrency}, type: 'select'}
    ])
    const fieldsCuivre = computed(() => [
        {label: 'CopperIndex', name: 'copperIndex ', type: 'number'},
        {label: 'CopperType', name: 'copperType', type: 'text'},
        {label: 'Gest. cuivre', name: 'managed', type: 'boolean'},
        {label: 'Date du prochain indice', name: 'next', type: 'date'},
        {label: 'Date du dernier indice', name: 'last', type: 'date'},
        {label: 'type', name: 'type', type: 'text'}
    ])

    const generalData = {}
    const comptabilityData = {}
    const cuivreData = {}

    function generalForm(value) {
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(generalData, key)) {
            if (typeof value[key] === 'object') {
                if (typeof value[key].value !== 'undefined') {
                    const inputValue = parseFloat(value[key].value)
                    generalData[key] = {...generalData[key], value: inputValue}
                }
                if (typeof value[key].code !== 'undefined') {
                    const inputCode = value[key].code
                    generalData[key] = {...generalData[key], code: inputCode}
                }
            } else {
                generalData[key] = value[key]
            }
        } else {
            generalData[key] = value[key]
        }
    }
    function comptabilityForm(value) {
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(comptabilityData, key)) {
            if (typeof value[key] === 'object') {
                if (typeof value[key].value !== 'undefined') {
                    const inputValue = parseFloat(value[key].value)
                    comptabilityData[key] = {...comptabilityData[key], value: inputValue}
                }
                if (typeof value[key].code !== 'undefined') {
                    const inputCode = value[key].code
                    comptabilityData[key] = {...comptabilityData[key], code: inputCode}
                }
            } else {
                comptabilityData[key] = value[key]
            }
        } else {
            comptabilityData[key] = value[key]
        }
    }
    function cuivreForm(value) {
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(cuivreData, key)) {
            if (typeof value[key] === 'object') {
                if (typeof value[key].value !== 'undefined') {
                    const inputValue = parseFloat(value[key].value)
                    cuivreData[key] = {...cuivreData[key], value: inputValue}
                }
                if (typeof value[key].code !== 'undefined') {
                    const inputCode = value[key].code
                    cuivreData[key] = {...cuivreData[key], code: inputCode}
                }
            } else {
                cuivreData[key] = value[key]
            }
        } else {
            cuivreData[key] = value[key]
        }
    }

    async function customerFormCreate(){
        try {
            const customer = {
                address: {
                    address: generalData?.address || '',
                    address2: generalData?.address2 || '',
                    city: generalData?.city || '',
                    country: 'FR',
                    // "country":  generalForm?.value?.value?.country || '',
                    email: generalData?.email || '',
                    phoneNumber: generalData?.phoneNumber || '',
                    zipCode: generalData?.zipCode || ''
                },
                copper: {
                    index: {
                        code: cuivreData?.copperType || '',
                        value: cuivreData?.copperIndex || 0
                    },
                    last: cuivreData?.last || null,
                    managed: cuivreData?.managed || false,
                    next: cuivreData?.next || null,
                    type: cuivreData?.type || ''
                },
                currency: comptabilityData?.currency || '#',
                name: generalData?.name || '#',
                paymentTerms: '/api/invoice-time-dues/1',
                society: generalData?.society || '#'
            }
            console.log('customer', customer)
            await storeCustomersList.addCustomer(customer)
            isPopupVisible.value = false
            isCreatedPopupVisible.value = true
            success = 'client crée'
        } catch (error) {
            violations = error
            isPopupVisible.value = true
            isCreatedPopupVisible.value = false
            console.log('violations', violations)
        }
    }
</script>

<template>
    <AppModal :id="modalId" class="four" :title="title">
        <AppTabs id="gui-start" class="gui-start-content">
            <AppTab id="gui-start-general" active icon="sitemap" title="Général">
                <AppFormJS id="supplier" :fields="fields" @update:model-value="generalForm"/>
            </AppTab>
            <AppTab id="gui-start-comptabilite" icon="chart-line" title="Comptabilité">
                <AppFormJS id="comptabilite" :fields="fieldsComp" @update:model-value="comptabilityForm"/>
            </AppTab>
            <AppTab id="gui-start-cuivre" icon="clipboard-list" title="Cuivre">
                <AppFormJS id="cuivre" :fields="fieldsCuivre" @update:model-value="cuivreForm"/>
            </AppTab>
        </AppTabs>
        <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
            <li>{{ violations }}</li>
        </div>
        <div v-if="isCreatedPopupVisible" class="alert alert-success" role="alert">
            <li>{{ success }}</li>
        </div>
        <template #buttons>
            <AppBtn
                variant="success"
                label="Créer"
                data-bs-toggle="modal"
                :data-bs-target="target"
                @click="customerFormCreate">
                Créer
            </AppBtn>
        </template>
    </AppModal>
</template>

<style>
.cardOrderSupplier {
  border: 6px solid #1d583d;
}
</style>
