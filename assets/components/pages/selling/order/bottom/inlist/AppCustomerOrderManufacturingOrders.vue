<script setup>
    import AppSuspense from "../../../../../AppSuspense.vue"
    import {useOfCustomerOrderItemsStore} from "../../../../../../stores/customer/ofCustomerOrderItems"
    import useFetchCriteria from "../../../../../../stores/fetch-criteria/fetchCriteria"
    import {useCustomerOrderItemsStore} from "../../../../../../stores/customer/customerOrderItems"
    import {computed, ref} from "vue"
    import {useRoute} from "vue-router"

    const props = defineProps({
        currentCompany: Object,
        companiesOptions: Array,
        optionsUnit: Array,
        roleUser: String,
        order: Object
    })
    console.log('props', props)
    const route = useRoute()
    const id = Number(route.params.id)
    const currentCompany = ref(props.currentCompany)

    const roleUser = ref(props.roleUser)
    const companiesOptions = computed(() => props.companiesOptions)
    const optionsUnit = computed(() => props.optionsUnit)

    function filterInitialize() {
        ofCustomerOrderCriteria.resetAllFilter()
        ofCustomerOrderCriteria.addFilter('sellingOrder', props.order['@id'])
    }
    const ofCustomerOrderCriteria = useFetchCriteria('manufacturing-orders-criteria')
    filterInitialize()
    const manufacturingOrderStore = useOfCustomerOrderItemsStore()
    await manufacturingOrderStore.fetch(ofCustomerOrderCriteria.getFetchCriteria)

    const currentPlaceOptions = [
        {text: 'rejected', value: 'rejected'},
        {text: 'asked', value: 'asked'},
        {text: 'agreed', value: 'agreed'}
    ]
    const currentBlockerOptions = [
        {text: 'blocked', value: 'blocked'},
        {text: 'enabled', value: 'enabled'},
        {text: 'disabled', value: 'disabled'}
    ]
    const ofFields = [
        {
            label: 'Manufacturing Company',
            name: 'manufacturingCompany',
            options: {
                label: value =>
                    companiesOptions.value.find(option => option.type === value)?.text ?? null,
                options: companiesOptions.value
            },
            trie: false,
            type: 'select'
        },
        {
            label: 'Produit',
            name: 'product',
            filter: true,
            min: true,
            type: 'multiselect-fetch',
            api: '/api/products',
            filteredProperty: 'code',
            max: 1
        },
        {
            label: 'Quantité demandée',
            name: 'quantityRequested',
            filter: true,
            min: true,
            measure: {
                code: {
                    label: 'Code',
                    name: 'quantityRequested.code',
                    options: {
                        label: value =>
                            optionsUnit.value.find(option => option.type === value)?.text ?? null,
                        options: optionsUnit.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'quantityRequested.value',
                    type: 'number',
                    step: 0.1
                }
            },
            trie: true,
            type: 'measure'
        },
        {
            label: 'Current Place',
            name: 'currentPlace',
            options: {
                label: value =>
                    currentPlaceOptions.find(option => option.type === value)?.text ?? null,
                options: currentPlaceOptions
            },
            trie: true,
            type: 'select'
        },
        {
            label: 'Blocker',
            name: 'blocker',
            options: {
                label: value =>
                    currentBlockerOptions.find(option => option.type === value)?.text ?? null,
                options: currentBlockerOptions
            },
            trie: true,
            type: 'select'
        },
        {
            label: 'Date de fabrication',
            name: 'manufacturingDate',
            type: 'date',
            trie: true,
        },
        {
            label: 'Date de livraison',
            name: 'deliveryDate',
            type: 'date',
            trie: true,
        }
    ]

    const ofCustomerOrderItems = computed(() => manufacturingOrderStore.itemsofCustomerOrder)

    async function refreshTableOfCustomerOrders() {
        filterInitialize()
        await manufacturingOrderStore.fetch(ofCustomerOrderCriteria.getFetchCriteria)
    }
    await refreshTableOfCustomerOrders()
    async function deletedOFCustomerOrders(idRemove) {
        await manufacturingOrderStore.remove(idRemove)
        await refreshTableOfCustomerOrders()
    }
    async function getPageOfCustomerOrders(nPage) {
        ofCustomerOrderCriteria.gotoPage(parseFloat(nPage))
        await manufacturingOrderStore.fetch(ofCustomerOrderCriteria.getFetchCriteria)
    }
    async function trierAlphabetOfCustomerOrders(payload) {
        if (payload.name === 'quantityRequested') {
            ofCustomerOrderCriteria.addSort('quantityRequested.value', payload.direction)
            await manufacturingOrderStore.fetch(ofCustomerOrderCriteria.getFetchCriteria)
        } else if (payload.name === 'currentPlace') {
            ofCustomerOrderCriteria.addSort('embState.state', payload.direction)
            await manufacturingOrderStore.fetch(ofCustomerOrderCriteria.getFetchCriteria)
        } else if (payload.name === 'blocker') {
            ofCustomerOrderCriteria.addSort('embBlocker.state', payload.direction)
            await manufacturingOrderStore.fetch(ofCustomerOrderCriteria.getFetchCriteria)
        }
        else {
            ofCustomerOrderCriteria.addSort(payload.name, payload.direction)
            await manufacturingOrderStore.fetch(ofCustomerOrderCriteria.getFetchCriteria)
        }
    }
    async function searchOfCustomerOrders(inputValues) {
        filterInitialize()
        if (inputValues.manufacturingCompany) ofCustomerOrderCriteria.addFilter('manufacturingCompany', inputValues.manufacturingCompany)
        if (inputValues.quantityRequested) ofCustomerOrderCriteria.addFilter('quantityRequested.value', inputValues.quantityRequested.value)
        if (inputValues.quantityRequested) {
            const requestedUnit = units.find(unit => unit['@id'] === inputValues.quantityRequested.code)
            ofCustomerOrderCriteria.addFilter('quantityRequested.code', requestedUnit.code)
        }
        if (inputValues.currentPlace) ofCustomerOrderCriteria.addFilter('embState.state[]', inputValues.currentPlace)
        await manufacturingOrderStore.fetch(ofCustomerOrderCriteria.getFetchCriteria)
    }
    async function cancelSearchOfCustomerOrders() {
        filterInitialize()
        await manufacturingOrderStore.fetch(ofCustomerOrderCriteria.getFetchCriteria)
    }
</script>

<template>
    <AppSuspense>
        <AppCardableTable
            :current-page="manufacturingOrderStore.currentPage"
            :fields="ofFields"
            :first-page="manufacturingOrderStore.firstPage"
            :items="ofCustomerOrderItems"
            :last-page="manufacturingOrderStore.lastPage"
            :next-page="manufacturingOrderStore.nextPage"
            :pag="manufacturingOrderStore.pagination"
            :previous-page="manufacturingOrderStore.previousPage"
            :user="roleUser.toString()"
            :should-delete="false"
            :should-see="false"
            form="ofCustomerOrderTable"
            @deleted="deletedOFCustomerOrders"
            @get-page="getPageOfCustomerOrders"
            @trier-alphabet="trierAlphabetOfCustomerOrders"
            @search="searchOfCustomerOrders"
            @cancel-search="cancelSearchOfCustomerOrders"/>
    </AppSuspense>
</template>
