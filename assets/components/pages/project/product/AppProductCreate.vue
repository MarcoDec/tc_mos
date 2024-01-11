<script setup>
    import {computed, ref} from 'vue'
    import AppFormJS from '../../../form/AppFormJS.js'
    import {useCustomerProductStore} from '../../../../stores/customer/customerProduct'
    import {useCustomersStore} from '../../../../stores/customer/customers'
    import {useProductStore} from '../../../../stores/project/product/products'
    import AppTab from '../../../tab/AppTab.vue'
    import AppTabs from '../../../tab/AppTabs.vue'
    import useOptions from '../../../../stores/option/options'

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

    //region options
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

    const fecthOptionsIncoterms = useOptions('incoterms')
    await fecthOptionsIncoterms.fetchOp()
    const optionsIncoterms = computed(() =>
        fecthOptionsIncoterms.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))
    const fecthOptionsCompanies = useOptions('companies')
    await fecthOptionsCompanies.fetchOp()
    const optionsCompanies = computed(() =>
        fecthOptionsCompanies.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))

    //endregion
    //region fields
    const logisticFields = computed(() => [
        {
            label: 'Incoterm',
            name: 'incoterm',
            options: {
                label: value =>
                    optionsIncoterms.value.find(option => option.type === value)?.text ?? null,
                options: optionsIncoterms.value
            },
            type: 'select'
        },
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
            label: 'Minimum Livraison',
            name: 'minDelivery',
            options: {
                label: value =>
                    optionsUnits.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnits.value
            },
            type: 'measureSelect'
        },
        {
            label: 'Poids',
            name: 'weight',
            options: {
                label: value =>
                    optionsUnits.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnits.value
            },
            type: 'measureSelect'
        }
    ])

    const generalFields = computed(() => [
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
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Réf', name: 'code', type: 'text'},
        {label: 'Indice', name: 'index', type: 'text'},
        {
            label: 'Unité',
            name: 'unit',
            options: {
                label: value =>
                    optionsUnits.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnits.value
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
        {label: 'Date d\'expiration', name: 'endOfLife', type: 'date'},
        {label: 'Note', name: 'notes', type: 'textarea'}
    ])
    const clientFields = computed(() => [
        {
            label: 'Clients',
            name: 'client',
            options: {
                label: value =>
                    optionCustomers.value.find(option => option.value === value)?.text ?? null,
                options: optionCustomers.value
            },
            type: 'multiselect'
        }
    ])
    const manufacturingCompanyFields = computed(() => [
        {
            label: 'Compagnies Fournisseurs de ce produit',
            name: 'companies',
            options: {
                label: value =>
                    optionsCompanies.value.find(option => option.value === value)?.text ?? null,
                options: optionsCompanies.value
            },
            type: 'multiselect'
        },
        {
            label: 'Minimum Production Série',
            name: 'minProd',
            options: {
                label: value =>
                    optionsUnits.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnits.value
            },
            type: 'measureSelect'
        },
        {
            label: 'Max Production Prototype',
            name: 'maxProto',
            options: {
                label: value =>
                    optionsUnits.value.find(option => option.type === value)?.text ?? null,
                options: optionsUnits.value
            },
            type: 'measureSelect'
        }
    ])
    //endregion
    //region data
    const generalData = ref({
        code: '',
        endOfLife: '',
        family: '',
        forecastVolume: {
            code: '/api/units/1',
            value: 0
        },
        index: '',
        kind: 'Série',
        name: '',
        notes: '',
        unit: '/api/units/1'
    })
    const customerData = ref([])
    const manufacturingCompanyData = ref({
        companies: [],
        minProd: {
            code: '/api/units/1',
            value: 0
        },
        maxProto: {
            code: '/api/units/1',
            value: 0
        }
    })
    const logisticData = ref({
        incoterm: '',
        packaging: {
            code: '/api/units/1',
            value: 0
        },
        packagingKind: '',
        weight: {
            code: '/api/units/5',
            value: 0
        },
        minDelivery: {
            code: '/api/units/1',
            value: 0
        }
    })
    //endregion
    // region functions
    function generalForm(value){
        //console.log('update General Form', value)
        Object.keys(value).forEach(key => {
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
        })
        //console.log('generalData', generalData.value)
    }
    function customerForm(value){
        //console.log('update Customer Form', value)
        const key = Object.keys(value)[0]
        //console.log('key', key)
        if (Object.prototype.hasOwnProperty.call(customerData.value, key)) {
            if (typeof value[key] === 'object') {
                if (typeof value[key].value !== 'undefined') {
                    const inputValue = parseFloat(value[key].value)
                    customerData.value[key] = {...customerData.value[key], value: inputValue}
                }
                if (typeof value[key].code !== 'undefined') {
                    const inputCode = value[key].code
                    customerData.value[key] = {...customerData.value[key], code: inputCode}
                }
                if (Array.isArray(value[key])) {
                    customerData.value[key] = value[key]
                }
            } else {
                customerData.value[key] = value[key]
            }
        } else {
            customerData.value[key] = value[key]
        }
    }
    function logisticForm(value){
        Object.keys(value).forEach(key => {
            if (Object.prototype.hasOwnProperty.call(logisticData.value, key)) {
                if (typeof value[key] === 'object') {
                    if (typeof value[key].value !== 'undefined') {
                        const inputValue = parseFloat(value[key].value)
                        logisticData.value[key] = {...logisticData.value[key], value: inputValue}
                    }
                    if (typeof value[key].code !== 'undefined') {
                        const inputCode = value[key].code
                        logisticData.value[key] = {...logisticData.value[key], code: inputCode}
                    }
                    if (Array.isArray(value[key])) {
                        logisticData.value[key] = value[key]
                    }
                } else {
                    logisticData.value[key] = value[key]
                }
            } else {
                logisticData.value[key] = value[key]
            }
        })
    }
    function manufacturingCompanyForm(value){
        Object.keys(value).forEach(key => {
            if (Object.prototype.hasOwnProperty.call(manufacturingCompanyData.value, key)) {
                if (typeof value[key] === 'object') {
                    if (typeof value[key].value !== 'undefined') {
                        const inputValue = parseFloat(value[key].value)
                        manufacturingCompanyData.value[key] = {
                            ...manufacturingCompanyData.value[key],
                            value: inputValue
                        }
                    }
                    if (typeof value[key].code !== 'undefined') {
                        const inputCode = value[key].code
                        manufacturingCompanyData.value[key] = {...manufacturingCompanyData.value[key], code: inputCode}
                    }
                    if (Array.isArray(value[key])) {
                        manufacturingCompanyData.value[key] = value[key]
                    }
                } else {
                    manufacturingCompanyData.value[key] = value[key]
                }
            } else {
                manufacturingCompanyData.value[key] = value[key]
            }
        })
    }

    async function productFormCreate(){
        try {
            const product = {
                code: generalData.value?.code || '',
                endOfLife: generalData.value?.endOfLife || '',
                family: generalData.value?.family || '',
                forecastVolume: typeof generalData.value.forecastVolume === 'undefined'
                    ? {
                        code: 'U',
                        value: 0
                    } : {
                        code: optionsUnits.value.find(option => option.value === generalData.value.forecastVolume.code).text,
                        value: parseFloat(generalData.value.forecastVolume.value)
                    },
                incoterm: logisticData.value?.incoterm || '',
                index: generalData.value?.index || '',
                kind: generalData.value?.kind || '',
                maxProto: typeof manufacturingCompanyData.value.maxProto === 'undefined'
                    ? {
                        code: 'U',
                        value: 0
                    } : {
                        code: optionsUnits.value.find(option => option.value === manufacturingCompanyData.value.maxProto.code).text,
                        value: parseFloat(manufacturingCompanyData.value.maxProto.value)
                    },
                minDelivery: typeof logisticData.value.minDelivery === 'undefined'
                    ? {
                        code: 'U',
                        value: 0
                    } : {
                        code: optionsUnits.value.find(option => option.value === logisticData.value.minDelivery.code).text,
                        value: parseFloat(logisticData.value.minDelivery.value)
                    },
                minProd: typeof manufacturingCompanyData.value.minProd === 'undefined'
                    ? {
                        code: 'U',
                        value: 0
                    } : {
                        code: optionsUnits.value.find(option => option.value === manufacturingCompanyData.value.minProd.code).text,
                        value: parseFloat(manufacturingCompanyData.value.minProd.value)
                    },
                name: generalData.value?.name || '',
                notes: generalData.value?.notes || '',
                packaging: typeof logisticData.value.packaging === 'undefined'
                    ? {
                        code: 'U',
                        value: 0
                    } : {
                        code: optionsUnits.value.find(option => option.value === logisticData.value.packaging.code).text,
                        value: parseFloat(logisticData.value.packaging.value)
                    },
                packagingKind: generalData.value?.packagingKind || '',
                unit: generalData.value?.unit || '',
                weight: typeof logisticData.value.weight === 'undefined'
                    ? {
                        code: 'Kg',
                        value: 0
                    } : {
                        code: optionsUnits.value.find(option => option.value === logisticData.value.weight.code).text,
                        value: parseFloat(logisticData.value.weight.value)
                    }
            }
            await storeProductsList.addProduct(product)
            isPopupVisible.value = false
            isCreatedPopupVisible.value = true
            success = 'Produit crée'
            const customerProduct = {
                customer: customerData.value?.client || '',
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
    // endregion
</script>

<template>
    <AppModal :id="modalId" :title="title">
        <AppTabs id="gui-start" class="gui-start-content">
            <AppTab id="gui-start-general" class="css-tab" active icon="fa-brands fa-product-hunt" tabs="gui-start" title="Général">
                <AppFormJS
                    id="general"
                    :fields="generalFields"
                    :model-value="generalData"
                    @update:model-value="generalForm"/>
            </AppTab>
            <AppTab id="gui-start-customer" class="css-tab" icon="user-tie" tabs="gui-start" title="Clients">
                <p class="bg-info text-white p-2">
                    Les données de chiffrage pourront être renseignées dans la fiche produit une fois créée.
                </p>
                <AppFormJS
                    id="client"
                    :fields="clientFields"
                    :model-value="customerData"
                    @update:model-value="customerForm"/>
            </AppTab>
            <AppTab id="gui-start-manufacturing-company" class="css-tab" icon="industry" tabs="gui-start" title="Fabrication">
                <p class="bg-info text-white p-2">
                    Les données de répartition de la fabrication pourront être renseignées dans la fiche produit une fois créée.
                </p>
                <AppFormJS
                    id="manufacturingCompany"
                    :fields="manufacturingCompanyFields"
                    :model-value="manufacturingCompanyData"
                    @update:model-value="manufacturingCompanyForm"/>
            </AppTab>
            <AppTab id="gui-start-logistic" class="css-tab" icon="boxes" tabs="gui-start" title="Logistique">
                <AppFormJS
                    id="logistic"
                    :fields="logisticFields"
                    :model-value="logisticData"
                    @update:model-value="logisticForm"/>
            </AppTab>
        </AppTabs>
        <ul v-if="isPopupVisible" class="alert alert-danger" role="list">
            <li>{{ violations }}</li>
        </ul>
        <ul v-if="isCreatedPopupVisible" class="alert alert-success" role="list">
            <li>{{ success }}</li>
        </ul>
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
