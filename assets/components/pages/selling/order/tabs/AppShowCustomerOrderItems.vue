<script setup>
    import AppSuspense from '../../../../AppSuspense.vue'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import useUser from '../../../../../stores/security'
    import {Modal} from 'bootstrap'
    import {computed, onBeforeMount, ref} from 'vue'
    import {useCustomerOrderItemsStore} from '../../../../../stores/customer/customerOrderItems'
    import AppFixedItemAddForm from "./itemsForms/AppFixedItemAddForm.vue";
    import AppForeCastItemAddForm from "./itemsForms/AppForeCastItemAddForm.vue";
    import useOptions from "../../../../../stores/option/options";

    const props = defineProps({
        order: {default: () => ({}), required: true, type: Object},
        customer: {default: () => ({}), required: true, type: Object}
    })
    //console.log('customer', props.customer)
    //region initialisation des constantes et variables
    const fetchUser = useUser()
    const isLoaded = ref(false)
    const isSellingWriterOrAdmin = fetchUser.isSellingWriter || fetchUser.isSellingAdmin
    const roleUser = ref(isSellingWriterOrAdmin ? 'writer' : 'reader')
    const fetchUnitOptions = useOptions('units')
    const fetchCurrencyOptions = useOptions('currencies')
    const formKeys = ref(0)
    const promises = []
    promises.push(fetchUnitOptions.fetchOp())
    promises.push(fetchCurrencyOptions.fetchOp())
    await Promise.all(promises).then(() => {
        console.log('promises', promises)
        console.log('fetchUnitOptions', fetchUnitOptions)
        console.log('fetchCurrencyOptions', fetchCurrencyOptions)
        formKeys.value++
    })
    const optionsUnit = computed(() =>
        fetchUnitOptions.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))
    const optionsCurrency = computed(() =>
        fetchCurrencyOptions.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))
    console.log('optionsUnit', optionsUnit.value)
    console.log('optionsCurrency', optionsCurrency.value)
    const storeCustomerOrderItems = useCustomerOrderItemsStore()
    const customerOrderItemsCriteria = useFetchCriteria('customer-order-items-criteria')
    const tableKey = ref(0)

    const stateOptions = [
        {text: 'partially_delivered', value: 'partially_delivered'},
        {text: 'delivered', value: 'delivered'},
        {text: 'agreed', value: 'agreed'}
    ]
    const fixedFamilies = ['fixed', 'edi_orders', 'free']
    //region      initialisation des données computed
    const customerOrderItems = computed(() => storeCustomerOrderItems.itemsCustomerOrders)

    const fieldsCommande = computed(() => [
        {label: 'Forecast', name: 'isForecast', type: 'boolean', width: 50},
        {label: 'Produit', name: 'product', type: 'multiselect-fetch', api: '/api/products', filteredProperty: 'code', max: 1},
        {label: 'Composant', name: 'component', type: 'multiselect-fetch', api: '/api/components', filteredProperty: 'code', max: 1},
        {
            label: 'Quantité souhaitée',
            name: 'requestedQuantity',
            info: 'La quantité doit être supérieure à la quantité minimale de livraison définie sur la fiche produit',
            filter: true,
            min: true,
            measure: {
                code: {
                    label: 'Code',
                    name: 'requestedQuantity.code',
                    options: {
                        label: value =>
                            optionsUnit.value.find(option => option.type === value)?.text ?? null,
                        options: optionsUnit.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'requestedQuantity.value',
                    type: 'number',
                    step: 0.1
                }
            },
            trie: true,
            type: 'measure',
            width: 150
        },
        {
            label: 'Quantité confirmée',
            name: 'confirmedQuantity',
            filter: true,
            min: true,
            measure: {
                code: {
                    label: 'Code',
                    name: 'confirmedQuantity.code',
                    options: {
                        label: value =>
                            optionsUnit.value.find(option => option.type === value)?.text ?? null,
                        options: optionsUnit.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'confirmedQuantity.value',
                    type: 'number',
                    step: 0.1
                }
            },
            trie: true,
            type: 'measure',
            width: 150
        },
        {label: 'date de livraison souhaitée', name: 'requestedDate', trie: true, type: 'date', width: 80},
        {label: 'Date de livraison confirmée', name: 'confirmedDate', trie: true, type: 'date', width: 80},
        {
            label: 'Prix Unitaire',
            name: 'price',
            trie: false,
            type: 'measure',
            measure: {
                code: {
                    label: 'Code',
                    name: 'price.code',
                    options: {
                        label: value =>
                            optionsCurrency.value.find(option => option.type === value)?.text ?? null,
                        options: optionsCurrency.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'price.value',
                    type: 'number',
                    step: 0.01
                }
            }
        },
        {
            label: 'Etat',
            name: 'state',
            options: {
                label: value =>
                    stateOptions.find(option => option.type === value)?.text ?? null,
                options: stateOptions
            },
            trie: true,
            type: 'select'
        },
        {label: 'Référence item de commande', name: 'ref', trie: true, type: 'text'},
        {label: 'Description', name: 'notes', trie: true, type: 'text'}
    ])
    //endregion
    //endregion

    //region Methods

    async function refreshTableCustomerOrders() {
        await storeCustomerOrderItems.fetchAll(customerOrderItemsCriteria.getFetchCriteria)
    }
    async function deletedCustomerOrderItem(idRemove) {
        await storeCustomerOrderItems.remove(idRemove)
        await refreshTableCustomerOrders()
    }
    async function getPageCustomerOrders(nPage) {
        customerOrderItemsCriteria.gotoPage(parseFloat(nPage))
        await storeCustomerOrderItems.fetchAll(customerOrderItemsCriteria.getFetchCriteria)
    }
    function addPermanentFilters() {
        customerOrderItemsCriteria.addFilter('parentOrder', props.order['@id'])
    }
    async function searchCustomerOrders(inputValues) {
        // console.log('inputValues', inputValues)
        customerOrderItemsCriteria.resetAllFilter()
        addPermanentFilters()
        if (inputValues.product) customerOrderItemsCriteria.addFilter('item', inputValues.product)
        if (inputValues.component) customerOrderItemsCriteria.addFilter('item', inputValues.component)
        if (inputValues.ref) customerOrderItemsCriteria.addFilter('ref', inputValues.ref)
        if (inputValues.requestedQuantity) customerOrderItemsCriteria.addFilter('requestedQuantity.value', inputValues.requestedQuantity.value)
        if (inputValues.requestedQuantity) {
            const requestedUnit = optionsUnit.value.find(unit => unit['@id'] === inputValues.requestedQuantity.code)
            customerOrderItemsCriteria.addFilter('requestedQuantity.code', requestedUnit.code)
        }
        if (inputValues.requestedDate) customerOrderItemsCriteria.addFilter('requestedDate', inputValues.requestedDate)
        if (inputValues.confirmedQuantity) customerOrderItemsCriteria.addFilter('confirmedQuantity.value', inputValues.confirmedQuantity.value)
        if (inputValues.confirmedQuantity) {
            const requestedUnit = optionsUnit.value.find(unit => unit['@id'] === inputValues.confirmedQuantity.code)
            customerOrderItemsCriteria.addFilter('confirmedQuantity.code', requestedUnit.code)
        }
        if (inputValues.confirmedDate) customerOrderItemsCriteria.addFilter('confirmedDate', inputValues.confirmedDate)
        if (inputValues.state) customerOrderItemsCriteria.addFilter('embState.state', inputValues.state)
        if (inputValues.notes) customerOrderItemsCriteria.addFilter('notes', inputValues.notes)
        if (inputValues.product && !inputValues.component) {
            await storeCustomerOrderItems.fetchAllProduct(customerOrderItemsCriteria.getFetchCriteria)
            return
        }
        if (!inputValues.product && inputValues.component) {
            await storeCustomerOrderItems.fetchAllComponent(customerOrderItemsCriteria.getFetchCriteria)
            return
        }
        if (inputValues.product && inputValues.component) {
            window.alert('Vous ne pouvez pas rechercher à la fois un produit et un composant')
            return
        }
        await storeCustomerOrderItems.fetchAll(customerOrderItemsCriteria.getFetchCriteria)
    }
    async function cancelSearchCustomerOrders() {
        customerOrderItemsCriteria.resetAllFilter()
        addPermanentFilters()
        await storeCustomerOrderItems.fetchAll(customerOrderItemsCriteria.getFetchCriteria)
        //On réinitialise les données du formulaire
        document.getElementById('formCustomerOrdersTable').reset()
    }
    async function trierAlphabetCustomerOrders(payload) {
        addPermanentFilters()
        if (payload.name === 'requestedQuantity') {
            customerOrderItemsCriteria.addSort('requestedQuantity.value', payload.direction)
        } else if (payload.name === 'confirmedQuantity') {
            customerOrderItemsCriteria.addSort('confirmedQuantity.value', payload.direction)
        } else if (payload.name === 'state') {
            customerOrderItemsCriteria.addSort('embState.state', payload.direction)
        } else {
            customerOrderItemsCriteria.addSort(payload.name, payload.direction)
        }
        // console.log('customerOrderItemsCriteria', customerOrderItemsCriteria.getFetchCriteria)
        await storeCustomerOrderItems.fetchAll(customerOrderItemsCriteria.getFetchCriteria)
    }
    async function updateTable() {
        console.log('updateTable')
        await refreshTableCustomerOrders()
        //On rafraichit le formulaire
        tableKey.value++
    }
    //endregion
    //chargement des données
    addPermanentFilters()
    await storeCustomerOrderItems.fetchAll(customerOrderItemsCriteria.getFetchCriteria)
    isLoaded.value = true
    console.log('éléments de tableau chargés')
    //endregion
</script>

<template>
    <AppSuspense>
        <AppFixedItemAddForm
            v-if="isLoaded"
            :key="`addFixedItem_${formKeys}`"
            :customer="customer"
            :order="order"
            :options-currency="optionsCurrency"
            :options-unit="optionsUnit"
            @updated="updateTable"/>
    </AppSuspense>
    <AppSuspense>
        <AppForeCastItemAddForm
            v-if="isLoaded && !fixedFamilies.includes(order.orderFamily)"
            :key="`addForecastItem_${formKeys}`"
            :customer="customer"
            :order="order"
            :options-currency="optionsCurrency"
            :options-unit="optionsUnit"
            @updated="updateTable"/>
    </AppSuspense>
    <AppSuspense>
        <AppCardableTable
            v-if="isLoaded"
            :key="tableKey"
            :current-page="storeCustomerOrderItems.currentPage"
            :fields="fieldsCommande"
            :first-page="storeCustomerOrderItems.firstPage"
            :items="customerOrderItems"
            :last-page="storeCustomerOrderItems.lastPage"
            :next-page="storeCustomerOrderItems.nextPage"
            :pag="storeCustomerOrderItems.pagination"
            :previous-page="storeCustomerOrderItems.previousPage"
            :user="roleUser"
            title
            form="formCustomerOrdersTable"
            @deleted="deletedCustomerOrderItem"
            @get-page="getPageCustomerOrders"
            @trier-alphabet="trierAlphabetCustomerOrders"
            @search="searchCustomerOrders"
            @cancel-search="cancelSearchCustomerOrders">
            <template #title>
                <span>Items de commande {{ order.ref }}</span>
                <button
                    class="btn btn-success btn-float-right m-1"
                    data-bs-toggle="modal"
                    data-bs-target="#modalAddNewOrderItem">
                    Ajouter Item en Ferme
                </button>
                <button
                    v-if="!fixedFamilies.includes(order.orderFamily)"
                    class="btn btn-success btn-float-right m-1"
                    data-bs-toggle="modal"
                    data-bs-target="#modalAddNewForecastItem">
                    Ajouter Item en Prévisionnel {{ order.orderFamily }}
                </button>
            </template>
        </AppCardableTable>
    </AppSuspense>
</template>
