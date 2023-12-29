<script setup>
    import {computed, ref} from 'vue-demi'
    import AppFormJS from '../../../components/form/AppFormJS.js'
    import {useCustomerProductStore} from '../../../stores/customer/customerProduct'
    import {useCustomersStore} from '../../../stores/customer/customers'
    import {useProductStore} from '../../../stores/project/product/products'
    import AppTab from '../../../components/tabs/AppTab.vue'
    import AppTabs from '../../../components/tabs/AppTabs.vue'
    import useOptions from '../../../stores/option/options'

    const props = defineProps({
        title: {required: true, type: String},
        target: {required: true, type: String},
        modalId: {required: true, type: String},
        optionsProductFamilies: {required: true, type: Array}
    })
    const storeProductsList = useProductStore()
    const storeCustomerProductList = useCustomerProductStore()
    const storeCustomerList = useCustomersStore()
    await storeCustomerList.fetch()
    const optionCustomers = computed(() => storeCustomerList.optionCustomers)
    let violations = []
    let success = []
    const isPopupVisible = ref(false)
    const isCreatedPopupVisible = ref(false)

    const options = [
        {text: 'Prototype', value: 'Prototype'},
        {text: 'EI', value: 'EI'},
        {text: 'Série', value: 'Série'},
        {text: 'Piéce de rechange', value: 'Piéce de rechange'}
    ]

    const fecthOptionsUnits = useOptions('units')
    await fecthOptionsUnits.fetchOp()
    const optionsUnits = computed(() =>
        fecthOptionsUnits.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))

    const generalFields = computed(() => [
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Réf', name: 'code', type: 'text'},
        {label: 'Date d\'expiration', name: 'endOfLife', type: 'date'},
        {
            label: 'Famille de Produit',
            name: 'family',
            options: {
                label: value =>
                    props.optionsProductFamilies.find(option => option.value === value)?.text ?? null,
                options: props.optionsProductFamilies
            },
            type: 'select'
        },
        {
            label: 'Volume annuel prévisionnel ',
            name: 'forecastVolume',
            options: {
                label: value =>
                    optionsUnits.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnits.value
            },
            type: 'measureSelect'
        },
        {label: 'id', name: 'index', type: 'text'},
        {
            label: 'Type de Produit',
            name: 'kind',
            options: {
                label: value =>
                    options.find(option => option.value === value)?.text ?? null,
                options
            },
            type: 'select'
        },
        {label: 'Infos publiques', name: 'notes', type: 'text'},
        {
            label: 'Conditionnement',
            name: 'packaging',
            options: {
                label: value =>
                    optionsUnits.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnits.value
            },
            type: 'measureSelect'
        },
        {label: 'Conditionnement (type)', name: 'packagingKind', type: 'text'},
        {
            label: 'Unité',
            name: 'unit',
            options: {
                label: value =>
                    optionsUnits.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnits.value
            },
            type: 'select'
        }
    ])

    const clientFields = computed(() => [
        {
            label: 'Client principal',
            name: 'client',
            options: {
                label: value =>
                    optionCustomers.value.find(option => option.value === value)?.text ?? null,
                options: optionCustomers.value
            },
            type: 'multiselect'
        }
    ])

    const generalData = {}
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
    const customerData = {}
    function customerForm(value) {
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(customerData, key)) {
            if (typeof value[key] === 'object') {
                if (typeof value[key].value !== 'undefined') {
                    const inputValue = parseFloat(value[key].value)
                    customerData[key] = {...customerData[key], value: inputValue}
                }
                if (typeof value[key].code !== 'undefined') {
                    const inputCode = value[key].code
                    customerData[key] = {...customerData[key], code: inputCode}
                }
                if (Array.isArray(value[key])) {
                    customerData[key] = value[key]
                }
            } else {
                customerData[key] = value[key]
            }
        } else {
            customerData[key] = value[key]
        }
    }

    async function productFormCreate(){
        try {
            const product = {
                code: generalData?.code || '',
                endOfLife: generalData?.endOfLife || '',
                family: generalData?.family || '',
                forecastVolume: {
                    // code: generalData?.forecastVolume.code || '',
                    code: 'U',
                    value: parseFloat(generalData?.forecastVolume.value || '0')
                },
                index: generalData?.index || '',
                kind: generalData?.kind || '',
                name: generalData?.name || '',
                notes: generalData?.notes || '',
                packaging: {
                    // code: generalData?.packaging.code || '',
                    code: 'U',
                    value: parseFloat(generalData?.packaging.value || '0')
                },
                packagingKind: generalData?.packagingKind || '',
                unit: generalData?.unit || ''
            }
            await storeProductsList.addProduct(product)
            isPopupVisible.value = false
            isCreatedPopupVisible.value = true
            success = 'Produit crée'

            const customerProduct = {
                customer: customerData?.client || '',
                product: storeProductsList.currentProduct
            }
            await storeCustomerProductList.addCustomerProduct(customerProduct)
            window.location.reload()
        } catch (error) {
            violations = error
            isPopupVisible.value = true
            isCreatedPopupVisible.value = false
        }
    }
</script>

<template>
    <AppModal :id="modalId" :title="title">
        <AppTabs id="gui-start" class="gui-start-content">
            <AppTab id="gui-start-general" class="css-tab" active icon="sitemap" title="Général">
                <AppFormJS id="general" :fields="generalFields" @update:model-value="generalForm"/>
            </AppTab>
            <AppTab id="gui-start-customer" class="css-tab" icon="chart-line" title="Clients">
                <AppFormJS id="client" :fields="clientFields" @update:model-value="customerForm"/>
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
                @click="productFormCreate">
                Créer
            </AppBtn>
        </template>
    </AppModal>
</template>

<style>
.css-tab {
    min-height: 250px;
}
</style>
