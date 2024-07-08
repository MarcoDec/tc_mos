<script setup>
    import api from '../../../api'
    import AppPricesTable from '../../app-prices-table/AppPricesTable.vue'
    import AppRowsTablePage from './AppRowsTablePage.vue'
    import {computed, ref, watchEffect} from 'vue'
    import AppSuspense from '../../../components/AppSuspense.vue'
    import useOptions from '../../../stores/option/options'
    import useFetchCriteria from "../../../stores/fetch-criteria/fetchCriteria"
    import useUser from "../../../stores/security"

    const user = useUser()
    // console.log('user company', user.company)
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
    const defaultAddFormValues = {
        component: null,
        supplier: null,
        product: null,
        customer: null,
        copperWeight: {
            code: 'g',
            value: null
        },
        deliveryTime: {
            code: 'j',
            value: null
        },
        moq: {
            code: null,
            value: null
        },
        packaging: {
            code: null,
            value: null
        },
        reference: null,
        index: null,
        incoterms: null,
        packagingKind: null,
        proportion: 100,
        kind: 'Série'
    }
    //region fetch options
    const storeUnits = useOptions('units')
    const optionsUnits = computed(() => storeUnits.getOptionsMap())
    storeUnits.fetchOp()

    const storeCurrencies = useOptions('currencies')
    const currenciesOptions = computed(() => storeCurrencies.getOptionsMap())
    console.log('currenciesOptions', currenciesOptions.value)
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
        mainTitle.value += ' pour ' + component.value.code
        defaultAddFormValues.component = component.value['@id']
    }
    if (props.supplier) {
        supplier.value = await api(props.supplier, 'GET')
        mainTitle.value += ' pour ' + supplier.value.name
        defaultAddFormValues.supplier = supplier.value['@id']
    }
    if (props.product) {
        product.value = await api(props.product, 'GET')
        mainTitle.value += ' pour ' + product.value.name
        defaultAddFormValues.product = product.value['@id']
    }
    if (props.customer) {
        customer.value = await api(props.customer, 'GET')
        mainTitle.value += ' pour ' + customer.value.name
        defaultAddFormValues.customer = customer.value['@id']
    }
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
            label: 'Type de Grille',
            name: 'kind',
            type: 'select',
            width: '100',
            options: {
                label: value => value,
                options: [
                    {text: 'Prototype', value: 'Prototype'},
                    {text: 'Série', value: 'Série'}
                ]
            }
        },
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
            width: '200',
            max: 1,
            readonly: !!props.component
        }
    ]
    const supplierFields = [
        {
            label: 'Fournisseur',
            name: 'supplier',
            type: 'multiselect-fetch',
            api: '/api/suppliers',
            filteredProperty: 'name',
            width: '150',
            max: 1
        }
    ]
    const customerFields = [
        {
            label: 'Client',
            name: 'customer',
            type: 'multiselect-fetch',
            api: '/api/customers',
            filteredProperty: 'name',
            width: '150',
            max: 1
        }
    ]
    const productFields = [
        {
            label: 'Produit',
            name: 'product',
            type: 'multiselect-fetch',
            api: '/api/products',
            filteredProperty: 'name',
            width: '150',
            max: 1
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
                prices: '/api/customer-product-prices',
                parentPriceFieldName: 'product'
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
                    prices: '/api/customer-component-prices',
                    parentPriceFieldName: 'component'
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
                prices: '/api/customer-component-prices',
                parentPriceFieldName: 'component'
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
                prices: '/api/supplier-component-prices',
                parentPriceFieldName: 'component'
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
                    prices: '/api/supplier-product-prices',
                    parentPriceFieldName: 'product'
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
                prices: '/api/supplier-product-prices',
                parentPriceFieldName: 'product'
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
            prices: '/api/supplier-component-prices',
            parentPriceFieldName: 'component'
        }
        fieldsMain2.value = [
            ...componentFields,
            ...customerFields,
            ...commonFields
        ]
        title2.value = 'Tableau des prix Composant - Clients'
        apis.value[1] = {
            main: '/api/customer-components',
            prices: '/api/customer-component-prices',
            parentPriceFieldName: 'component'
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
            prices: '/api/customer-product-prices',
            parentPriceFieldName: 'product'
        }
        fieldsMain2.value = [
            ...productFields,
            ...supplierFields,
            ...commonFields
        ]
        title2.value = 'Tableau des prix Produit - Fournisseurs'
        apis.value[1] = {
            main: '/api/supplier-products',
            prices: '/api/supplier-product-prices',
            parentPriceFieldName: 'product'
        }

    }
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
        console.log('mainItems1', mainItems1.value)
        const response2 = await api(apis.value[1].main + fetchCriteria2.getFetchCriteria, 'GET')
        mainItems2.value = response2['hydra:member'].map(item => transformPricesAsAnArray(item))
    }
    await loadData()

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
            type: 'measure',
            width: '250'
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
            type: 'measure',
            width: '250'
        },
        {
            label: 'ref',
            name: 'ref',
            type: 'text',
        }
    ]

    async function transformItems(items) {
        return await Promise.all(items.map(async item => {
            const foundUnitDelai = optionsUnits.value.find(unit => unit.text === item.deliveryTime.code)
            const foundUnitMoq = optionsUnits.value.find(unit => unit.text === item.moq.code)
            const foundUnitPackaging = optionsUnits.value.find(unit => unit.text === item.packaging.code)
            const foundUnitPoidsCu = optionsUnits.value.find(unit => unit.text === item.copperWeight.code)
            //On charge les prix via l'API en utilisant le champ '@id' de l'item de prix
            const promises = item.prices.map(price => {
                return api(price['@id'], 'GET')
            })
            const values = await Promise.all(promises)
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
    async function addItem(formData, index) {
        const currentApi = apis.value[index].main
        const data = {}
        if (formData.component) {
            data.component = formData.component
        }
        if (formData.supplier) {
            data.supplier = formData.supplier
        }
        if (formData.product) {
            data.product = formData.product
        }
        if (formData.customer) {
            data.customer = formData.customer
        }
        data.copperWeight = {
            code: optionsUnits.value.find(option => option.value === formData.copperWeight.code)?.text,
            value: formData.copperWeight.value??0
        }
        data.deliveryTime = {
            code: optionsUnits.value.find(option => option.value === formData.deliveryTime.code)?.text,
            value: formData.deliveryTime.value??0
        }
        data.moq = {
            code: optionsUnits.value.find(option => option.value === formData.moq.code)?.text,
            value: formData.moq.value??0
        }
        data.packaging = {
            code: optionsUnits.value.find(option => option.value === formData.packaging.code)?.text,
            value: formData.packaging.value??0
        }
        data.reference = formData.reference
        data.index = formData.index
        data.incoterms = formData.incoterms
        data.packagingKind = formData.packagingKind
        data.proportion = formData.proportion??0
        // console.log('formData', formData)
        data.kind = formData.kind
        data.administeredBy = user.company
        await api(currentApi, 'POST', data)
        await loadData()
    }
    async function annuleUpdated() {
        console.log('annuleUpdated')
    }
    async function updateItems(item) {
        const data = {}
        const iri = item['@id']
        if (item.component) {
            data.component = item.component
        }
        if (item.supplier) {
            data.supplier = item.supplier
        }
        if (item.product) {
            data.product = item.product
        }
        if (item.customer) {
            data.customer = item.customer
        }
        data.proportion = item.proportion
        data.deliveryTime = {
            code: optionsUnits.value.find(option => option.value === item.deliveryTime.code)?.text,
            value: item.deliveryTime.value
        }
        data.moq = {
            code: optionsUnits.value.find(option => option.value === item.moq.code)?.text,
            value: item.moq.value
        }
        data.copperWeight = {
            code: optionsUnits.value.find(option => option.value === item.copperWeight.code)?.text,
            value: item.copperWeight.value
        }
        data.code = item.code
        data.index = item.index
        data.incoterms = item.incoterms
        data.packaging = {
            code: optionsUnits.value.find(option => option.value === item.packaging.code)?.text,
            value: item.packaging.value
        }
        data.packagingKind = item.packagingKind
        await api(iri, 'PATCH', data)
        await loadData()
    }
    async function addItemPrice(formData, index) {
        console.log('addItemPrice', formData, index)
        const currentApi = apis.value[index].prices
        const currentItemFieldNames = apis.value[index].parentPriceFieldName
        const data = {}
        if (formData) {
            data[currentItemFieldNames] = formData.item
            const priceCode = currenciesOptions.value.find(option => option.value === formData.price.code)?currenciesOptions.value.find(option => option.value === formData.price.code).text:'EUR'
            data.price = {
                code: priceCode,
                value: formData.price.value
            }
            data.quantity = {
                code: optionsUnits.value.find(option => option.value === formData.quantity.code)?.text,
                value: formData.quantity.value
            }
            data.ref = formData.ref
        }
        await api(currentApi, 'POST', data)
        await loadData()
    }
    async function updateItemsPrices(item) {
        const data = {}
        const iri = item['@id']
        if (item.component) {
            data.component = item.component
        }
        if (item.product) {
            data.product = item.product
        }
        data.price = {
            code: currenciesOptions.value.find(option => option.value === item.price.code)?.text,
            value: item.price.value
        }
        data.quantity = {
            code: optionsUnits.value.find(option => option.value === item.quantity.code)?.text,
            value: item.quantity.value
        }
        data.ref = item.ref
        await api(iri, 'PATCH', data)
        await loadData()
    }
    async function deleted(id){
        // console.log('deleted', id)
        if (window.confirm('Voulez-vous vraiment supprimer cet élément ?') === false) return
        await api(id, 'DELETE')
        await loadData()
    }
    async function deletedPrices(id){
        if (window.confirm('Voulez-vous vraiment supprimer cet élément ?') === false) return
        await api(id, 'DELETE')
        await loadData()
    }
    // console.log('defaultAddFormValues', defaultAddFormValues)
</script>

<template>
    <AppSuspense>
        <div
            v-if="!inputError">
            <AppPricesTable
                id="prices"
                :default-add-form-values="defaultAddFormValues"
                :main-fields="fieldsMain1"
                :price-fields="fieldsPrices"
                :items="resolvedItems1"
                :title="title"
                form="formComponentSuppliersPricesTable"
                @add-item="addItem"
                @add-item-price="(item) => addItemPrice(item, 0)"
                @deleted="deleted"
                @deleted-prices="deletedPrices"
                @annule-update="annuleUpdated"
                @update-items="updateItems"
                @update-items-prices="updateItemsPrices"/>
            <AppPricesTable
                id="prices"
                :default-add-form-values="defaultAddFormValues"
                :main-fields="fieldsMain2"
                :price-fields="fieldsPrices"
                :items="resolvedItems2"
                :title="title"
                form="formComponentSuppliersPricesTable"
                @add-item="addItem"
                @add-item-price="(item) => addItemPrice(item, 0)"
                @deleted="deleted"
                @deleted-prices="deletedPrices"
                @annule-update="annuleUpdated"
                @update-items="updateItems"
                @update-items-prices="updateItemsPrices"/>
        </div>
        <div v-else class="bg-danger text-white text-center m-5 p-5">{{ errorMessage }}</div>
    </AppSuspense>
</template>
