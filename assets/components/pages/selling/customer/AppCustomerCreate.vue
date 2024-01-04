<script setup>
    import {computed, defineEmits, defineProps, ref} from 'vue'
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
    const emits = defineEmits(['created'])
    const storeCustomersList = useCustomersStore()
    const violations = ref([])
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
        {label: 'Nom*', name: 'name', type: 'text'},
        {label: 'Société mère / Groupe *', name: 'society', options: {label: value => optionsSociety.value.find(option => option.type === value)?.text ?? null, options: optionsSociety.value}, type: 'select'},
        {label: 'Adresse', name: 'address', type: 'text'},
        {label: 'complément d\'adresse', name: 'address2', type: 'text'},
        {label: 'ville', name: 'city', type: 'text'},
        {label: 'Code postal', name: 'zipCode', type: 'text'},
        // {label: 'Pays*', name: 'country',options: {label: value =>optionsCountry.value.find(option => option.type === value)?.text ?? null, options: optionsCountry.value}, type: 'select'},
        {label: 'Pays', name: 'country', type: 'text'},
        {label: 'Téléphone', name: 'phoneNumber', type: 'text'},
        {label: 'Email', name: 'email', type: 'text'}
    ])

    const fieldsComp = computed(() => [
        {label: 'Devise (chiffrage et facturation)*', name: 'currency', options: {label: value => optionsCurrency.find(option => option.type === value)?.text ?? null, options: optionsCurrency}, type: 'select'},
        {label: 'modalités de paiement', name: 'paymentTerms', options: {label: value => optionsInvoiceTimeDue.find(option => option.type === value)?.text ?? null, options: optionsInvoiceTimeDue}, type: 'select'}
    ])
    const baseFieldsCuivre = [{
        label: 'Gest. cuivre',
        name: 'managed',
        type: 'boolean'
    }]

    const typeOptions = [
        {text: 'à la livraison', value: 'à la livraison'},
        {text: 'mensuel', value: 'mensuel'},
        {text: 'semestriel', value: 'semestriel'}
    ]
    const conditionnedFieldsCuivre = [{
        label: 'Indice du cuivre',
        name: 'copperIndex ',
        type: 'number'
    }, {
        label: 'Date de l\'indice',
        name: 'last',
        type: 'date'
    }, {
        label: 'Périodicité',
        name: 'type',
        options: {
            label: value =>
                typeOptions.find(option => option.type === value)?.text ?? null,
            options: typeOptions
        },
        type: 'select'
    }]
    // const fieldsCuivre = computed(() => [
    //     {label: 'Gest. cuivre', name: 'managed', type: 'boolean'},
    //     {label: 'CopperIndex', name: 'copperIndex ', type: 'number'},
    //     {label: 'CopperType', name: 'copperType', type: 'text'},
    //     {label: 'Date du prochain indice', name: 'next', type: 'date'},
    //     {label: 'Date du dernier indice', name: 'last', type: 'date'},
    //     {label: 'type', name: 'type', type: 'text'}
    // ])
    const cuivreData = ref({
        managed: false
    })
    const fieldsCuivre = computed(() => {
        if (cuivreData.value.managed) {
            return baseFieldsCuivre.concat(conditionnedFieldsCuivre)
        }
        return baseFieldsCuivre
    })
    const generalData = ref({})
    const comptabilityData = {}

    function generalForm(value) {
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(generalData.value, key)) {
            if (typeof value[key] === 'object') {
                if (typeof value[key].value !== 'undefined') {
                    const inputValue = parseFloat(value[key].value)
                    generalData.value[key] = {...generalData.value[key], value: inputValue}
                }
                if (typeof value[key].code !== 'undefined') {
                    const inputCode = value[key].code
                    generalData.value[key] = {...generalData.value[key], code: inputCode}
                }
            } else {
                generalData.value[key] = value[key]
            }
        } else {
            generalData.value[key] = value[key]
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
    let copperKey = 0
    function cuivreForm(value) {
        console.log('cuivreForm value', value)
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(cuivreData.value, key)) {
            if (typeof value[key] === 'object') {
                if (typeof value[key].value !== 'undefined') {
                    const inputValue = parseFloat(value[key].value)
                    cuivreData.value[key] = {...cuivreData.value[key], value: inputValue}
                }
                if (typeof value[key].code !== 'undefined') {
                    const inputCode = value[key].code
                    cuivreData.value[key] = {...cuivreData.value[key], code: inputCode}
                }
            } else {
                cuivreData.value[key] = value[key]
            }
        } else {
            cuivreData.value[key] = value[key]
        }
        if (key === 'managed' && !value[key]) {
            cuivreData.value.copperIndex = null
            cuivreData.value.copperType = null
            cuivreData.value.next = null
            cuivreData.value.last = null
            cuivreData.value.type = null
            cuivreData.value.managed = false
        }
        if (key === 'managed' && value[key]) {
            cuivreData.value.copperIndex = 0
            cuivreData.value.copperType = ''
            cuivreData.value.next = null
            cuivreData.value.last = null
            cuivreData.value.type = ''
            cuivreData.value.managed = true
        }
        copperKey += 1
    }
    const customerData = ref({
        address: null,
        copper: {
            index: {
                code: '',
                value: 0
            },
            managed: false
        },
        currency: null,
        name: '',
        paymentTerms: '/api/invoice-time-dues/1',
        society: null
    })

    async function customerFormCreate(){
        if (typeof generalData.value.address !== 'undefined') {
            customerData.value.address = {
                address: generalData.address,
                address2: generalData.address2,
                city: generalData.city,
                country: generalData.country,
                email: generalData.email,
                phoneNumber: generalData.phoneNumber,
                zipCode: generalData.zipCode
            }
        }
        if (typeof generalData.society !== 'undefined') {
            customerData.value.society = generalData.society
        }
        if (typeof comptabilityData.currency !== 'undefined') {
            customerData.value.currency = comptabilityData.currency
        }
        if (typeof generalData.name !== 'undefined') {
            customerData.value.name = generalData.name
        }
        if (typeof generalData.paymentTerms !== 'undefined') {
            customerData.value.paymentTerms = generalData.paymentTerms
        }
        if (typeof cuivreData.value !== 'undefined') {
            if (typeof cuivreData.value.managed !== 'undefined') {
                customerData.value.copper.managed = cuivreData.value.managed
            }
            if (typeof cuivreData.value.copperIndex !== 'undefined') {
                customerData.value.copper.index.value = cuivreData.value.copperIndex
            }
            if (typeof cuivreData.value.copperType !== 'undefined') {
                customerData.value.copper.index.code = cuivreData.value.copperType
            }
            if (typeof cuivreData.value.next !== 'undefined') {
                customerData.value.copper.next = cuivreData.value.next
            }
            if (typeof cuivreData.value.last !== 'undefined') {
                customerData.value.copper.last = cuivreData.value.last
            }
        }
        try {
            await storeCustomersList.addCustomer(customerData.value)
            isPopupVisible.value = false
            isCreatedPopupVisible.value = true
            success = 'client crée'
            emits('created')
            // Remise à zéro des données
            customerData.value = {
                address: null,
                copper: {
                    index: {
                        code: '',
                        value: 0
                    },
                    managed: false
                },
                currency: null,
                name: '',
                paymentTerms: '/api/invoice-time-dues/1',
                society: null
            }
            generalData = {}
            comptabilityData = {}
            cuivreData.value = {
                managed: false
            }
        } catch (error) {
            violations.value = error
            isPopupVisible.value = true
            isCreatedPopupVisible.value = false
            console.log('violations', violations.value)
        }
    }
</script>

<template>
    <AppModal :id="modalId" class="four" :title="title">
        <AppTabs id="gui-start" class="gui-start-content">
            <AppTab id="gui-start-general" active icon="sitemap" title="Général">
                <AppFormJS id="supplier" :fields="fields" :violations="violations" @update:model-value="generalForm"/>
            </AppTab>
            <AppTab id="gui-start-comptabilite" icon="chart-line" title="Comptabilité">
                <AppFormJS id="comptabilite" :fields="fieldsComp" :violations="violations" @update:model-value="comptabilityForm"/>
            </AppTab>
            <AppTab id="gui-start-cuivre" icon="clipboard-list" title="Cuivre">
                <AppFormJS id="cuivre" :key="copperKey" :fields="fieldsCuivre" :violations="violations" :model-value="cuivreData" @update:model-value="cuivreForm"/>
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
.tab-pane {
    padding: 10px;
}
</style>
