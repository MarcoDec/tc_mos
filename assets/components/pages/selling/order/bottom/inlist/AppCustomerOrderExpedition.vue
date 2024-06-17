<script setup>
    import AppSuspense from "../../../../../AppSuspense.vue"
    import {computed, ref} from "vue"
    import {useBlCustomerOrderItemsStore} from "../../../../../../stores/customer/blCustomerOrderItems"
    import useFetchCriteria from "../../../../../../stores/fetch-criteria/fetchCriteria"

    const props = defineProps({
        order: Object,
        currentCompany: Object,
        customer: Object,
        storeCustomerOrder: Object,
        customerAddressStore: Object,
        roleUser: String
    })
    const storeCustomerOrder = ref(props.storeCustomerOrder)
    const addressCustomer = {
        deliveryAddress: props.order.destination
    }
    const userRole = ref(props.roleUser)
    console.log('roleUser', userRole.value)
    const currentCompany = ref(props.currentCompany)
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
        await storeCustomerOrder.updateSellingOrder(payload)
    }

    //BlCustomerOrderTable

    const storeBlCustomerOrderItems = useBlCustomerOrderItemsStore()
    const blCustomerOrderCriteria = useFetchCriteria('bl-customer-orders-criteria')
    await storeBlCustomerOrderItems.fetch(blCustomerOrderCriteria.getFetchCriteria)

    blCustomerOrderCriteria.addFilter('company', currentCompany)
    const blCustomerOrderItems = computed(() => storeBlCustomerOrderItems.itemsBlCustomerOrder)
    async function refreshTableBlCustomerOrders() {
        await storeBlCustomerOrderItems.fetch(blCustomerOrderCriteria.getFetchCriteria)
    }
    await refreshTableBlCustomerOrders()

    async function deletedBlCustomerOrders(idRemove) {
        await storeBlCustomerOrderItems.remove(idRemove)
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
        } else {
            blCustomerOrderCriteria.addSort(payload.name, payload.direction)
            await storeBlCustomerOrderItems.fetch(blCustomerOrderCriteria.getFetchCriteria)
        }
    }
    async function searchBlCustomerOrders(inputValues) {
        blCustomerOrderCriteria.resetAllFilter()
        blCustomerOrderCriteria.addFilter('company', currentCompany)
        if (inputValues.number) blCustomerOrderCriteria.addFilter('ref', inputValues.number)
        if (inputValues.departureDate) blCustomerOrderCriteria.addFilter('date', inputValues.departureDate)
        if (inputValues.currentPlace) blCustomerOrderCriteria.addFilter('embState.state[]', inputValues.currentPlace)
        await storeBlCustomerOrderItems.fetch(blCustomerOrderCriteria.getFetchCriteria)
    }
    async function cancelSearchBlCustomerOrders() {
        blCustomerOrderCriteria.resetAllFilter()
        blCustomerOrderCriteria.addFilter('company', currentCompany)
        //await storeBlCustomerOrderItems.fetch(ofCustomerOrderCriteria.getFetchCriteria)
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
            :fields="blFields"
            :first-page="storeBlCustomerOrderItems.firstPage"
            :items="blCustomerOrderItems"
            :last-page="storeBlCustomerOrderItems.lastPage"
            :next-page="storeBlCustomerOrderItems.nextPage"
            :pag="storeBlCustomerOrderItems.pagination"
            :previous-page="storeBlCustomerOrderItems.previousPage"
            :user="userRole.toString()"
            form="blCustomerOrderTable"
            @deleted="deletedBlCustomerOrders"
            @get-page="getPageBlCustomerOrders"
            @trier-alphabet="trierAlphabetBlCustomerOrders"
            @search="searchBlCustomerOrders"
            @cancel-search="cancelSearchBlCustomerOrders"/>
    </AppSuspense>
</template>

<style scoped>

</style>