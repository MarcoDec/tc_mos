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
        roleUser: String
    })

    const route = useRoute()
    const id = Number(route.params.id)
    const currentCompany = ref(props.currentCompany)

    const roleUser = ref(props.roleUser)
    const companiesOptions = computed(() => props.companiesOptions)
    const optionsUnit = computed(() => props.optionsUnit)

    const storeCustomerOrderItems = useCustomerOrderItemsStore()
    const customerOrderCriteria = useFetchCriteria('customer-orders-criteria')
    customerOrderCriteria.addFilter('product', `/api/products/${id}`)
    await storeCustomerOrderItems.fetchAll(customerOrderCriteria.getFetchCriteria)

    customerOrderCriteria.addFilter('company', currentCompany)
    const storeOfCustomerOrderItems = useOfCustomerOrderItemsStore()
    const ofCustomerOrderCriteria = useFetchCriteria('of-customer-orders-criteria')
    customerOrderCriteria.addFilter('order', `/api/selling-orders/${id}`)
    await storeOfCustomerOrderItems.fetch(ofCustomerOrderCriteria.getFetchCriteria)

    const currentPlaceOptions = [
        {text: 'rejected', value: 'rejected'},
        {text: 'asked', value: 'asked'},
        {text: 'agreed', value: 'agreed'}
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
        }
    ]

    ofCustomerOrderCriteria.addFilter('company', currentCompany)
    const ofCustomerOrderItems = computed(() => storeOfCustomerOrderItems.itemsofCustomerOrder)
    async function refreshTableOfCustomerOrders() {
        await storeOfCustomerOrderItems.fetch(ofCustomerOrderCriteria.getFetchCriteria)
    }
    await refreshTableOfCustomerOrders()
    async function deletedOFCustomerOrders(idRemove) {
        await storeOfCustomerOrderItems.remove(idRemove)
        await refreshTableOfCustomerOrders()
    }
    async function getPageOfCustomerOrders(nPage) {
        ofCustomerOrderCriteria.gotoPage(parseFloat(nPage))
        await storeOfCustomerOrderItems.fetch(ofCustomerOrderCriteria.getFetchCriteria)
    }
    async function trierAlphabetOfCustomerOrders(payload) {
        if (payload.name === 'quantityRequested') {
            ofCustomerOrderCriteria.addSort('quantityRequested.value', payload.direction)
            await storeOfCustomerOrderItems.fetch(ofCustomerOrderCriteria.getFetchCriteria)
        } else if (payload.name === 'currentPlace') {
            ofCustomerOrderCriteria.addSort('embState.state', payload.direction)
            await storeOfCustomerOrderItems.fetch(ofCustomerOrderCriteria.getFetchCriteria)
        } else {
            ofCustomerOrderCriteria.addSort(payload.name, payload.direction)
            await storeOfCustomerOrderItems.fetch(ofCustomerOrderCriteria.getFetchCriteria)
        }
    }
    async function searchOfCustomerOrders(inputValues) {
        ofCustomerOrderCriteria.resetAllFilter()
        ofCustomerOrderCriteria.addFilter('company', currentCompany)
        if (inputValues.manufacturingCompany) ofCustomerOrderCriteria.addFilter('manufacturingCompany', inputValues.manufacturingCompany)
        if (inputValues.quantityRequested) ofCustomerOrderCriteria.addFilter('quantityRequested.value', inputValues.quantityRequested.value)
        if (inputValues.quantityRequested) {
            const requestedUnit = units.find(unit => unit['@id'] === inputValues.quantityRequested.code)
            ofCustomerOrderCriteria.addFilter('quantityRequested.code', requestedUnit.code)
        }
        if (inputValues.currentPlace) ofCustomerOrderCriteria.addFilter('embState.state[]', inputValues.currentPlace)
        await storeOfCustomerOrderItems.fetch(ofCustomerOrderCriteria.getFetchCriteria)
    }
    async function cancelSearchOfCustomerOrders() {
        ofCustomerOrderCriteria.resetAllFilter()
        ofCustomerOrderCriteria.addFilter('company', currentCompany)
        await storeOfCustomerOrderItems.fetch(ofCustomerOrderCriteria.getFetchCriteria)
    }
</script>

<template>
    <AppSuspense>
        <AppCardableTable
            :current-page="storeOfCustomerOrderItems.currentPage"
            :fields="ofFields"
            :first-page="storeOfCustomerOrderItems.firstPage"
            :items="ofCustomerOrderItems"
            :last-page="storeOfCustomerOrderItems.lastPage"
            :next-page="storeOfCustomerOrderItems.nextPage"
            :pag="storeOfCustomerOrderItems.pagination"
            :previous-page="storeOfCustomerOrderItems.previousPage"
            :user="roleUser.toString()"
            form="ofCustomerOrderTable"
            @deleted="deletedOFCustomerOrders"
            @get-page="getPageOfCustomerOrders"
            @trier-alphabet="trierAlphabetOfCustomerOrders"
            @search="searchOfCustomerOrders"
            @cancel-search="cancelSearchOfCustomerOrders"/>
    </AppSuspense>
</template>
