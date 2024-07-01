<script setup>
    import api from '../../../api'
    import AppRowsTablePage from './AppRowsTablePage.vue'
    import {computed, ref, watchEffect} from 'vue'
    import AppSuspense from '../../../components/AppSuspense.vue'
    import useOptions from '../../../stores/option/options'
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
        customer: {
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

    //region fetch options
    const storeUnits = useOptions('units')
    const optionsUnits = computed(() => storeUnits.getOptionsMap())
    storeUnits.fetchOp()

    const storeCurrencies = useOptions('currencies')
    const currenciesOptions = computed(() => storeCurrencies.getOptionsMap())
    storeCurrencies.fetchOp()

    const fetchIncotermsOptions = useOptions('incoterms')
    await fetchIncotermsOptions.fetchOp()
    const incotermsOptions = fetchIncotermsOptions.options.map(op => {
        const text = op.text
        const value = op['@id']
        return {text, value}
    })
    //endregion
    // Si supplier et component sont nulls, on affiche une erreur
    const inputError = ref(false)
    const errorMessage = ref('')

    const component = ref(null)
    const supplier = ref(null)
    const product = ref(null)
    const customer = ref(null)

    const mainItems1 = ref([])
    const mainItems2 = ref([])

    const fetchCriteria1 = useFetchCriteria('table1Criteria')
    const fetchCriteria2 = useFetchCriteria('table2Criteria')

    const componentId = ref(0)
    const mainTitle = ref("Grille de prix")
    const apis = ref([])
    //region chargement des inputs
    if (props.component) {
        component.value = await api(props.component, 'GET')
        // console.log('Chargement du composant', component.value)
        mainTitle.value += ' pour ' + component.value.code
    }
    if (props.supplier) {
        supplier.value = await api(props.supplier, 'GET')
        // console.log('Chargement du fournisseur', supplier.value)
        mainTitle.value += ' pour ' + supplier.value.name
    }
    if (props.product) {
        product.value = await api(props.product, 'GET')
        // console.log('Chargement du produit', product.value)
        mainTitle.value += ' pour ' + product.value.name
    }
    if (props.customer) {
        customer.value = await api(props.customer, 'GET')
        // console.log('Chargement du client', customer.value)
        mainTitle.value += ' pour ' + customer.value.name
    }
    // console.log('props', props)
    // console.log('component', component.value)
    // console.log('supplier', supplier.value)
    // console.log('product', product.value)
    // console.log('customer', customer.value)
    //endregion
    //region controle des inputs et génération des erreurs correspondantes
    if (supplier.value !== null && customer.value !== null) {
        inputError.value = true
        errorMessage.value = 'Le contexte Client/Fournisseur de la grille de prix est invalide car les 2 sont renseignés'
    }
    if (component.value !== null && product.value !== null) {
        inputError.value = true
        errorMessage.value = 'Le contexte Produit/Composant de la grille de prix est invalide car les 2 sont renseignés'
    }
    //endregion
    // Liste des champs communs à toutes les grilles de prix (partie principale)
    const commonFields = [
        {
            label: 'Proportion',
            name: 'proportion',
            type: 'number',
            width: '70'
        },
        {
            label: 'Délai',
            name: 'deliveryTime',
            width: '100',
            measure: {
                code: {
                    label: 'Code',
                    name: 'deliveryTime.code',
                    options: {
                        label: value => storeUnits.getLabelFromValue(value),
                        options: optionsUnits.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'deliveryTime.value',
                    type: 'number',
                    step: 0.1
                }
            },
            type: 'measure'
        },
        {
            label: 'Moq',
            name: 'moq',
            width: '100',
            measure: {
                code: {
                    label: 'Code',
                    name: 'moq.code',
                    options: {
                        label: value => storeUnits.getLabelFromValue(value),
                        options: optionsUnits.value
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
            type: 'measure'
        },
        {
            label: 'Poids cu',
            name: 'copperWeight',
            width: '100',
            measure: {
                code: {
                    label: 'Code',
                    name: 'copperWeight.code',
                    options: {
                        label: value => storeUnits.getLabelFromValue(value),
                        options: optionsUnits.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'copperWeight.value',
                    type: 'number',
                    step: 0.1
                }
            },
            type: 'measure'
        },
        {
            label: 'Référence',
            name: 'reference',
            type: 'text',
            width: '100'
        },
        {
            label: 'Indice',
            name: 'index',
            type: 'text',
            width: '50'
        },
        {
            label: 'incoterms',
            name: 'incoterms',
            width: '150',
            options: {
                label: value =>
                    incotermsOptions.find(option => option.value === value)?.text ?? null,
                options: incotermsOptions
            },
            type: 'select'
        },
        {
            label: 'packaging',
            name: 'packaging',
            width: '100',
            measure: {
                code: {
                    label: 'Code',
                    name: 'packaging.code',
                    options: {
                        label: value => storeUnits.getLabelFromValue(value),
                        options: optionsUnits.value
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
            type: 'measure'
        },
        {
            label: 'packagingKind',
            name: 'packagingKind',
            type: 'text',
            width: '70'
        },
        {
            children: [
                {
                    label: '€',
                    name: 'price',
                    type: 'number',
                    width: '150'
                },
                {

                    label: 'Q',
                    name: 'quantite',
                    type: 'number',
                    width: '150'
                },
                {
                    label: 'ref',
                    name: 'ref',
                    type: 'text',
                }
            ],
            label: 'Prix',
            name: 'prices',
            type: 'text'
        }
    ]
    const componentFields = [
        {
            label: 'Composant',
            name: 'component',
            type: 'multiselect-fetch',
            api: '/api/components',
            filteredProperty: 'code',
            width: '100'
        }
    ]
    const supplierFields = [
        {
            label: 'Fournisseur',
            name: 'supplier',
            type: 'multiselect-fetch',
            api: '/api/suppliers',
            filteredProperty: 'name',
            width: '150'
        }
    ]
    const customerFields = [
        {
            label: 'Client',
            name: 'customer',
            type: 'multiselect-fetch',
            api: '/api/customers',
            filteredProperty: 'name',
            width: '150'
        }
    ]
    const productFields = [
        {
            label: 'Produit',
            name: 'product',
            type: 'multiselect-fetch',
            api: '/api/products',
            filteredProperty: 'name',
            width: '150'
        }
    ]
    // en fonction des données passées en props, on ajoute les champs correspondants dans les propriétés mainFields et pricesFields des tableaux 1 et 2
    const fieldsMain1 = ref([])
    const fieldsMain2 = ref([])
    const showTable2 = ref(false)
    const title1 = ref('')
    const title2 = ref('')

    if (customer.value !== null) { // Si on a un client [B]
        fetchCriteria1.addFilter('customer', props.customer)
        fetchCriteria2.addFilter('customer', props.customer)
        if (component.value === null) { // Et si aucun composant n'est renseigné, alors on affiche la grille de prix produit [4]
            fieldsMain1.value = [
                ...customerFields,
                ...productFields,
                ...commonFields
            ]
            title1.value = 'Tableau des prix Client - Produits'
            apis.value[0] = {
                main: '/api/customer-products',
                prices: '/api/customer-product-prices'
            }
            if (product.value === null) { // Et si aucun produit n'est renseigné, alors on affiche la grille de prix composant [3]
                fieldsMain2.value = [
                    ...customerFields,
                    ...componentFields,
                    ...commonFields
                ]
                title2.value = 'Tableau des prix Client - Composants'
                apis.value[1] = {
                    main: '/api/customer-components',
                    prices: '/api/customer-component-prices'
                }
                showTable2.value = true
            } else {
                fetchCriteria1.addFilter('product', props.product)
                fieldsMain2.value = []
                showTable2.value = false
            }
        } else { // Sinon, on affiche la grille de prix composant [3]
            fetchCriteria1.addFilter('component', props.component)
            fieldsMain1.value = [
                ...customerFields,
                ...componentFields,
                ...commonFields
            ]
            title1.value = 'Tableau des prix Client - Composants'
            apis.value[0] = {
                main: '/api/customer-components',
                prices: '/api/customer-component-prices'
            }
            showTable2.value = false
        }
    } else if (supplier.value !== null) { // Sinon si on a un fournisseur de renseigné [A]
        fetchCriteria1.addFilter('supplier', props.supplier)
        fetchCriteria2.addFilter('supplier', props.supplier)
        if (product.value === null) { // Et si aucun produit n'est renseigné, alors on affiche la grille de prix composant [1]
            fieldsMain1.value = [
                ...supplierFields,
                ...componentFields,
                ...commonFields
            ]
            title1.value = 'Tableau des prix Fournisseur - Composants'
            apis.value[0] ={
                main: '/api/supplier-components',
                prices: '/api/supplier-component-prices'
            }
            if (component.value === null) { // Et si aucun composant n'est renseigné, alors on affiche la grille de prix produit [2]
                fieldsMain2.value = [
                    ...supplierFields,
                    ...productFields,
                    ...commonFields
                ]
                title2.value = 'Tableau des prix Fournisseur - Produits'
                apis.value[1] = {
                    main: '/api/supplier-products',
                    prices: '/api/supplier-product-prices'
                }
                showTable2.value = true
            } else {
                fetchCriteria1.addFilter('component', props.component)
                fieldsMain2.value = []
                showTable2.value = false
            }
        } else { // Sinon, on affiche la grille de prix produit [2]
            fetchCriteria1.addFilter('product', props.product)
            fieldsMain1.value = [
                ...supplierFields,
                ...productFields,
                ...commonFields
            ]
            title1.value = 'Tableau des prix Fournisseur - Produits'
            apis.value[0] = {
                main: '/api/supplier-products',
                prices: '/api/supplier-product-prices'
            }
            showTable2.value = false
        }
    } else if (component.value !== null) { // Uniquement un composant de renseigné [C]
        fetchCriteria1.addFilter('component', props.component)
        fetchCriteria2.addFilter('component', props.component)
        fieldsMain1.value = [
            ...componentFields,
            ...supplierFields,
            ...commonFields
        ]
        title1.value = 'Tableau des prix Composant - Fournisseurs'
        apis.value[0] ={
            main: '/api/supplier-components',
            prices: '/api/supplier-component-prices'
        }
        fieldsMain2.value = [
            ...componentFields,
            ...customerFields,
            ...commonFields
        ]
        title2.value = 'Tableau des prix Composant - Clients'
        apis.value[1] = {
            main: '/api/customer-components',
            prices: '/api/customer-component-prices'
        }
        showTable2.value = true
    } else { // Uniquement un produit est renseigné [D]
        fetchCriteria1.addFilter('product', props.product)
        fetchCriteria2.addFilter('product', props.product)
        fieldsMain1.value = [
            ...productFields,
            ...customerFields,
            ...commonFields
        ]
        title1.value = 'Tableau des prix Produit - Clients'
        apis.value[0] = {
            main: '/api/customer-products',
            prices: '/api/customer-product-prices'
        }
        fieldsMain2.value = [
            ...productFields,
            ...supplierFields,
            ...commonFields
        ]
        title2.value = 'Tableau des prix Produit - Fournisseurs'
        apis.value[1] = {
            main: '/api/supplier-products',
            prices: '/api/supplier-product-prices'
        }

    }
    console.log('title1', title1.value)
    console.log('title2', title2.value)
    console.log('apis', apis.value)
    // Chargement des données

    function transformPricesAsAnArray(item) {
        if (typeof item.prices === 'object') {
            item.prices = Object.values(item.prices)
        }
        return item
    }
    async function loadData() {
        const response1 = await api(apis.value[0].main + fetchCriteria1.getFetchCriteria, 'GET')
        mainItems1.value = response1['hydra:member'].map(item => transformPricesAsAnArray(item))
        const response2 = await api(apis.value[1].main + fetchCriteria2.getFetchCriteria, 'GET')
        mainItems2.value = response2['hydra:member'].map(item => transformPricesAsAnArray(item))
    }
    await loadData()
    console.log('mainItems1', mainItems1.value)
    console.log('mainItems2', mainItems2.value)

    const fieldsPrices = [
        {
            label: '€',
            name: 'price',
            measure: {
                code: {
                    label: 'Code',
                    name: 'price.code',
                    options: {
                        label: value => storeCurrencies.getLabelFromCode(value),
                        options: currenciesOptions.value
                    },
                    type: 'select',
                    disabled: true
                },
                value: {
                    label: 'Valeur',
                    name: 'price.value',
                    type: 'number',
                    step: 0.1
                }
            },
            type: 'measure'
        },
        {
            label: 'Q',
            name: 'quantity',
            measure: {
                code: {
                    label: 'Code',
                    name: 'quantity.code',
                    options: {
                        label: value => storeUnits.getLabelFromValue(value),
                        options: optionsUnits.value
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
            type: 'measure'
        },
        {
            label: 'ref',
            name: 'ref',
            type: 'text',
        }
    ]

    async function transformItems(items) {
        return await Promise.all(items.map(async item => {
            console.log('item', item)
            const foundUnitDelai = optionsUnits.value.find(unit => unit.text === item.deliveryTime.code)
            const foundUnitMoq = optionsUnits.value.find(unit => unit.text === item.moq.code)
            const foundUnitPackaging = optionsUnits.value.find(unit => unit.text === item.packaging.code)
            const foundUnitPoidsCu = optionsUnits.value.find(unit => unit.text === item.copperWeight.code)
            //On charge les prix via l'API en utilisant le champ '@id' de l'item de prix
            const promises = item.prices.map(price => {
                return api(price['@id'], 'GET')
            })
            const values = await Promise.all(promises)
            console.log('values', values)
            // On attend le retour de toutes les promesses
            const transformedPrices = values.map(price => {
                    const foundUnitQuantity = optionsUnits.value.find(unit => unit.text === price.quantity.code)
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
                deliveryTime: {value: item.deliveryTime.value, code: foundUnitDelai ? foundUnitDelai.value : item.deliveryTime.code},
                moq: {value: item.moq.value, code: foundUnitMoq ? foundUnitMoq.value : item.moq.code},
                packaging: {value: item.packaging.value, code: foundUnitPackaging ? foundUnitPackaging.value : item.packaging.code},
                copperWeight: {value: item.copperWeight.value, code: foundUnitPoidsCu ? foundUnitPoidsCu.value : item.copperWeight.code},
                prices: transformedPrices
            }
        }))
    }

    const resolvedItems1 = ref([])
    const resolvedItems2 = ref([])

    watchEffect(async () => {
        resolvedItems1.value = await transformItems(mainItems1.value)
    })

    watchEffect(async () => {
        resolvedItems2.value = await transformItems(mainItems2.value)
    })
    async function addItem(formData) {
        console.log('addItem formData', formData)
        const component = props.component
    }
    async function annuleUpdated() {
        console.log('annuleUpdated')
    }
    async function updateItems(item) {
        console.log('updateItems', item)
    }
    async function addItemPrice(formData) {
        console.log('addItemPrice formData', formData)
    }
    async function updateItemsPrices(item) {
        console.log('updateItemsPrices', item)
    }
    async function deleted(id){
        if (window.confirm('Voulez-vous vraiment supprimer cet élément ?') === false) return
        await api(id, 'DELETE')
        await loadData()
    }
    async function deletedPrices(id){
        if (window.confirm('Voulez-vous vraiment supprimer cet élément ?') === false) return
        await api(id, 'DELETE')
        await loadData()
    }
</script>

<template>
    <AppSuspense>
        <h1>{{ mainTitle}}</h1>
        <div
            v-if="!inputError">
            <AppRowsTablePage
                :main-fields="fieldsMain1"
                :price-fields="fieldsPrices"
                :items="resolvedItems1"
                :title="title1"
                @add-item="addItem"
                @add-item-price="addItemPrice"
                @deleted="deleted"
                @deleted-prices="deletedPrices"
                @annule-update="annuleUpdated"
                @update-items="updateItems"
                @update-items-prices="updateItemsPrices"/>
            <AppRowsTablePage
                v-if="showTable2"
                :main-fields="fieldsMain2"
                :price-fields="fieldsPrices"
                :items="resolvedItems2"
                :title="title2"
                @add-item="addItem"
                @add-item-price="addItemPrice"
                @deleted="deleted"
                @deleted-prices="deletedPrices"
                @annule-update="annuleUpdated"
                @update-items="updateItems"
                @update-items-prices="updateItemsPrices"/>
        </div>
        <div v-else class="bg-danger text-white text-center m-5 p-5">{{ errorMessage }}</div>
    </AppSuspense>
</template>
