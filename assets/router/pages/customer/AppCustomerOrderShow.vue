<script setup>
import { computed, ref } from 'vue-demi'
import { useBlCustomerOrderItemsStore } from '../../../stores/customer/blCustomerOrderItems'
import { useCustomerOrderItemsStore } from '../../../stores/customer/customerOrderItems'
import { useFacturesCustomerOrderItemsStore } from '../../../stores/customer/facturesCustomerOrderItems'
import { useOfCustomerOrderItemsStore } from '../../../stores/customer/ofCustomerOrderItems'
import { useUnitsStore } from '../../../stores/unit/units'
import { useRoute } from 'vue-router'
import AppTab from '../../../components/tab/AppTab.vue'
import AppTabs from '../../../components/tab/AppTabs.vue'
import useFetchCriteria from '../../../stores/fetch-criteria/fetchCriteria'
import useUser from '../../../stores/security'
import AppSuspense from '../../../components/AppSuspense.vue'
import useOptions from '../../../stores/option/options'
import { useCustomerAddressStore } from '../../../stores/customer/customerAddress'
import { useCustomerOrderStore } from '../../../stores/customer/customerOrder'

const route = useRoute()
const idProduct = Number(route.params.id_product)
const idOrder = Number(route.params.id_order)
const id = Number(route.params.id)
console.log('id', id);

const fetchUnitOptions = useOptions('units')
await fetchUnitOptions.fetchOp()
const optionsUnit = computed(() =>
    fetchUnitOptions.options.map(op => {
        const text = op.text
        const value = op.value
        return { text, value }
    })
)
const fetchcompaniesOptions = useOptions('companies')
await fetchcompaniesOptions.fetchOp()
const companiesOptions = computed(() =>
    fetchcompaniesOptions.options.map(op => {
        const text = op.text
        const value = op.value
        return { text, value }
    })
)
const stateOptions = [
    { text: 'partially_delivered', value: 'partially_delivered' },
    { text: 'delivered', value: 'delivered' },
    { text: 'agreed', value: 'agreed' },
]
const currentPlaceOptions = [
    { text: 'rejected', value: 'rejected' },
    { text: 'asked', value: 'asked' },
    { text: 'agreed', value: 'agreed' },
]
const blCurrentPlaceOptions = [
    { text: 'rejected', value: 'rejected' },
    { text: 'asked', value: 'asked' },
    { text: 'agreed', value: 'agreed' },
    { text: 'closed', value: 'closed' }
]

const storeCustomerorder = useCustomerOrderStore()
await storeCustomerorder.fetchById(id)
const customer = computed(() => storeCustomerorder.customer)
const adressCustomer = {
    AdresseLivraison: storeCustomerorder.customerOrder.destination
}
console.log('adressCustomer', adressCustomer);

const storeCustomerAddress = useCustomerAddressStore()
await storeCustomerAddress.fetchDeliveryAddress()
const deliveryAddressesOption = computed(() => storeCustomerAddress.deliveryAddressesOption)
let filteredOptions = deliveryAddressesOption.value.filter(option => option.customer === customer.value)


const BLfields = [
    {
        label: 'Adresse de livraison',
        name: 'AdresseLivraison',
        options: {
            label: value =>
                filteredOptions.find(option => option.type === value)?.text ?? null,
            options: filteredOptions
        },
        trie: true,
        type: 'select'
    }
]
const Facturesfields = [
    { label: 'Adresse de facturation', name: 'AdresseFacturation', type: 'text' }
]
const fieldsCommande = [
    { label: 'Produit', name: 'product', type: 'multiselect-fetch', api: '/api/products', filteredProperty: 'code', max: 1 },
    { label: 'Réf', name: 'ref', trie: true, type: 'text' },
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
        type: 'measure'
    },
    { label: 'date de livraison souhaitée', name: 'requestedDate', trie: true, type: 'date' },
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
        type: 'measure'
    },
    { label: 'Date de livraison confirmée', name: 'confirmedDate', trie: true, type: 'date' },
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
    { label: 'Description', name: 'notes', trie: true, type: 'text' },

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
    { label: 'Number', name: 'number', trie: true, type: 'number' },
    { label: 'Departure Date', name: 'departureDate', trie: true, type: 'date' },
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
    { label: 'Invoice Number', name: 'invoiceNumber', trie: true, type: 'text' },
    { label: 'Total HT', name: 'totalHT', trie: true, type: 'text' },
    { label: 'Total TTC', name: 'totalTTC', trie: true, type: 'text' },
    { label: 'Vta', name: 'vta', trie: true, type: 'text' },
    { label: 'Invoice Date', name: 'invoiceDate', trie: true, type: 'date' },
    { label: 'Deadline Date', name: 'deadlineDate', trie: true, type: 'date' },
    { label: 'Invoice Send By Email', name: 'invoiceSendByEmail', trie: true, type: 'text' },
    { label: 'Current Place', name: 'currentPlace', trie: true, type: 'text' }
]



const fetchUser = useUser()
const currentCompany = fetchUser.company
const isSellingWriterOrAdmin = fetchUser.isSellingWriter || fetchUser.isSellingAdmin
const roleuser = ref(isSellingWriterOrAdmin ? 'writer' : 'reader')

const storeUnits = useUnitsStore()
await storeUnits.fetch()
const units = storeUnits.units
//CustomerOrdersTable
const storeCustomerOrderItems = useCustomerOrderItemsStore()
const customerOrderCriteria = useFetchCriteria('customer-orders-criteria')
customerOrderCriteria.addFilter('product', `/api/products/${idProduct}`)
await storeCustomerOrderItems.fetchAll(customerOrderCriteria.getFetchCriteria)

customerOrderCriteria.addFilter('company', currentCompany)
const customerOrderItems = computed(() => storeCustomerOrderItems.itemsCustomerOrders)

async function refreshTableCustomerOrders() {
    await storeCustomerOrderItems.fetchAll(customerOrderCriteria.getFetchCriteria)
}
await refreshTableCustomerOrders()


async function deletedCustomerOrders(id) {
    await storeCustomerOrderItems.remove(id)
    await refreshTableCustomerOrders()
}
async function getPageCustomerOrders(nPage) {
    customerOrderCriteria.gotoPage(parseFloat(nPage))
    await storeCustomerOrderItems.fetchAll(customerOrderCriteria.getFetchCriteria)
}
async function searchCustomerOrders(inputValues) {
    customerOrderCriteria.resetAllFilter()
    customerOrderCriteria.addFilter('company', currentCompany)
    if (inputValues.ref) customerOrderCriteria.addFilter('ref', inputValues.ref)
    if (inputValues.requestedQuantity) customerOrderCriteria.addFilter('requestedQuantity.value', inputValues.requestedQuantity.value)
    if (inputValues.requestedQuantity) {
        const requestedUnit = units.find(unit => unit['@id'] === inputValues.requestedQuantity.code);
        customerOrderCriteria.addFilter('requestedQuantity.code', requestedUnit.code)
    }
    if (inputValues.requestedDate) customerOrderCriteria.addFilter('requestedDate', inputValues.requestedDate)
    if (inputValues.confirmedQuantity) customerOrderCriteria.addFilter('confirmedQuantity.value', inputValues.confirmedQuantity.value)
    if (inputValues.confirmedQuantity) {
        const requestedUnit = units.find(unit => unit['@id'] === inputValues.confirmedQuantity.code);
        customerOrderCriteria.addFilter('confirmedQuantity.code', requestedUnit.code)
    }
    if (inputValues.confirmedDate) customerOrderCriteria.addFilter('confirmedDate', inputValues.confirmedDate)
    if (inputValues.state) customerOrderCriteria.addFilter('embState.state', inputValues.state)
    if (inputValues.notes) customerOrderCriteria.addFilter('notes', inputValues.notes)
    await storeCustomerOrderItems.fetchAll(customerOrderCriteria.getFetchCriteria)
}
async function cancelSearchCustomerOrders() {
    customerOrderCriteria.resetAllFilter()
    customerOrderCriteria.addFilter('company', currentCompany)
    await storeCustomerOrderItems.fetchAll(customerOrderCriteria.getFetchCriteria)
}
async function trierAlphabetCustomerOrders(payload) {
    if (payload.name === 'requestedQuantity') {
        customerOrderCriteria.addSort('requestedQuantity.value', payload.direction)
        await storeCustomerOrderItems.fetchAll(customerOrderCriteria.getFetchCriteria)
    } else if (payload.name === 'confirmedQuantity') {
        customerOrderCriteria.addSort('confirmedQuantity.value', payload.direction)
        await storeCustomerOrderItems.fetchAll(customerOrderCriteria.getFetchCriteria)
    } else if (payload.name === 'state') {
        customerOrderCriteria.addSort('embState.state', payload.direction)
        await storeCustomerOrderItems.fetchAll(customerOrderCriteria.getFetchCriteria)
    } else {
        customerOrderCriteria.addSort(payload.name, payload.direction)
        await storeCustomerOrderItems.fetchAll(customerOrderCriteria.getFetchCriteria)
    }
}

//ofCustomerOrderTable
const storeOfCustomerOrderItems = useOfCustomerOrderItemsStore()
const ofCustomerOrderCriteria = useFetchCriteria('of-customer-orders-criteria')
customerOrderCriteria.addFilter('order', `/api/selling-orders/${idOrder}`)
await storeOfCustomerOrderItems.fetch(ofCustomerOrderCriteria.getFetchCriteria)

ofCustomerOrderCriteria.addFilter('company', currentCompany)
const ofcustomerOrderItems = computed(() => storeOfCustomerOrderItems.itemsofCustomerOrder)
async function refreshTableOfCustomerOrders() {
    await storeOfCustomerOrderItems.fetch(ofCustomerOrderCriteria.getFetchCriteria)
}
await refreshTableOfCustomerOrders()

async function deletedOFCustomerOrders(id) {
    await storeOfCustomerOrderItems.remove(id)
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
        const requestedUnit = units.find(unit => unit['@id'] === inputValues.quantityRequested.code);
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

async function deletedBlCustomerOrders(id) {
    await storeBlCustomerOrderItems.remove(id)
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
    }else if (payload.name === 'departureDate') {
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
const updateAdressCustomerOrder ={}
async function updateAdress(newAdress) {
    updateAdressCustomerOrder.value = {
        destination: newAdress.AdresseLivraison,
    }
}
async function updateCustomerOrder(){
    const payload= {
        id: id,
        SellingOrder:{destination: updateAdressCustomerOrder.value.destination}
    }
    await storeCustomerorder.updateSellingOrder(payload)
}

// factures

// const storeFacturesCustomerOrderItems = useFacturesCustomerOrderItemsStore()
// storeFacturesCustomerOrderItems.fetchItems()

</script>

<template>
    <AppTabs id="gui-form-create" class="display-block-important">
        <AppTab id="gui-start-main" active icon="sitemap" title="Commande" tabs="gui-form-create">
            <AppSuspense>
                <AppCardableTable
                        :current-page="storeCustomerOrderItems.currentPage"
                        :fields="fieldsCommande"
                        :first-page="storeCustomerOrderItems.firstPage"
                        :items="customerOrderItems"
                        :last-page="storeCustomerOrderItems.lastPage"
                        :next-page="storeCustomerOrderItems.nextPage"
                        :pag="storeCustomerOrderItems.pagination"
                        :previous-page="storeCustomerOrderItems.previousPage"
                        :user="roleuser"
                        form="formCustomerOrdersTable"
                        @deleted="deletedCustomerOrders"
                        @get-page="getPageCustomerOrders"
                        @trier-alphabet="trierAlphabetCustomerOrders"
                        @search="searchCustomerOrders"
                        @cancel-search="cancelSearchCustomerOrders"/>
            </AppSuspense>
        </AppTab>
        <AppTab id="gui-start-files" icon="industry" title="OF" tabs="gui-form-create">
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
            <AppCardShow id="addBL" :componentAttribute="adressCustomer" :fields="BLfields" @update:modelValue="updateAdress" @update="updateCustomerOrder"/>
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
            <!-- <AppCardShow id="addFacture" :fields="Facturesfields"/> -->
            <!-- <AppCardableTable :fields="fieldsFactures" :store="storeFacturesCustomerOrderItems"/> -->
            <!-- <AppTable :id="route.name" :fields="fieldsFactures" :store="storeFacturesCustomerOrderItems" /> -->
        </AppTab>
        <AppTab id="gui-start-addresses" icon="chart-line" title="Qualité" tabs="gui-form-create">
            <div class="alert alert-warning">
                a définir
            </div>
        </AppTab>
        <AppTab id="gui-start-contacts" icon="clipboard" title="Généralités" tabs="gui-form-create">
            <div class="alert alert-warning">
                En cours de développement
            </div>
        </AppTab>
    </AppTabs>
</template>
<style scoped>
.display-block-important {
    display: block !important;
}
</style>