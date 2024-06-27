<script setup>
    import api from '../../../api'
    import AppRowsTablePage from './AppRowsTablePage.vue'
    import {computed, ref} from 'vue'
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

    //TODO: remplacer les stores spécifiques par des fonctions génériques
    const storeComponentSuppliers = useComponentSuppliersStore()
    const storeComponentSuppliersPrices = useComponentSuppliersPricesStore()

    const mainItems1 = ref([])
    const mainItems2 = ref([])

    const mainItems = computed(() => storeComponentSuppliers.componentSuppliersItems)
    const fetchCriteria = useFetchCriteria('componentProductSuppliersCustomer')

    const componentId = ref(0)
    //region chargement des inputs
    if (props.component) {
        component.value = await api(props.component, 'GET')
        console.log('Chargement du composant', component.value)
    }
    if (props.supplier) {
        supplier.value = await api(props.supplier, 'GET')
        console.log('Chargement du fournisseur', supplier.value)
    }
    if (props.product) {
        product.value = await api(props.product, 'GET')
        console.log('Chargement du produit', product.value)
    }
    if (props.customer) {
        customer.value = await api(props.customer, 'GET')
        console.log('Chargement du client', customer.value)
    }
    console.log('props', props)
    console.log('component', component.value)
    console.log('supplier', supplier.value)
    console.log('product', product.value)
    console.log('customer', customer.value)
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
    //region génération des routes nécessaires pour l'affichage des prix
    const apis = ref([])
    if (
        (component.value !== null && customer.value === null)
        || (supplier.value !== null && product.value === null)
    ) {

    }
    if (
        (component.value !== null && supplier.value === null)
        || (customer.value !== null && product.value === null)
    ) {
    }
    if (
        (product.value !== null && customer.value === null)
        || (supplier.value !== null && component.value === null)
    ) {
    }
    if (
        (product.value !== null && supplier.value === null)
        || (customer.value !== null && component.value === null)
    ) {
    }
    console.log('apis', apis.value)
    //endregion


    function getIdFromIri(iri) {
        return iri.split('/').pop()
    }
    // Liste des champs communs à toutes les grilles de prix (partie principale)
    const commonFields = [
        {
            label: 'Proportion',
            name: 'proportion',
            type: 'number'
        },
        {
            label: 'Délai',
            name: 'delai',
            measure: {
                code: {
                    label: 'Code',
                    name: 'delai.code',
                    options: {
                        label: value => storeUnits.getLabel(value),
                        options: optionsUnits.value
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
            type: 'measure'
        },
        {
            label: 'Moq',
            name: 'moq',
            measure: {
                code: {
                    label: 'Code',
                    name: 'moq.code',
                    options: {
                        label: value => storeUnits.getLabel(value),
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
            name: 'poidsCu',
            measure: {
                code: {
                    label: 'Code',
                    name: 'poidsCu.code',
                    options: {
                        label: value => storeUnits.getLabel(value),
                        options: optionsUnits.value
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
            type: 'measure'
        },
        {
            label: 'Référence',
            name: 'reference',
            type: 'text'
        },
        {
            label: 'Indice',
            name: 'indice',
            type: 'text',
        },
        {
            label: 'incoterms',
            name: 'incoterms',
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
            measure: {
                code: {
                    label: 'Code',
                    name: 'packaging.code',
                    options: {
                        label: value => storeUnits.getLabel(value),
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
            type: 'text'
        },
        {
            children: [
                {
                    label: '€',
                    name: 'price',
                    type: 'number'
                },
                {

                    label: 'Q',
                    name: 'quantite',
                    type: 'number'
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
            filteredProperty: 'code'
        }
    ]
    const supplierFields = [
        {
            label: 'Fournisseur',
            name: 'supplier',
            type: 'multiselect-fetch',
            api: '/api/suppliers',
            filteredProperty: 'name'
        }
    ]
    const customerFields = [
        {
            label: 'Client',
            name: 'customer',
            type: 'multiselect-fetch',
            api: '/api/customers',
            filteredProperty: 'name'
        }
    ]
    const productFields = [
        {
            label: 'Produit',
            name: 'product',
            type: 'multiselect-fetch',
            api: '/api/products',
            filteredProperty: 'name'
        }
    ]
    // en fonction des données passées en props, on ajoute les champs correspondants dans les propriétés mainFields et pricesFields des tableaux 1 et 2
    const fieldsMain1 = ref([])
    const fieldsMain2 = ref([])
    const showTable2 = ref(false)
    const title1 = ref('')
    const title2 = ref('')
    if (customer.value !== null) { // Si on a un client [B]
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
                fieldsMain2.value = []
                showTable2.value = false
            }
        } else { // Sinon, on affiche la grille de prix composant [3]
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
                fieldsMain2.value = []
                showTable2.value = false
            }
        } else { // Sinon, on affiche la grille de prix produit [2]
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
    const fieldsPrices = [
        {
            label: '€',
            name: 'price',
            measure: {
                code: {
                    label: 'Code',
                    name: 'price.code',
                    options: {
                        label: value => storeCurrencies.getLabel(value),
                        options: currenciesOptions.value
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
                        label: value => storeUnits.getLabel(value),
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

    console.log('fieldsMain1', fieldsMain1.value)
    console.log('fieldsMain2', fieldsMain2.value)
    function transformItems(items) {
        console.log('items', items)
        return items.map(item => {
            const foundUnitDelai = optionsUnits.value.find(unit => unit.text === item.delai.code)
            const foundUnitMoq = optionsUnits.value.find(unit => unit.text === item.moq.code)
            const foundUnitPackaging = optionsUnits.value.find(unit => unit.text === item.packaging.code)
            const foundUnitPoidsCu = optionsUnits.value.find(unit => unit.text === item.poidsCu.code)

            const transformedPrices = item.prices.map(price => {
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
                delai: {value: item.delai.value, code: foundUnitDelai ? foundUnitDelai.value : item.delai.code},
                moq: {value: item.moq.value, code: foundUnitMoq ? foundUnitMoq.value : item.moq.code},
                packaging: {value: item.packaging.value, code: foundUnitPackaging ? foundUnitPackaging.value : item.packaging.code},
                poidsCu: {value: item.poidsCu.value, code: foundUnitPoidsCu ? foundUnitPoidsCu.value : item.poidsCu.code},
                prices: transformedPrices
            }
        })
    }
    const localItems1 = computed(() => transformItems(mainItems1.value))
    const localItems2 = computed(() => transformItems(mainItems2.value))

    function initializePermanentFilters() {
        if (component.value !== null) {
            componentId.value = getIdFromIri(props.component)
            fetchCriteria.addFilter('component', props.component)
        }
        if (product.value !== null) {
            fetchCriteria.addFilter('product', props.product)
        }
        if (supplier.value !== null) {
            fetchCriteria.addFilter('supplier', props.supplier)
        }
        if (customer.value !== null) {
            fetchCriteria.addFilter('customer', props.customer)
        }
    }
    async function refreshTable() {
        initializePermanentFilters()
        await storeComponentSuppliers.fetch(fetchCriteria.getFetchCriteria)
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
        <div
            v-if="!inputError">
            <AppRowsTablePage
                :main-fields="fieldsMain1"
                :price-fields="fieldsPrices"
                :items="localItems1"
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
                :items="localItems2"
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
