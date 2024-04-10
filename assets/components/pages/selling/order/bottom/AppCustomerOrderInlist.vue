<script setup>
    import {computed, ref} from 'vue'
    import {useBlCustomerOrderItemsStore} from '../../../../../stores/customer/blCustomerOrderItems'
    import {useCustomerOrderItemsStore} from '../../../../../stores/customer/customerOrderItems'
    import {useFacturesCustomerOrderItemsStore} from '../../../../../stores/customer/facturesCustomerOrderItems'
    import {useOfCustomerOrderItemsStore} from '../../../../../stores/customer/ofCustomerOrderItems'
    import {useRoute} from 'vue-router'
    import useUnitsStore from '../../../../../stores/unit/units'
    import AppTab from '../../../../../components/tab/AppTab.vue'
    import AppTabs from '../../../../../components/tab/AppTabs.vue'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import useUser from '../../../../../stores/security'
    import AppSuspense from '../../../../../components/AppSuspense.vue'
    import useOptions from '../../../../../stores/option/options'
    import {useCustomerAddressStore} from '../../../../../stores/customer/customerAddress'
    import {useCustomerOrderStore} from '../../../../../stores/customer/customerOrder'
    import {useCurrenciesStore} from '../../../../../stores/currency/currencies'
    import {useCustomersStore} from '../../../../../stores/customer/customers'

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
    const fetchCurrenciesOptions = useOptions('currencies')
    await fetchCurrenciesOptions.fetchOp()
    const currenciesOptions = computed(() =>
        fetchCurrenciesOptions.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))
    const currentPlaceOptions = [
        {text: 'rejected', value: 'rejected'},
        {text: 'asked', value: 'asked'},
        {text: 'agreed', value: 'agreed'}
    ]
    const blCurrentPlaceOptions = [
        {text: 'rejected', value: 'rejected'},
        {text: 'asked', value: 'asked'},
        {text: 'agreed', value: 'agreed'},
        {text: 'closed', value: 'closed'}
    ]
    const facturesCurrentPlaceOptions = [
        {text: 'billed', value: 'billed'},
        {text: 'draft', value: 'draft'},
        {text: 'partially_paid', value: 'partially_paid'},
        {text: 'paid', value: 'paid'}
    ]
    const storeCustomerorder = useCustomerOrderStore()
    await storeCustomerorder.fetchById(id)
    const customer = computed(() => storeCustomerorder.customer)
    const adressCustomer = {
        AdresseLivraison: storeCustomerorder.customerOrder.destination
    }
    const billingAddressesCustomer = {
        AdresseFacturation: storeCustomerorder.customerOrder.billedTo
    }

    const storeCustomerAddress = useCustomerAddressStore()
    await storeCustomerAddress.fetchDeliveryAddress()
    const deliveryAddressesOption = computed(() => storeCustomerAddress.deliveryAddressesOption)
    const filteredDeliveryAddressesOptions = deliveryAddressesOption.value.filter(option => option.customer === customer.value)

    await storeCustomerAddress.fetchBillingAddress()
    const billingAddressesOption = computed(() => storeCustomerAddress.billingAddressesOption)
    const filteredBillingAddressesOptions = billingAddressesOption.value.filter(option => option.customer === customer.value)

    const storeCustomers = useCustomersStore()
    await storeCustomers.fetch()
    const BLfields = [
        {
            label: 'Adresse de livraison',
            name: 'AdresseLivraison',
            options: {
                label: value =>
                    filteredDeliveryAddressesOptions.find(option => option.type === value)?.text ?? null,
                options: filteredDeliveryAddressesOptions
            },
            type: 'select'
        }
    ]
    const Facturesfields = [
        {
            label: 'Adresse de facturation',
            name: 'AdresseFacturation',
            options: {
                label: value =>
                    filteredBillingAddressesOptions.find(option => option.type === value)?.text ?? null,
                options: filteredBillingAddressesOptions
            },
            type: 'select'
        }
    ]
    const OFfields = [
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
    const fieldsBL = [
        {label: 'Number', name: 'number', trie: true, type: 'number'},
        {label: 'Departure Date', name: 'departureDate', trie: true, type: 'date'},
        {
            label: 'Current Place',
            name: 'currentPlace',
            options: {
                label: value =>
                    blCurrentPlaceOptions.find(option => option.type === value)?.text ?? null,
                options: blCurrentPlaceOptions
            },
            trie: true,
            type: 'select'
        }
    ]
    const fieldsFactures = [
        {label: 'Invoice Number', name: 'invoiceNumber', trie: true, type: 'text'},
        {
            label: 'Total HT',
            name: 'totalHT',
            filter: true,
            measure: {
                code: {
                    label: 'Code',
                    name: 'totalHT.code',
                    options: {
                        label: value =>
                            currenciesOptions.value.find(option => option.type === value)?.text ?? null,
                        options: currenciesOptions.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'totalHT.value',
                    type: 'number',
                    step: 0.1
                }
            },
            trie: true,
            type: 'measure'
        },
        {
            label: 'Total TTC',
            name: 'totalTTC',
            filter: true,
            measure: {
                code: {
                    label: 'Code',
                    name: 'totalTTC.code',
                    options: {
                        label: value =>
                            currenciesOptions.value.find(option => option.type === value)?.text ?? null,
                        options: currenciesOptions.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'totalTTC.value',
                    type: 'number',
                    step: 0.1
                }
            },
            trie: true,
            type: 'measure'
        },
        {
            label: 'Vta',
            name: 'vta',
            filter: true,
            measure: {
                code: {
                    label: 'Code',
                    name: 'vta.code',
                    options: {
                        label: value =>
                            currenciesOptions.value.find(option => option.type === value)?.text ?? null,
                        options: currenciesOptions.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'vta.value',
                    type: 'number',
                    step: 0.1
                }
            },
            trie: true,
            type: 'measure'
        },
        {label: 'Invoice Date', name: 'invoiceDate', trie: true, type: 'date'},
        {label: 'Deadline Date', name: 'deadlineDate', trie: true, type: 'date'},
        {
            label: 'Current Place',
            name: 'currentPlace',
            options: {
                label: value =>
                    facturesCurrentPlaceOptions.find(option => option.type === value)?.text ?? null,
                options: facturesCurrentPlaceOptions
            },
            trie: true,
            type: 'select'
        }
    ]

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

    //ofCustomerOrderTable
    const storeOfCustomerOrderItems = useOfCustomerOrderItemsStore()
    const ofCustomerOrderCriteria = useFetchCriteria('of-customer-orders-criteria')
    customerOrderCriteria.addFilter('order', `/api/selling-orders/${id}`)
    await storeOfCustomerOrderItems.fetch(ofCustomerOrderCriteria.getFetchCriteria)

    ofCustomerOrderCriteria.addFilter('company', currentCompany)
    const ofcustomerOrderItems = computed(() => storeOfCustomerOrderItems.itemsofCustomerOrder)
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
        await storeBlCustomerOrderItems.fetch(ofCustomerOrderCriteria.getFetchCriteria)
    }
    const updateAdressCustomerOrder = {}
    async function updateAdress(newAdress) {
        updateAdressCustomerOrder.value = {
            destination: newAdress.AdresseLivraison
        }
    }
    async function updateCustomerOrder(){
        const payload = {
            id,
            SellingOrder: {destination: updateAdressCustomerOrder.value.destination}
        }
        await storeCustomerorder.updateSellingOrder(payload)
    }

    // factures

    const storeFacturesCustomerOrderItems = useFacturesCustomerOrderItemsStore()
    const facturesCustomerOrderCriteria = useFetchCriteria('factures-customer-orders-criteria')
    await storeFacturesCustomerOrderItems.fetch(facturesCustomerOrderCriteria.getFetchCriteria)

    facturesCustomerOrderCriteria.addFilter('company', currentCompany)
    const facturesCustomerOrderItems = computed(() => storeFacturesCustomerOrderItems.itemsFactureCustomerOrder)
    async function refreshTableFacturesCustomerOrders() {
        await storeFacturesCustomerOrderItems.fetch(facturesCustomerOrderCriteria.getFetchCriteria)
    }
    await refreshTableFacturesCustomerOrders()

    async function deletedFacturesCustomerOrders(idRemove) {
        await storeFacturesCustomerOrderItems.remove(idRemove)
        await refreshTableFacturesCustomerOrders()
    }
    async function getPageFacturesCustomerOrders(nPage) {
        facturesCustomerOrderCriteria.gotoPage(parseFloat(nPage))
        await storeFacturesCustomerOrderItems.fetch(facturesCustomerOrderCriteria.getFetchCriteria)
    }
    async function trierAlphabetFacturesCustomerOrders(payload) {
        if (payload.name === 'invoiceNumber') {
            facturesCustomerOrderCriteria.addSort('ref', payload.direction)
            await storeFacturesCustomerOrderItems.fetch(facturesCustomerOrderCriteria.getFetchCriteria)
        } else if (payload.name === 'currentPlace') {
            facturesCustomerOrderCriteria.addSort('embState.state', payload.direction)
            await storeFacturesCustomerOrderItems.fetch(facturesCustomerOrderCriteria.getFetchCriteria)
        } else if (payload.name === 'totalHT') {
            facturesCustomerOrderCriteria.addSort('exclTax.value', payload.direction)
            await storeFacturesCustomerOrderItems.fetch(facturesCustomerOrderCriteria.getFetchCriteria)
        } else if (payload.name === 'totalTTC') {
            facturesCustomerOrderCriteria.addSort('inclTax.value', payload.direction)
            await storeFacturesCustomerOrderItems.fetch(facturesCustomerOrderCriteria.getFetchCriteria)
        } else if (payload.name === 'vta') {
            facturesCustomerOrderCriteria.addSort('vat.value', payload.direction)
            await storeFacturesCustomerOrderItems.fetch(facturesCustomerOrderCriteria.getFetchCriteria)
        } else if (payload.name === 'invoiceDate') {
            facturesCustomerOrderCriteria.addSort('billingDate', payload.direction)
            await storeFacturesCustomerOrderItems.fetch(facturesCustomerOrderCriteria.getFetchCriteria)
        } else if (payload.name === 'deadlineDate') {
            facturesCustomerOrderCriteria.addSort('dueDate', payload.direction)
            await storeFacturesCustomerOrderItems.fetch(facturesCustomerOrderCriteria.getFetchCriteria)
        } else {
            facturesCustomerOrderCriteria.addSort(payload.name, payload.direction)
            await storeFacturesCustomerOrderItems.fetch(facturesCustomerOrderCriteria.getFetchCriteria)
        }
    }
    async function searchFacturesCustomerOrders(inputValues) {
        facturesCustomerOrderCriteria.resetAllFilter()
        facturesCustomerOrderCriteria.addFilter('company', currentCompany)
        if (inputValues.invoiceNumber) facturesCustomerOrderCriteria.addFilter('ref', inputValues.invoiceNumber)
        if (inputValues.totalHT) facturesCustomerOrderCriteria.addFilter('exclTax.value', inputValues.totalHT.value)
        if (inputValues.totalHT) {
            const requestedcurrencie = currencies.find(currencie => currencie['@id'] === inputValues.totalHT.code)
            facturesCustomerOrderCriteria.addFilter('exclTax.code', requestedcurrencie.code)
        }
        if (inputValues.totalTTC) facturesCustomerOrderCriteria.addFilter('inclTax.value', inputValues.totalTTC.value)
        if (inputValues.totalTTC) {
            const requestedcurrencie = currencies.find(currencie => currencie['@id'] === inputValues.totalTTC.code)
            facturesCustomerOrderCriteria.addFilter('inclTax.code', requestedcurrencie.code)
        }
        if (inputValues.vta) facturesCustomerOrderCriteria.addFilter('vat.value', inputValues.vta.value)
        if (inputValues.vta) {
            const requestedcurrencie = currencies.find(currencie => currencie['@id'] === inputValues.vta.code)
            facturesCustomerOrderCriteria.addFilter('vat.code', requestedcurrencie.code)
        }
        if (inputValues.invoiceDate) facturesCustomerOrderCriteria.addFilter('billingDate', inputValues.invoiceDate)
        if (inputValues.deadlineDate) facturesCustomerOrderCriteria.addFilter('dueDate', inputValues.deadlineDate)
        if (inputValues.currentPlace) facturesCustomerOrderCriteria.addFilter('embState.state', inputValues.currentPlace)
        await storeFacturesCustomerOrderItems.fetch(facturesCustomerOrderCriteria.getFetchCriteria)
    }
    async function cancelSearchFacturesCustomerOrders() {
        facturesCustomerOrderCriteria.resetAllFilter()
        facturesCustomerOrderCriteria.addFilter('company', currentCompany)
        await storeFacturesCustomerOrderItems.fetch(facturesCustomerOrderCriteria.getFetchCriteria)
    }

    const updateBillingAdressCustomerOrder = {}
    async function updateBillingAdress(newAdress) {
        updateBillingAdressCustomerOrder.value = {
            billedTo: newAdress.AdresseFacturation
        }
    }
    async function updateBAdressCustomerOrder(){
        const payload = {
            id,
            SellingOrder: {billedTo: updateBillingAdressCustomerOrder.value.billedTo}
        }
        await storeCustomerorder.updateSellingOrder(payload)
    }
</script>

<template>
    <AppTabs id="gui-form-create" class="display-block-important">
        <AppTab id="gui-start-files" active icon="industry" title="OF" tabs="gui-form-create">
            <AppSuspense>
                <AppCardableTable
                    :current-page="storeOfCustomerOrderItems.currentPage"
                    :fields="OFfields"
                    :first-page="storeOfCustomerOrderItems.firstPage"
                    :items="ofcustomerOrderItems"
                    :last-page="storeOfCustomerOrderItems.lastPage"
                    :next-page="storeOfCustomerOrderItems.nextPage"
                    :pag="storeOfCustomerOrderItems.pagination"
                    :previous-page="storeOfCustomerOrderItems.previousPage"
                    :user="roleuser"
                    form="ofCustomerOrderTable"
                    @deleted="deletedOFCustomerOrders"
                    @get-page="getPageOfCustomerOrders"
                    @trier-alphabet="trierAlphabetOfCustomerOrders"
                    @search="searchOfCustomerOrders"
                    @cancel-search="cancelSearchOfCustomerOrders"/>
            </AppSuspense>
        </AppTab>
        <AppTab id="gui-start-quality" icon="clipboard" title="Bons de préparation" tabs="gui-form-create">
            <div class="alert alert-warning">
                En cours de développement
            </div>
        </AppTab>
        <AppTab id="gui-start-purchase-logistics" icon="truck" title="BL" tabs="gui-form-create">
            <AppCardShow id="addBL" :component-attribute="adressCustomer" :fields="BLfields" @update:model-value="updateAdress" @update="updateCustomerOrder"/>
            <AppSuspense>
                <AppCardableTable
                    :current-page="storeBlCustomerOrderItems.currentPage"
                    :fields="fieldsBL"
                    :first-page="storeBlCustomerOrderItems.firstPage"
                    :items="blCustomerOrderItems"
                    :last-page="storeBlCustomerOrderItems.lastPage"
                    :next-page="storeBlCustomerOrderItems.nextPage"
                    :pag="storeBlCustomerOrderItems.pagination"
                    :previous-page="storeBlCustomerOrderItems.previousPage"
                    :user="roleuser"
                    form="blCustomerOrderTable"
                    @deleted="deletedBlCustomerOrders"
                    @get-page="getPageBlCustomerOrders"
                    @trier-alphabet="trierAlphabetBlCustomerOrders"
                    @search="searchBlCustomerOrders"
                    @cancel-search="cancelSearchBlCustomerOrders"/>
            </AppSuspense>
        </AppTab>
        <AppTab id="gui-start-accounting" icon="file-invoice-dollar" title="Factures" tabs="gui-form-create">
            <AppCardShow id="addressFacture" :component-attribute="billingAddressesCustomer" :fields="Facturesfields" @update:model-value="updateBillingAdress" @update="updateBAdressCustomerOrder"/>
            <AppSuspense>
                <AppCardableTable
                    :current-page="storeFacturesCustomerOrderItems.currentPage"
                    :fields="fieldsFactures"
                    :first-page="storeFacturesCustomerOrderItems.firstPage"
                    :items="facturesCustomerOrderItems"
                    :last-page="storeFacturesCustomerOrderItems.lastPage"
                    :next-page="storeFacturesCustomerOrderItems.nextPage"
                    :pag="storeFacturesCustomerOrderItems.pagination"
                    :previous-page="storeFacturesCustomerOrderItems.previousPage"
                    :user="roleuser"
                    form="facturesCustomerOrderTable"
                    @deleted="deletedFacturesCustomerOrders"
                    @get-page="getPageFacturesCustomerOrders"
                    @trier-alphabet="trierAlphabetFacturesCustomerOrders"
                    @search="searchFacturesCustomerOrders"
                    @cancel-search="cancelSearchFacturesCustomerOrders"/>
            </AppSuspense>
        </AppTab>
        <AppTab id="gui-start-addresses" icon="chart-line" title="Qualité" tabs="gui-form-create">
            <div class="alert alert-warning">
                a définir
            </div>
        </AppTab>
    </AppTabs>
</template>

<style scoped>
.display-block-important {
    display: block !important;
}
</style>
