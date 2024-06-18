<script setup>
    import {computed, ref} from 'vue'
    import {useCustomerOrderItemsStore} from '../../../../../stores/customer/customerOrderItems'
    import {useRoute} from 'vue-router'
    import useUnitsStore from '../../../../../stores/unit/units'
    import AppTab from '../../../../../components/tab/AppTab.vue'
    import AppTabs from '../../../../../components/tab/AppTabs.vue'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import useUser from '../../../../../stores/security'
    import useOptions from '../../../../../stores/option/options'
    import {useCustomerAddressStore} from '../../../../../stores/customer/customerAddress'
    import {useCustomerOrderStore} from '../../../../../stores/customer/customerOrder'
    import {useCurrenciesStore} from '../../../../../stores/currency/currencies'
    import {useCustomersStore} from '../../../../../stores/customer/customers'
    import AppCustomerOrderManufacturingOrders from "./inlist/AppCustomerOrderManufacturingOrders.vue"
    import AppUnderDevelopment from "../../../../gui/AppUnderDevelopment.vue"
    import AppCustomerOrderExpedition from "./inlist/AppCustomerOrderExpedition.vue"
    import AppCustomerOrderBill from "./inlist/AppCustomerOrderBill.vue"

    const route = useRoute()
    const id = Number(route.params.id)

    const fetchUnitOptions = useOptions('units')
    await fetchUnitOptions.fetchOp()
    const optionsUnit = computed(() =>
        fetchUnitOptions.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))
    const fetchcompaniesOptions = useOptions('companies')
    await fetchcompaniesOptions.fetchOp()
    const companiesOptions = computed(() =>
        fetchcompaniesOptions.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))
    console.log("companiesOptions", companiesOptions.value)
    const fetchCurrenciesOptions = useOptions('currencies')
    await fetchCurrenciesOptions.fetchOp()
    const currenciesOptions = computed(() =>
        fetchCurrenciesOptions.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))
    const storeCustomerOrder = useCustomerOrderStore()
    await storeCustomerOrder.fetchById(id)
    console.log('storeCustomerorder', storeCustomerOrder.customerOrder)
    const customer = computed(() => storeCustomerOrder.customer)

    const storeCustomerAddress = useCustomerAddressStore()
    await storeCustomerAddress.fetchDeliveryAddress()


    await storeCustomerAddress.fetchBillingAddress()

    const storeCustomers = useCustomersStore()
    await storeCustomers.fetch()

    const fetchUser = useUser()
    const currentCompany = fetchUser.company
    const isSellingWriterOrAdmin = fetchUser.isSellingWriter || fetchUser.isSellingAdmin
    const roleuser = ref(isSellingWriterOrAdmin ? 'writer' : 'reader')

    const storeUnits = useUnitsStore()
    await storeUnits.fetch()
    const units = storeUnits.units

    const storeCurrencies = useCurrenciesStore()
    await storeCurrencies.fetch()
    const currencies = storeCurrencies.currencies
    //CustomerOrdersTable
    const storeCustomerOrderItems = useCustomerOrderItemsStore()
    const customerOrderCriteria = useFetchCriteria('customer-orders-criteria')
    customerOrderCriteria.addFilter('product', `/api/products/${id}`)
    await storeCustomerOrderItems.fetchAll(customerOrderCriteria.getFetchCriteria)

    customerOrderCriteria.addFilter('company', currentCompany)

    async function refreshTableCustomerOrders() {
        await storeCustomerOrderItems.fetchAll(customerOrderCriteria.getFetchCriteria)
    }
    await refreshTableCustomerOrders()
</script>

<template>
    <AppTabs id="gui-form-create" class="display-block-important">
        <AppTab id="gui-start-files" active icon="industry" title="OF" tabs="gui-form-create">
            <AppCustomerOrderManufacturingOrders
                :order="storeCustomerOrder.customerOrder"
                :companies-options="companiesOptions"
                :current-company="currentCompany"
                :options-unit="optionsUnit"
                :role-user="roleuser"
            />
        </AppTab>
        <AppTab id="gui-start-quality" icon="clipboard" title="Bons de préparation" tabs="gui-form-create">
            <AppUnderDevelopment/>
        </AppTab>
        <AppTab id="gui-start-purchase-logistics" icon="truck" title="BL" tabs="gui-form-create">
            <AppCustomerOrderExpedition
                :order="storeCustomerOrder.customerOrder"
                :current-company="currentCompany"
                :customer="customer"
                :customer-address-store="storeCustomerAddress"
                :store-customer-order="storeCustomerOrder"
                :role-user="roleuser"
            />
        </AppTab>
        <AppTab id="gui-start-accounting" icon="file-invoice-dollar" title="Factures" tabs="gui-form-create">
            <AppCustomerOrderBill
                :order="storeCustomerOrder.customerOrder"
                :current-company="currentCompany"
                :currencies-options="currenciesOptions"
                :store-customer-address="storeCustomerAddress"
                customer="customer"
                currencies="currencies"
                :user-role="roleuser"/>
        </AppTab>
        <AppTab id="gui-start-addresses" icon="chart-line" title="Qualité" tabs="gui-form-create">
            <AppUnderDevelopment/>
        </AppTab>
    </AppTabs>
</template>

<style scoped>
.display-block-important {
    display: block !important;
}
</style>
