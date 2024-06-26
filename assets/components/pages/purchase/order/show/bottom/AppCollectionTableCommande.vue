<script setup>
    import {Modal} from 'bootstrap'
    import {computed, nextTick, ref} from 'vue'
    import useUser from '../../../../../../stores/security'
    import {usePurchaseOrderItemComponentsStore} from '../../../../../../stores/purchase/order/purchaseOrderItem'
    import useFetchCriteria from '../../../../../../stores/fetch-criteria/fetchCriteria'
    import useOptions from '../../../../../../stores/option/options'
    import AppFixedItemAddForm from './itemsForms/AppFixedItemAddForm.vue'
    import AppFixedUpdateForm from './itemsForms/AppFixedUpdateForm.vue'
    import AppForeCastItemAddForm from './itemsForms/AppForeCastItemAddForm.vue'
    import AppForeCastUpdateForm from './itemsForms/AppForeCastUpdateForm.vue'
    import {Portal} from "portal-vue"

    const props = defineProps({
        order: {default: () => ({}), required: true, type: Object},
        supplier: {default: () => ({}), required: true, type: Object}
    })
    //Si l'utilisateur courant a les droits admin ou writer, il peut ajouter/modifier et supprimer des items de commande
    const showForeCastUpdateForm = ref(false)
    const showFixedUpdateForm = ref(false)
    //region initialisation des constantes et variables
    const fetchUser = useUser()
    const isLoaded = ref(false)
    const isPurchaseAdmin = fetchUser.isPurchaseAdmin
    const isPurchaseWriterOrAdmin = fetchUser.isPurchaseWriter || isPurchaseAdmin
    const roleUser = ref(isPurchaseWriterOrAdmin ? 'writer' : 'reader')
    const fetchUnitOptions = useOptions('units')
    const fetchCurrencyOptions = useOptions('currencies')
    const formKeys = ref(0)
    const formUpdateKeys = ref(0)
    const storePurchaseOrderItems = usePurchaseOrderItemComponentsStore()
    const purchaseOrderItemsCriteria = useFetchCriteria('purchase-order-items-criteria')
    const promises = []
    promises.push(fetchUnitOptions.fetchOp())
    promises.push(fetchCurrencyOptions.fetchOp())
    await Promise.all(promises).then(() => {
        console.log('options', fetchUnitOptions.options, fetchCurrencyOptions.options)
        formKeys.value++
    })
    const optionsUnit = computed(() => fetchUnitOptions.getOptionsMap())
    const optionsCurrency = computed(() => fetchCurrencyOptions.getOptionsMap())
    const tableKey = ref(0)

    const stateOptions = [
        {text: 'initial', value: 'initial'},
        {text: 'draft', value: 'draft'},
        {text: 'monthly', value: 'monthly'},
        {text: 'forecast', value: 'forecast'},
        {text: 'agreed', value: 'agreed'},
        {text: 'partially_received', value: 'partially_received'},
        {text: 'received', value: 'received'},
        {text: 'paid', value: 'paid'},
    ]
    const fixedFamilies = ['fixed', 'edi_orders', 'free']
    //region      initialisation des données computed
    const customerOrderItems = computed(() => storePurchaseOrderItems.itemsPurchaseOrders)

    const fieldsCommande = computed(() => [
        {label: 'Forecast', name: 'isForecast', type: 'boolean', width: 50},
        {label: 'Produit', name: 'product', type: 'multiselect-fetch', api: '/api/products', filteredProperty: 'code', max: 1},
        {label: 'Composant', name: 'component', type: 'multiselect-fetch', api: '/api/components', filteredProperty: 'code', max: 1},
        {
            label: 'Quantité souhaitée',
            name: 'requestedQuantity',
            info: 'La quantité doit être supérieure à la quantité minimale de livraison définie sur la fiche produit',
            filter: false,
            min: true,
            measure: {
                code: {
                    label: 'Code',
                    name: 'requestedQuantity.code',
                    options: {
                        label: value => fetchUnitOptions.getLabel(value),
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
            filter: false,
            min: true,
            measure: {
                code: {
                    label: 'Code',
                    name: 'confirmedQuantity.code',
                    options: {
                        label: value => fetchUnitOptions.getLabel(value),
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
            filter: false,
            measure: {
                code: {
                    label: 'Code',
                    name: 'price.code',
                    options: {
                        label: value => fetchCurrencyOptions.getLabel(value),
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
        await storePurchaseOrderItems.fetch(purchaseOrderItemsCriteria.getFetchCriteria)
    }
    async function deletedPurchaseOrderItem(idRemove) {
        //On demande une confirmation avant de supprimer
        const confirmation = window.confirm('Voulez-vous vraiment supprimer cet item de commande ?')
        if (!confirmation) return
        await storePurchaseOrderItems.remove(idRemove)
        await refreshTableCustomerOrders()
    }
    async function getPagePurchaseOrders(nPage) {
        purchaseOrderItemsCriteria.gotoPage(parseFloat(nPage))
        await storePurchaseOrderItems.fetch(purchaseOrderItemsCriteria.getFetchCriteria)
    }
    function addPermanentFilters() {
        purchaseOrderItemsCriteria.addFilter('parentOrder', props.order['@id'])
    }
    async function searchPurchaseOrders(inputValues) {
        purchaseOrderItemsCriteria.resetAllFilter()
        addPermanentFilters()
        console.log('inputValues', inputValues)
        if (inputValues.product) purchaseOrderItemsCriteria.addFilter('item', inputValues.product)
        if (inputValues.component) purchaseOrderItemsCriteria.addFilter('item', inputValues.component)
        if (inputValues.ref) purchaseOrderItemsCriteria.addFilter('ref', inputValues.ref)
        if (inputValues.requestedQuantity) purchaseOrderItemsCriteria.addFilter('requestedQuantity.value', inputValues.requestedQuantity.value)
        if (inputValues.requestedQuantity) {
            const requestedUnit = optionsUnit.value.find(unit => unit['@id'] === inputValues.requestedQuantity.code)
            purchaseOrderItemsCriteria.addFilter('requestedQuantity.code', requestedUnit.code)
        }
        if (inputValues.requestedDate) purchaseOrderItemsCriteria.addFilter('requestedDate', inputValues.requestedDate)
        if (inputValues.confirmedQuantity) purchaseOrderItemsCriteria.addFilter('confirmedQuantity.value', inputValues.confirmedQuantity.value)
        if (inputValues.confirmedQuantity) {
            const requestedUnit = optionsUnit.value.find(unit => unit['@id'] === inputValues.confirmedQuantity.code)
            purchaseOrderItemsCriteria.addFilter('confirmedQuantity.code', requestedUnit.code)
        }
        if (inputValues.confirmedDate) purchaseOrderItemsCriteria.addFilter('confirmedDate', inputValues.confirmedDate)
        if (inputValues.state) purchaseOrderItemsCriteria.addFilter('embState.state[]', inputValues.state)
        if (inputValues.notes) purchaseOrderItemsCriteria.addFilter('notes', inputValues.notes)
        if (inputValues.product && !inputValues.component) {
            await storePurchaseOrderItems.fetchAllProduct(purchaseOrderItemsCriteria.getFetchCriteria)
            return
        }
        if (!inputValues.product && inputValues.component) {
            await storePurchaseOrderItems.fetchAllComponent(purchaseOrderItemsCriteria.getFetchCriteria)
            return
        }
        if (typeof inputValues.isForecast !== 'undefined') {
            purchaseOrderItemsCriteria.addFilter('isForecast', inputValues.isForecast ? 1: 0)
        }
        if (inputValues.product && inputValues.component) {
            window.alert('Vous ne pouvez pas rechercher à la fois un produit et un composant')
            return
        }
        await storePurchaseOrderItems.fetch(purchaseOrderItemsCriteria.getFetchCriteria)
    }
    async function cancelSearchPurchaseOrderItems() {
        purchaseOrderItemsCriteria.resetAllFilter()
        addPermanentFilters()
        await storePurchaseOrderItems.fetch(purchaseOrderItemsCriteria.getFetchCriteria)
    }
    async function trierAlphabetPurchaseOrderItems(payload) {
        addPermanentFilters()
        if (payload.name === 'requestedQuantity') {
            purchaseOrderItemsCriteria.addSort('requestedQuantity.value', payload.direction)
        } else if (payload.name === 'confirmedQuantity') {
            purchaseOrderItemsCriteria.addSort('confirmedQuantity.value', payload.direction)
        } else if (payload.name === 'state') {
            purchaseOrderItemsCriteria.addSort('embState.state', payload.direction)
        } else {
            purchaseOrderItemsCriteria.addSort(payload.name, payload.direction)
        }
        await storePurchaseOrderItems.fetch(purchaseOrderItemsCriteria.getFetchCriteria)
    }
    async function updateTable() {
        await refreshTableCustomerOrders()
        showForeCastUpdateForm.value = false
        tableKey.value++
    }
    //endregion
    //chargement des données
    addPermanentFilters()
    await storePurchaseOrderItems.fetch(purchaseOrderItemsCriteria.getFetchCriteria)
    isLoaded.value = true
    //endregion
    const canModifyForm = ref(true)
    const fixedUpdateModalId = 'modalUpdateFixedItem'
    const forecastUpdateModalId = 'modalUpdateForecastItem'
    function updateItemToUpdate(item) {
        storePurchaseOrderItems.setCurrentUnitOptions(optionsUnit.value)
        storePurchaseOrderItems.setCurrentCurrencyOptions(optionsCurrency.value)
        storePurchaseOrderItems.setCurrentItem(item)
        formUpdateKeys.value++
        // console.log('updatedStore', storePurchaseOrderItems.currentUnitOptions, storePurchaseOrderItems.currentCurrencyOptions)
        // Si l'item n'est pas à l'état "draft" et que l'utilisateur n'est pas "administrateur" alors on ne peut pas modifier l'item
        if (item.state !== 'draft' && !isPurchaseAdmin && !item.isForecast) {
            canModifyForm.value = false
            return
        }
        if (item.isForecast) {
            showForeCastUpdateForm.value = true
            nextTick(() => {
                const modalElement = document.getElementById(forecastUpdateModalId)
                const bootstrapModal = Modal.getInstance(modalElement)
                bootstrapModal.show()
            })
            return
        }
        showFixedUpdateForm.value = true
        nextTick(() => {
            const modalElement = document.getElementById(fixedUpdateModalId)
            const bootstrapModal = Modal.getInstance(modalElement)
            bootstrapModal.show()
        })
    }
    const fixedAddModalId = 'modalAddNewFixedPurchaseItem'
    let modalEventListener = null
    function openModalAddNewOrderItem() {
        // On récupère la modale d'ajout
        const modalElement = document.getElementById(fixedAddModalId)
        const bootstrapModal = Modal.getInstance(modalElement)
        bootstrapModal.show()
    }
    const forecastAddModalId = 'modalAddNewForecastPurchaseItem'
    function openModalAddNewForecastItem() {
        // On récupère la modale d'ajout
        const modalElement = document.getElementById(forecastAddModalId)
        modalEventListener = () => {
            const modalBackdrop = document.querySelector('.modal-backdrop')
            if (modalBackdrop) {
                modalBackdrop.style.zIndex = '1050'
            }
            modalElement.style.zIndex = '1500'
            const modalContent = modalElement.querySelector('.modal-content')
            if (modalContent) {
                modalContent.style.zIndex = '1500'
            }
        }
        modalElement.addEventListener('shown.bs.modal', modalEventListener)
        const bootstrapModal = Modal.getInstance(modalElement)
        bootstrapModal.show()
    }
    function onModalClose(modalName) {
        const modalElement = document.getElementById(modalName)
        if (modalEventListener) {
            modalElement.removeEventListener('shown.bs.modal', modalEventListener)
            modalEventListener = null
        }
        const bootstrapModal = Modal.getOrCreateInstance(modalElement)
        bootstrapModal.hide()
    }
    function onFormSubmitted() {
        updateTable()
    }
</script>

<template>
    <Portal to="modals">
        <AppFixedItemAddForm
            :key="`addFixedItem_${formKeys}`"
            :supplier="supplier"
            :modal-id="fixedAddModalId"
            :order="order"
            :options-currency="optionsCurrency"
            :options-unit="optionsUnit"
            @updated="updateTable"
            @closed="() => onModalClose(fixedAddModalId)"
            @submit="onFormSubmitted"/>
        <AppForeCastItemAddForm
            v-if="!fixedFamilies.includes(order.orderFamily)"
            :key="`addForecastItem_${formKeys}`"
            :supplier="supplier"
            :modal-id="forecastAddModalId"
            :order="order"
            :options-currency="optionsCurrency"
            :options-unit="optionsUnit"
            @updated="updateTable"
            @closed="() => onModalClose(forecastAddModalId)"
            @submit="onFormSubmitted"/>
        <AppForeCastUpdateForm
            v-if="!fixedFamilies.includes(order.orderFamily) && showForeCastUpdateForm"
            :key="`updateForecastItem_${formUpdateKeys}`"
            :can-modify="canModifyForm"
            :model-value="storePurchaseOrderItems.currentItem"
            :supplier="supplier"
            :modal-id="forecastUpdateModalId"
            :order="order"
            :options-currency="optionsCurrency"
            :options-unit="optionsUnit"
            @updated="updateTable"
            @closed="() => onModalClose(forecastUpdateModalId)"
            @submit="onFormSubmitted"/>
        <AppFixedUpdateForm
            v-if="showFixedUpdateForm"
            :key="`updateFixedItem_${formUpdateKeys}`"
            :can-modify="canModifyForm"
            :model-value="storePurchaseOrderItems.currentItem"
            :supplier="supplier"
            :modal-id="fixedUpdateModalId"
            :order="order"
            :options-currency="optionsCurrency"
            :options-unit="optionsUnit"
            @updated="updateTable"
            @closed="() => onModalClose(fixedUpdateModalId)"
            @submit="onFormSubmitted"/>
    </Portal>
    <AppCardableTable
        v-if="isLoaded"
        :key="tableKey"
        :should-delete="isPurchaseWriterOrAdmin"
        :should-seek="isPurchaseWriterOrAdmin"
        :current-page="storePurchaseOrderItems.currentPage"
        :fields="fieldsCommande"
        :first-page="storePurchaseOrderItems.firstPage"
        :items="customerOrderItems"
        :last-page="storePurchaseOrderItems.lastPage"
        :next-page="storePurchaseOrderItems.nextPage"
        :pag="storePurchaseOrderItems.pagination"
        :previous-page="storePurchaseOrderItems.previousPage"
        :user="roleUser"
        title
        form="formPurchaseOrderItemsTable"
        @deleted="deletedPurchaseOrderItem"
        @get-page="getPagePurchaseOrders"
        @update="updateItemToUpdate"
        @trier-alphabet="trierAlphabetPurchaseOrderItems"
        @search="searchPurchaseOrders"
        @cancel-search="cancelSearchPurchaseOrderItems">
        <template #title>
            <span>Items de commande {{ order.ref }}</span>
            <button
                v-if="isPurchaseWriterOrAdmin"
                class="btn btn-success btn-float-right m-1"
                @click="openModalAddNewOrderItem">
                Ajouter Item en Ferme
            </button>
            <button
                v-if="!fixedFamilies.includes(order.orderFamily) && isPurchaseWriterOrAdmin"
                class="btn btn-success btn-float-right m-1"
                @click="openModalAddNewForecastItem">
                Ajouter Item en Prévisionnel {{ order.orderFamily }}
            </button>
        </template>
    </AppCardableTable>
</template>
