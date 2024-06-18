<script setup>
    import AppSuspense from "../../../../../AppSuspense.vue"
    import {computed, ref} from "vue"
    import {useBlCustomerOrderItemsStore} from "../../../../../../stores/customer/blCustomerOrderItems"
    import useFetchCriteria from "../../../../../../stores/fetch-criteria/fetchCriteria"

    const props = defineProps({
        order: {default: () => ({}), type: Object},
        customer: {default: () => ({}), type: Object},
        storeCustomerOrder: {default: () => ({}), type: Object},
        customerAddressStore: {default: () => ({}), type: Object},
        roleUser: {default: '', type: String},
        currenciesOptions: {default: () => [], type: Array}
    })
    const storeCustomerOrder = ref(props.storeCustomerOrder)
    const addressCustomer = {
        deliveryAddress: props.order.destination
    }
    const userRole = ref(props.roleUser)
    console.log('roleUser', userRole.value)
    const storeCustomerAddress = props.customerAddressStore
    const deliveryAddressesOption = computed(() => storeCustomerAddress.deliveryAddressesOption)
    const filteredDeliveryAddressesOptions = deliveryAddressesOption.value.filter(option => option.customer === props.customer)
    const blFields = [
        {
            label: 'Adresse de livraison',
            name: 'deliveryAddress',
            options: {
                label: value =>
                    filteredDeliveryAddressesOptions.find(option => option.type === value)?.text ?? null,
                options: filteredDeliveryAddressesOptions
            },
            type: 'select'
        }
    ]
    const optionsCurrentPlace = [
        {text: 'Brouillon', value: 'draft'},
        {text: 'Envoyé', value: 'sent'},
        {text: 'Annulé', value: 'Rejected'},
        {text: 'Accusé réception reçu', value: 'acknowledgment_of_receipt'}
    ]
    const blListFields = [
        {
            label: 'Date',
            name: 'date',
            type: 'date',
            trie: true
        },
        {
            label: 'numéro',
            name: 'ref',
            type: 'text',
            trie: true
        },
        {
            label: 'Surcharge',
            name: 'freightSurcharge',
            type: 'measure',
            trie: true,
            filter: true,
            measure: {
                code: {
                    label: 'Code',
                    name: 'freightSurcharge.code',
                    options: {
                        label: value =>
                            props.currenciesOptions.find(option => option.type === value)?.text ?? null,
                        options: props.currenciesOptions
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'freightSurcharge.value',
                    type: 'number',
                    step: 0.0001
                }
            }
        },
        {
            label: 'statut',
            name: 'currentPlace',
            type: 'select',
            trie: true,
            options: {
                label: value =>
                    optionsCurrentPlace.find(option => option.value === value)?.text ?? null,
                options: optionsCurrentPlace
            }
        },
        {
            label: 'Facture',
            name: 'bill',
            type: 'multiselect-fetch',
            trie: true,
            api: '/api/bills',
            filteredProperty: 'ref',
            max: 1
        },
        {
            label: 'Non facturable?',
            name: 'nonBillable',
            type: 'boolean',
            trie: false,
            filter: false
        }
    ]
    const updateAddressCustomerOrder = {}
    async function updateAddress(newAddress) {
        updateAddressCustomerOrder.value = {
            destination: newAddress.deliveryAddress
        }
    }
    async function updateCustomerOrder(){
        const payload = {
            id,
            SellingOrder: {destination: updateAddressCustomerOrder.value.destination}
        }
        await storeCustomerOrder.value.updateSellingOrder(payload)
    }

    //BlCustomerOrderTable

    const storeBlCustomerOrderItems = useBlCustomerOrderItemsStore()
    const blCustomerOrderCriteria = useFetchCriteria('bl-customer-orders-criteria')
    const blCustomerOrderItems = computed(() => storeBlCustomerOrderItems.itemsBlCustomerOrder)
    function initializeFilter() {
        blCustomerOrderCriteria.resetAllFilter()
        blCustomerOrderCriteria.addFilter('sellingOrder', props.order['@id'])
    }
    initializeFilter()
    await storeBlCustomerOrderItems.fetch(blCustomerOrderCriteria.getFetchCriteria)

    async function refreshTableBlCustomerOrders() {
        await storeBlCustomerOrderItems.fetch(blCustomerOrderCriteria.getFetchCriteria)
    }

    async function deletedBlCustomerOrders(idRemove) {
        await storeBlCustomerOrderItems.remove(idRemove)
        initializeFilter()
        await refreshTableBlCustomerOrders()
    }
    async function getPageBlCustomerOrders(nPage) {
        blCustomerOrderCriteria.gotoPage(parseFloat(nPage))
        await storeBlCustomerOrderItems.fetch(blCustomerOrderCriteria.getFetchCriteria)
    }
    async function trierAlphabetBlCustomerOrders(payload) {
        if (payload.name === 'number') {
            blCustomerOrderCriteria.addSort('ref', payload.direction)
            await storeBlCustomerOrderItems.fetch(blCustomerOrderCriteria.getFetchCriteria)
        } else if (payload.name === 'currentPlace') {
            blCustomerOrderCriteria.addSort('embState.state', payload.direction)
            await storeBlCustomerOrderItems.fetch(blCustomerOrderCriteria.getFetchCriteria)
        } else if (payload.name === 'departureDate') {
            blCustomerOrderCriteria.addSort('date', payload.direction)
            await storeBlCustomerOrderItems.fetch(blCustomerOrderCriteria.getFetchCriteria)
        } else if (payload.name === 'freightSurcharge') {
            blCustomerOrderCriteria.addSort('freightSurcharge.value', payload.direction)
            await storeBlCustomerOrderItems.fetch(blCustomerOrderCriteria.getFetchCriteria)
        } else if (payload.name === 'bill') {
            blCustomerOrderCriteria.addSort('bill.ref', payload.direction)
            await storeBlCustomerOrderItems.fetch(blCustomerOrderCriteria.getFetchCriteria)
        } else {
            blCustomerOrderCriteria.addSort(payload.name, payload.direction)
            await storeBlCustomerOrderItems.fetch(blCustomerOrderCriteria.getFetchCriteria)
        }
    }
    async function searchBlCustomerOrders(inputValues) {
        initializeFilter()
        if (inputValues.ref) blCustomerOrderCriteria.addFilter('ref', inputValues.ref)
        if (inputValues.date) blCustomerOrderCriteria.addFilter('date', inputValues.date)
        if (inputValues.bill) blCustomerOrderCriteria.addFilter('bill', inputValues.bill)
        // if (inputValues.nonBillable) blCustomerOrderCriteria.addFilter('nonBillable[]', inputValues.nonBillable)
        if (inputValues.freightSurcharge) blCustomerOrderCriteria.addFilter('freightSurcharge.value[]', inputValues.freightSurcharge.value)
        if (inputValues.currentPlace) blCustomerOrderCriteria.addFilter('embState.state[]', inputValues.currentPlace)
        await storeBlCustomerOrderItems.fetch(blCustomerOrderCriteria.getFetchCriteria)
    }
    async function cancelSearchBlCustomerOrders() {
        initializeFilter()
        await storeBlCustomerOrderItems.fetch(blCustomerOrderCriteria.getFetchCriteria)
    }
</script>

<template>
    <AppCardShow
        id="addBL"
        :component-attribute="addressCustomer"
        :fields="blFields"
        @update:model-value="updateAddress"
        @update="updateCustomerOrder"/>
    <AppSuspense>
        <AppCardableTable
            :current-page="storeBlCustomerOrderItems.currentPage"
            :fields="blListFields"
            :first-page="storeBlCustomerOrderItems.firstPage"
            :items="blCustomerOrderItems"
            :last-page="storeBlCustomerOrderItems.lastPage"
            :next-page="storeBlCustomerOrderItems.nextPage"
            :pag="storeBlCustomerOrderItems.pagination"
            :previous-page="storeBlCustomerOrderItems.previousPage"
            :user="userRole.toString()"
            :should-delete="false"
            :should-see="false"
            form="blCustomerOrderTable"
            @deleted="deletedBlCustomerOrders"
            @get-page="getPageBlCustomerOrders"
            @trier-alphabet="trierAlphabetBlCustomerOrders"
            @search="searchBlCustomerOrders"
            @cancel-search="cancelSearchBlCustomerOrders"/>
    </AppSuspense>
</template>
