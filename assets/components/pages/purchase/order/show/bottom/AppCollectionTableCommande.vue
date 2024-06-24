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
    const tableKey = ref(0)

    const stateOptions = [
        {text: 'partially_delivered', value: 'partially_delivered'},
        {text: 'delivered', value: 'delivered'},
        {text: 'agreed', value: 'agreed'}
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
        if (inputValues.state) purchaseOrderItemsCriteria.addFilter('embState.state', inputValues.state)
        if (inputValues.notes) purchaseOrderItemsCriteria.addFilter('notes', inputValues.notes)
        if (inputValues.product && !inputValues.component) {
            await storePurchaseOrderItems.fetchAllProduct(purchaseOrderItemsCriteria.getFetchCriteria)
            return
        }
        if (!inputValues.product && inputValues.component) {
            await storePurchaseOrderItems.fetchAllComponent(purchaseOrderItemsCriteria.getFetchCriteria)
            return
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
        //On réinitialise les données du formulaire
        document.getElementById('formPurchaseOrderItemsTable').reset()
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
                const modalElement = document.getElementById('modalUpdateForecastItem')
                const bootstrapModal = Modal.getInstance(modalElement)
                bootstrapModal.show()
            })
            return
        }
        showFixedUpdateForm.value = true
        nextTick(() => {
            const modalElement = document.getElementById('modalUpdateFixedItem')
            const bootstrapModal = Modal.getInstance(modalElement)
            bootstrapModal.show()
        })
    }
    function openModalAddNewOrderItem() {
        // On récupère la modale d'ajout
        const modalElement = document.getElementById('modalAddNewFixedItem')
        const bootstrapModal = Modal.getInstance(modalElement)
        bootstrapModal.show()
    }
    function openModalAddNewForecastItem() {
        // On récupère la modale d'ajout
        const modalElement = document.getElementById('modalAddNewForecastItem')
        const bootstrapModal = Modal.getInstance(modalElement)
        bootstrapModal.show()
    }
    function onFormSubmitted() {
        updateTable()
    }
</script>

<template>
    <AppSuspense>
        <AppFixedItemAddForm
            v-if="isLoaded"
            :key="`addFixedItem_${formKeys}`"
            :supplier="supplier"
            modal-id="modalAddNewFixedItem"
            :order="order"
            :options-currency="optionsCurrency"
            :options-unit="optionsUnit"
            @updated="updateTable"
            @closed="() => console.log('fixed add form closed')"
            @submit="onFormSubmitted"/>
        <AppForeCastItemAddForm
            v-if="isLoaded && !fixedFamilies.includes(order.orderFamily)"
            :key="`addForecastItem_${formKeys}`"
            :supplier="supplier"
            modal-id="modalAddNewForecastItem"
            :order="order"
            :options-currency="optionsCurrency"
            :options-unit="optionsUnit"
            @updated="updateTable"
            @closed="() => console.log('forecast add form closed')"
            @submit="onFormSubmitted"/>
        <AppForeCastUpdateForm
            v-if="isLoaded && !fixedFamilies.includes(order.orderFamily) && showForeCastUpdateForm"
            :key="`updateForecastItem_${formUpdateKeys}`"
            :can-modify="canModifyForm"
            :model-value="storePurchaseOrderItems.currentItem"
            :supplier="supplier"
            modal-id="modalUpdateForecastItem"
            :order="order"
            :options-currency="optionsCurrency"
            :options-unit="optionsUnit"
            @updated="updateTable"
            @closed="() => console.log('forecast update form closed')"
            @submit="onFormSubmitted"/>
        <AppFixedUpdateForm
            v-if="isLoaded && showFixedUpdateForm"
            :key="`updateFixedItem_${formUpdateKeys}`"
            :can-modify="canModifyForm"
            :model-value="storePurchaseOrderItems.currentItem"
            :supplier="supplier"
            modal-id="modalUpdateFixedItem"
            :order="order"
            :options-currency="optionsCurrency"
            :options-unit="optionsUnit"
            @updated="updateTable"
            @closed="() => console.log('fixed update form closed')"
            @submit="onFormSubmitted"/>
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
    </AppSuspense>
</template>
