<script setup>
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import useUser from '../../../../../stores/security'
    import {computed, nextTick, ref} from 'vue'
    import {useCustomerOrderItemsStore} from '../../../../../stores/customer/customerOrderItems'
    import AppFixedItemAddForm from './itemsForms/AppFixedItemAddForm.vue'
    import AppForeCastItemAddForm from './itemsForms/AppForeCastItemAddForm.vue'
    import useOptions from '../../../../../stores/option/options'
    import AppForeCastUpdateForm from './itemsForms/AppForeCastUpdateForm.vue'
    import {Modal} from 'bootstrap'
    import AppFixedUpdateForm from "./itemsForms/AppFixedUpdateForm.vue"

    const props = defineProps({
        order: {default: () => ({}), required: true, type: Object},
        customer: {default: () => ({}), required: true, type: Object}
    })
    //Si l'utilisateur courant a les droits admin ou writer, il peut ajouter/modifier et supprimer des items de commande
    const showForeCastUpdateForm = ref(false)
    const showFixedUpdateForm = ref(false)
    //region initialisation des constantes et variables
    const fetchUser = useUser()
    const isLoaded = ref(false)
    const isSellingAdmin = fetchUser.isSellingAdmin
    const isSellingWriterOrAdmin = fetchUser.isSellingWriter || isSellingAdmin
    const roleUser = ref(isSellingWriterOrAdmin ? 'writer' : 'reader')
    const fetchUnitOptions = useOptions('units')
    const fetchCurrencyOptions = useOptions('currencies')
    const formKeys = ref(0)
    const formUpdateKeys = ref(0)
    const promises = []
    promises.push(fetchUnitOptions.fetchOp())
    promises.push(fetchCurrencyOptions.fetchOp())
    await Promise.all(promises).then(() => {
        formKeys.value++
    })
    const optionsUnit = computed(() => fetchUnitOptions.getOptionsMap())
    const optionsCurrency = computed(() => fetchCurrencyOptions.getOptionsMap())
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
                        label: value => fetchUnitOptions.getLabelFromCode(value),
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
                        label: value => fetchUnitOptions.getLabelFromCode(value),
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
                        label: value => fetchCurrencyOptions.getLabelFromCode(value),
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
        //On demande une confirmation avant de supprimer
        const confirmation = window.confirm('Voulez-vous vraiment supprimer cet item de commande ?')
        if (!confirmation) return
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
        await storeCustomerOrderItems.fetchAll(customerOrderItemsCriteria.getFetchCriteria)
    }
    async function updateTable() {
        await refreshTableCustomerOrders()
        showForeCastUpdateForm.value = false
        tableKey.value++
    }
    //endregion
    //chargement des données
    addPermanentFilters()
    await storeCustomerOrderItems.fetchAll(customerOrderItemsCriteria.getFetchCriteria)
    isLoaded.value = true
    //endregion
    const canModifyForm = ref(true)
    function updateItemToUpdate(item) {
        storeCustomerOrderItems.setCurrentUnitOptions(optionsUnit.value)
        storeCustomerOrderItems.setCurrentCurrencyOptions(optionsCurrency.value)
        storeCustomerOrderItems.setCurrentItem(item)
        formUpdateKeys.value++
        // console.log('updatedStore', storePurchaseOrderItems.currentUnitOptions, storePurchaseOrderItems.currentCurrencyOptions)
        // Si l'item n'est pas à l'état "draft" et que l'utilisateur n'est pas "administrateur" alors on ne peut pas modifier l'item
        if (item.state !== 'draft' && !isSellingAdmin && !item.isForecast) {
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
    <AppFixedItemAddForm
        v-if="isLoaded"
        :key="`addFixedItem_${formKeys}`"
        :customer="customer"
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
        :customer="customer"
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
        :model-value="storeCustomerOrderItems.currentItem"
        :customer="customer"
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
        :model-value="storeCustomerOrderItems.currentItem"
        :customer="customer"
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
        :should-delete="isSellingWriterOrAdmin"
        :should-see="isSellingWriterOrAdmin"
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
        @update="updateItemToUpdate"
        @search="searchCustomerOrders"
        @cancel-search="cancelSearchCustomerOrders">
        <template #title>
            <span>Items de commande {{ order.ref }}</span>
            <button
                v-if="isSellingWriterOrAdmin"
                class="btn btn-success btn-float-right m-1"
                @click="openModalAddNewOrderItem">
                Ajouter Item en Ferme
            </button>
            <button
                v-if="!fixedFamilies.includes(order.orderFamily) && isSellingWriterOrAdmin"
                class="btn btn-success btn-float-right m-1"
                @click="openModalAddNewForecastItem">
                Ajouter Item en Prévisionnel {{ order.orderFamily }}
            </button>
        </template>
    </AppCardableTable>
</template>
