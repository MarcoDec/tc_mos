<script setup>
    import api from '../../../../../api'
    import AppSuspense from '../../../../AppSuspense.vue'
    import {useCustomerOrderItemsStore} from '../../../../../stores/customer/customerOrderItems'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import useOptions from '../../../../../stores/option/options'
    import {computed, ref} from 'vue'
    import useUser from '../../../../../stores/security'
    import AppModal from '../../../../modal/AppModal.vue'
    import AppFormJS from '../../../../form/AppFormJS.js'
    import {Modal} from 'bootstrap'

    const props = defineProps({
        order: {default: () => ({}), required: true, type: Object}
    })
    const fetchUser = useUser()
    //const currentCompany = fetchUser.company
    const fetchUnitOptions = useOptions('units')
    const customerOrderItemsCriteria = useFetchCriteria('customer-order-items-criteria')
    function addPermanentFilters() {
        customerOrderItemsCriteria.addFilter('parentOrder', props.order['@id'])
    }
    addPermanentFilters()
    const isSellingWriterOrAdmin = fetchUser.isSellingWriter || fetchUser.isSellingAdmin
    const roleuser = ref(isSellingWriterOrAdmin ? 'writer' : 'reader')
    const storeCustomerOrderItems = useCustomerOrderItemsStore()
    await storeCustomerOrderItems.fetchAll(customerOrderItemsCriteria.getFetchCriteria)
    const customerOrderItems = computed(() => storeCustomerOrderItems.itemsCustomerOrders)

    await fetchUnitOptions.fetchOp()
    const optionsUnit = computed(() =>
        fetchUnitOptions.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))
    const stateOptions = [
        {text: 'partially_delivered', value: 'partially_delivered'},
        {text: 'delivered', value: 'delivered'},
        {text: 'agreed', value: 'agreed'}
    ]

    const fieldsCommande = [
        {label: 'Forecast', name: 'isForecast', type: 'boolean', width: 50},
        {label: 'Produit', name: 'product', type: 'multiselect-fetch', api: '/api/products', filteredProperty: 'code', max: 1},
        {label: 'Composant', name: 'component', type: 'multiselect-fetch', api: '/api/components', filteredProperty: 'code', max: 1},
        {
            label: 'Quantité souhaitée',
            name: 'requestedQuantity',
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
                        label: value => value, //TODO: Ajouter le tableau d'option des devises
                        options: []
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
    ]
    async function refreshTableCustomerOrders() {
        await storeCustomerOrderItems.fetchAll(customerOrderItemsCriteria.getFetchCriteria)
    }
    //await refreshTableCustomerOrders()
    async function deletedCustomerOrderItem(idRemove) {
        await storeCustomerOrderItems.remove(idRemove)
        await refreshTableCustomerOrders()
    }
    async function getPageCustomerOrders(nPage) {
        customerOrderItemsCriteria.gotoPage(parseFloat(nPage))
        await storeCustomerOrderItems.fetchAll(customerOrderItemsCriteria.getFetchCriteria)
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
            const requestedUnit = units.find(unit => unit['@id'] === inputValues.requestedQuantity.code)
            customerOrderItemsCriteria.addFilter('requestedQuantity.code', requestedUnit.code)
        }
        if (inputValues.requestedDate) customerOrderItemsCriteria.addFilter('requestedDate', inputValues.requestedDate)
        if (inputValues.confirmedQuantity) customerOrderItemsCriteria.addFilter('confirmedQuantity.value', inputValues.confirmedQuantity.value)
        if (inputValues.confirmedQuantity) {
            const requestedUnit = units.find(unit => unit['@id'] === inputValues.confirmedQuantity.code)
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
    const fieldsOrderItem = computed(() => [
        {label: 'Produit', name: 'product', type: 'multiselect-fetch', api: '/api/products', filteredProperty: 'code', max: 1},
        {label: 'Composant', name: 'component', type: 'multiselect-fetch', api: '/api/components', filteredProperty: 'code', max: 1},
        {
            label: 'Quantité souhaitée',
            name: 'requestedQuantity',
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
        {label: 'Date de livraison souhaitée', name: 'requestedDate', trie: true, type: 'date', width: 80},
        {label: 'Date de livraison confirmée', name: 'confirmedDate', trie: true, type: 'date', width: 80},
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
        }
    ])
    const fieldsOpenOrderItem = computed(() => [
        {label: 'Produit', name: 'product', type: 'multiselect-fetch', api: '/api/products', filteredProperty: 'code', max: 1},
        {label: 'Composant', name: 'component', type: 'multiselect-fetch', api: '/api/components', filteredProperty: 'code', max: 1},
        {
            label: 'Quantité souhaitée',
            name: 'requestedQuantity',
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
        {label: 'Date de livraison souhaitée', name: 'requestedDate', trie: true, type: 'date', width: 80}
    ])
    const localForecastData = ref({})
    function openAddOrderItemForm() {
        console.log('initialisation du formulaire')
        //document.getElementById('modalAddNewOrderItem').style.display = 'block'
    }
    const forecastFormKey = ref(0)
    function updateForecastValue(value) {
        if (value.product && value.product !== localForecastData.value.product) {
            api(value.product, 'GET').then(response => {
                // console.log('produit sélectionné', response)
                if (localForecastData.value.requestedQuantity) localForecastData.value.requestedQuantity.code = response.unit
                else localForecastData.value.requestedQuantity = {code: response.unit}
                forecastFormKey.value++
                // console.log(localForecastData.value)
            })
        }
        if (value.component && value.component !== localForecastData.value.component) {
            api(value.component, 'GET').then(response => {
                // console.log('composant sélectionné', response)
                if (localForecastData.value.requestedQuantity) localForecastData.value.requestedQuantity.code = response.unit
                else localForecastData.value.requestedQuantity = {code: response.unit}
                forecastFormKey.value++
                // console.log(localForecastData.value)
            })
        }
        localForecastData.value = value
        // console.log('localForecastData', localForecastData.value)
    }
    const tableKey = ref(0)
    const customerOrderItemForecastCreateModal = ref(null)
    function addForecastItem() {
        console.log('localForecastData', localForecastData.value)
        //On ajoute le champ parentOrder
        localForecastData.value.parentOrder = props.order['@id']
        //On ajoute le type d'item isForecast
        localForecastData.value.isForecast = true
        //On remplace la valeur du code qui contient actuellement l'id de l'unité par le code de l'unité
        const requestedUnit = optionsUnit.value.find(unit => unit.value === localForecastData.value.requestedQuantity.code)
        //Si c'est un produit on positionne la clé item avec la valeur de la clé produit, sinon on positionne la clé item avec la valeur de la clé component
        if (localForecastData.value.product) localForecastData.value.item = localForecastData.value.product
        else localForecastData.value.item = localForecastData.value.component
        localForecastData.value.requestedQuantity.code = requestedUnit.text
        storeCustomerOrderItems.add(localForecastData.value)
        //On ferme la modale
        if (customerOrderItemForecastCreateModal.value) {
            const modalElement = customerOrderItemForecastCreateModal.value.$el
            const bootstrapModal = Modal.getInstance(modalElement)
            bootstrapModal.hide()
        }
        //On réinitalise les données locales
        localForecastData.value = {}
        //On rafraichit les données du tableau
        refreshTableCustomerOrders()
        //On rafraichit le formulaire
        tableKey.value++
    }
    const fixedFamilies = ['fixed', 'edi_orders', 'free']
</script>

<template>
    <AppSuspense>
        <AppModal id="modalAddNewOrderItem" class="four" title="Ajouter Item en Ferme">
            <AppFormJS
                id="formAddNewOrderItem"
                :fields="fieldsOrderItem"/>
        </AppModal>
        <AppModal
            v-if="!fixedFamilies.includes(order.orderFamily)"
            id="modalAddNewForecastItem"
            ref="customerOrderItemForecastCreateModal"
            class="four"
            title="Ajouter Item en Prévisionnel">
            <AppFormJS
                id="formAddNewOrderItem"
                :key="forecastFormKey"
                :model-value="localForecastData"
                :fields="fieldsOpenOrderItem"
                submit-label="Ajouter"
                @update:model-value="updateForecastValue"
                @submit="addForecastItem"/>
        </AppModal>
        <AppCardableTable
            :key="tableKey"
            :current-page="storeCustomerOrderItems.currentPage"
            :fields="fieldsCommande"
            :first-page="storeCustomerOrderItems.firstPage"
            :items="customerOrderItems"
            :last-page="storeCustomerOrderItems.lastPage"
            :next-page="storeCustomerOrderItems.nextPage"
            :pag="storeCustomerOrderItems.pagination"
            :previous-page="storeCustomerOrderItems.previousPage"
            :user="roleuser"
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
