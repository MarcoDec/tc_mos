<script setup>
    import AppRowsTablePage from './AppRowsTablePage.vue'
    import {computed, ref} from 'vue'
    import AppSuspense from '../../../components/AppSuspense.vue'
    import useOptions from '../../../stores/option/options'
    import {useCurrenciesStore} from '../../../stores/currencies/currencies'
    import {useComponentSuppliersStore} from '../../../stores/prices/componentSuppliers'
    import {useComponentSuppliersPricesStore} from '../../../stores/prices/componentSuppliersPrices'
    import useFetchCriteria from "../../../stores/fetch-criteria/fetchCriteria"

    const props = defineProps({
        supplier: {
            type: String,
            required: false,
            default: () => null
        },
        component: {
            type: String,
            required: false,
            default: () => null
        },
        product: {
            type: String,
            required: false,
            default: () => null
        },
        title: {
            type: String,
            required: false,
            default: 'Tableau des prix des composants'
        }
    })
    // Si supplier et component sont nulls, on affiche une erreur
    const inputError = computed(() => !props.supplier && !props.component)

    function getIdFromIri(iri) {
        return iri.split('/').pop()
    }
    //region fetch options
    const fetchUnitOptions = useOptions('units')
    await fetchUnitOptions.fetchOp()
    const optionsUnit = fetchUnitOptions.getOptionsMap()

    const fetchIncotermsOptions = useOptions('incoterms')
    await fetchIncotermsOptions.fetchOp()
    const incotermsOptions = fetchIncotermsOptions.options.map(op => {
        const text = op.text
        const value = op['@id']
        return {text, value}
    })

    const storeCurrencies = useCurrenciesStore()
    await storeCurrencies.fetch()
    const currenciesOption = computed(() => storeCurrencies.currenciesOption)
    //endregion

    const fieldsComponentSuppliers = [
        {
            label: 'Fournisseur',
            name: 'supplier',
            type: 'multiselect-fetch',
            api: '/api/suppliers',
            filteredProperty: 'name'
        },
        {
            label: 'Composant',
            name: 'component',
            type: 'multiselect-fetch',
            api: '/api/components',
            filteredProperty: 'code'
        },
        {
            create: true,
            filter: true,
            label: 'Proportion',
            name: 'proportion',
            prefix: 'componentSuppliers',
            sort: false,
            type: 'number',
            update: true
        },
        {
            create: true,
            label: 'Délai',
            name: 'delai',
            prefix: 'componentSuppliers',
            measure: {
                code: {
                    label: 'Code',
                    name: 'delai.code',
                    options: {
                        label: value =>
                            optionsUnit.find(option => option.value === value)?.text ?? null,
                        options: optionsUnit
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'delai.value',
                    type: 'number',
                    step: 0.1
                }
            },
            type: 'measure',
            update: true
        },
        {
            create: false,
            label: 'Moq',
            name: 'moq',
            prefix: 'componentSuppliers',
            measure: {
                code: {
                    label: 'Code',
                    name: 'moq.code',
                    options: {
                        label: value =>
                            optionsUnit.find(option => option.value === value)?.text ?? null,
                        options: optionsUnit
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'moq.value',
                    type: 'number',
                    step: 0.1
                }
            },
            type: 'measure',
            update: true
        },
        {
            filter: true,
            label: 'Poids cu',
            name: 'poidsCu',
            prefix: 'componentSuppliers',
            measure: {
                code: {
                    label: 'Code',
                    name: 'poidsCu.code',
                    options: {
                        label: value =>
                            optionsUnit.find(option => option.value === value)?.text ?? null,
                        options: optionsUnit
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'poidsCu.value',
                    type: 'number',
                    step: 0.1
                }
            },
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Référence',
            name: 'reference',
            prefix: 'componentSuppliers',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Indice',
            name: 'indice',
            prefix: 'componentSuppliers',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: true,
            label: 'incoterms',
            name: 'incoterms',
            prefix: 'componentSuppliers',
            options: {
                label: value =>
                    incotermsOptions.find(option => option.value === value)?.text ?? null,
                options: incotermsOptions
            },
            type: 'select',
            update: true
        },
        {
            filter: true,
            label: 'packaging',
            name: 'packaging',
            prefix: 'componentSuppliers',
            measure: {
                code: {
                    label: 'Code',
                    name: 'packaging.code',
                    options: {
                        label: value =>
                            optionsUnit.find(option => option.value === value)?.text ?? null,
                        options: optionsUnit
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'packaging.value',
                    type: 'number',
                    step: 0.1
                }
            },
            type: 'measure',
            update: true
        },
        {
            create: true,
            label: 'packagingKind',
            name: 'packagingKind',
            prefix: 'componentSuppliers',
            type: 'text',
            update: true
        },
        {
            children: [
                {create: true, filter: true, label: '€', name: 'price', prefix: 'componentSupplierPrices', sort: false, type: 'number', update: true},
                {create: true, filter: true, label: 'Q', name: 'quantite', prefix: 'componentSupplierPrices', sort: false, type: 'number', update: true},
                {create: true, filter: true, label: 'ref', name: 'ref', prefix: 'componentSupplierPrices', sort: false, type: 'text', update: true}
            ],
            create: false,
            filter: true,
            label: 'Prix',
            name: 'prices',
            prefix: 'componentSuppliers',
            sort: false,
            type: 'text',
            update: true
        }
    ]
    const fieldsComponentSuppliersPrices = [
        {
            create: true,
            filter: true,
            label: '€',
            name: 'price',
            prefix: 'componentSupplierPrices',
            measure: {
                code: {
                    label: 'Code',
                    name: 'price.code',
                    options: {
                        label: value =>
                            currenciesOption.value.find(option => option.value === value)?.text ?? null,
                        options: currenciesOption.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'price.value',
                    type: 'number',
                    step: 0.1
                }
            },
            type: 'measure',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'Q',
            name: 'quantity',
            prefix: 'componentSupplierPrices',
            measure: {
                code: {
                    label: 'Code',
                    name: 'quantity.code',
                    options: {
                        label: value =>
                            optionsUnit.find(option => option.value === value)?.text ?? null,
                        options: optionsUnit
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'quantity.value',
                    type: 'number',
                    step: 0.1
                }
            },
            type: 'measure',
            update: true
        },
        {
            create: true,
            filter: true,
            label: 'ref',
            name: 'ref',
            prefix: 'componentSupplierPrices',
            sort: false,
            type: 'text',
            update: true
        }
    ]

    function transformItems(ItemsComponentSuppliers, optionsUnits, currenciesOptions) {
        return ItemsComponentSuppliers.map(item => {
            const foundUnitDelai = optionsUnits.find(unit => unit.text === item.delai.code)
            const foundUnitMoq = optionsUnits.find(unit => unit.text === item.moq.code)
            const foundUnitPackaging = optionsUnits.find(unit => unit.text === item.packaging.code)
            const foundUnitPoidsCu = optionsUnits.find(unit => unit.text === item.poidsCu.code)

            const transformedPrices = item.prices.map(price => {
                const foundUnitQuantity = optionsUnits.find(unit => unit.text === price.quantity.code)
                const foundCurrencietPrice = currenciesOptions.value.find(currencie => currencie.text === price.price.code)
                return {
                    ...price,
                    quantity: {
                        ...price.quantity,
                        code: foundUnitQuantity ? foundUnitQuantity.value : price.quantity.code
                    },
                    price: {
                        ...price.price,
                        code: foundCurrencietPrice ? foundCurrencietPrice.value : price.price.code
                    }
                }
            })

            return {
                ...item,
                delai: {value: item.delai.value, code: foundUnitDelai ? foundUnitDelai.value : item.delai.code},
                moq: {value: item.moq.value, code: foundUnitMoq ? foundUnitMoq.value : item.moq.code},
                packaging: {value: item.packaging.value, code: foundUnitPackaging ? foundUnitPackaging.value : item.packaging.code},
                poidsCu: {value: item.poidsCu.value, code: foundUnitPoidsCu ? foundUnitPoidsCu.value : item.poidsCu.code},
                prices: transformedPrices
            }
        })
    }

    const storeComponentSuppliers = useComponentSuppliersStore()
    const componentSupplierFetchCriteria = useFetchCriteria('componentSuppliers')
    const componentId = ref(0)
    const storeComponentSuppliersPrices = useComponentSuppliersPricesStore()
    const componentSuppliersItems = computed(() => storeComponentSuppliers.componentSuppliersItems)
    const localItems = computed(() => transformItems(componentSuppliersItems.value, optionsUnit, currenciesOption))
    function initializePermanentFilters() {
        if (props.component) {
            componentId.value = getIdFromIri(props.component)
            componentSupplierFetchCriteria.addFilter('component', props.component)
        }
        if (props.supplier) {
            componentSupplierFetchCriteria.addFilter('supplier', props.supplier)
        }
    }
    async function refreshTable() {
        initializePermanentFilters()
        await storeComponentSuppliers.fetch(componentSupplierFetchCriteria.getFetchCriteria)
        await storeComponentSuppliers.fetchPricesForItems()
   }
    refreshTable()

    async function addItem(formData) {
        const component = props.component
        await storeComponentSuppliers.addComponentSuppliers({formData, component})
        await refreshTable()
    }
    async function annuleUpdated() {
        await refreshTable()
    }
    async function updateItems(item) {
        await storeComponentSuppliers.updateComponentSuppliers(item)
        await refreshTable()
    }
    async function addItemPrice(formData) {
        await storeComponentSuppliersPrices.addPrices(formData)
        await refreshTable()
    }
    async function updateItemsPrices(item) {
        await storeComponentSuppliersPrices.updatePrices(item)
        await refreshTable()
    }
    async function deleted(id){
        await storeComponentSuppliers.remove(id)
        await refreshTable()
    }
    async function deletedPrices(id){
        await storeComponentSuppliersPrices.removePrice(id)
        await refreshTable()
    }
</script>

<template>
    <AppSuspense>
        <AppRowsTablePage
            v-if="!inputError"
            :fields-component-suppliers="fieldsComponentSuppliers"
            :fields-component-suppliers-prices="fieldsComponentSuppliersPrices"
            :items="localItems"
            :title="title"
            @add-item="addItem"
            @add-item-price="addItemPrice"
            @deleted="deleted"
            @deleted-prices="deletedPrices"
            @annule-update="annuleUpdated"
            @update-items="updateItems"
            @update-items-prices="updateItemsPrices"/>
        <div v-else class="bg-danger text-white text-center m-5 p-5">Impossible d'afficher une grille de prix car aucun composant et/ou aucun fournisseur n'a été sélectionné</div>
    </AppSuspense>
</template>
