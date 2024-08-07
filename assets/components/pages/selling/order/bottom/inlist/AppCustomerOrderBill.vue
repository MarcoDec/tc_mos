<script setup>
    import AppSuspense from "../../../../../AppSuspense.vue"
    import {useFacturesCustomerOrderItemsStore} from "../../../../../../stores/customer/facturesCustomerOrderItems"
    import useFetchCriteria from "../../../../../../stores/fetch-criteria/fetchCriteria"
    import {computed, ref} from "vue"
    const props = defineProps({
        currenciesOptions: {default: () => [], type: Array},
        customer: {default: () => ({}), type: Object},
        order: {default: () => ({}), type: Object},
        storeCustomerAddress: {default: () => ({}), type: Object},
        userRole: {default: '', type: String}
    })
    const billingAddressesOption = computed(() => props.storeCustomerAddress.billingAddressesOption)
    const filteredBillingAddressesOptions = billingAddressesOption.value.filter(option => option.customer === props.customer.value)

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
    const roleuser = ref(props.userRole)
    const facturesCurrentPlaceOptions = [
        {text: 'billed', value: 'billed'},
        {text: 'draft', value: 'draft'},
        {text: 'partially_paid', value: 'partially_paid'},
        {text: 'paid', value: 'paid'}
    ]
    const fieldsFactures = [
        {
            label: 'Invoice Number',
            name: 'invoiceNumber',
            trie: true,
            type: 'text'
        },
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
                            props.currenciesOptions.find(option => option.type === value)?.text ?? null,
                        options: props.currenciesOptions
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
                            props.currenciesOptions.find(option => option.type === value)?.text ?? null,
                        options: props.currenciesOptions
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
                            props.currenciesOptions.find(option => option.type === value)?.text ?? null,
                        options: props.currenciesOptions
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
    const storeFacturesCustomerOrderItems = useFacturesCustomerOrderItemsStore()
    const facturesCustomerOrderCriteria = useFetchCriteria('factures-customer-orders-criteria')
    const facturesCustomerOrderItems = computed(() => storeFacturesCustomerOrderItems.itemsFactureCustomerOrder)
    function initFilters() {
        facturesCustomerOrderCriteria.resetAllFilter()
        facturesCustomerOrderCriteria.addFilter('sellingOrder', props.order['@id'])
    }
    initFilters()
    await storeFacturesCustomerOrderItems.fetch(facturesCustomerOrderCriteria.getFetchCriteria)

    async function refreshTableFacturesCustomerOrders() {
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
        initFilters()
        if (inputValues.invoiceNumber) facturesCustomerOrderCriteria.addFilter('ref', inputValues.invoiceNumber)
        if (inputValues.totalHT) {
            if (inputValues.totalHT.value) facturesCustomerOrderCriteria.addFilter('exclTax.value', inputValues.totalHT.value)
            if (inputValues.totalHT.code) {
                const requestedcurrencie = props.currenciesOptions.find(currencie => currencie.value === inputValues.totalHT.code)
                facturesCustomerOrderCriteria.addFilter('exclTax.code', requestedcurrencie.value)
            }
        }
        if (inputValues.totalTTC) {
            if (inputValues.totalTTC.value) facturesCustomerOrderCriteria.addFilter('inclTax.value', inputValues.totalTTC.value)
            if (inputValues.totalTTC.code) {
                const requestedcurrencie = props.currenciesOptions.find(currencie => currencie.value === inputValues.totalTTC.code)
                facturesCustomerOrderCriteria.addFilter('inclTax.code', requestedcurrencie.value)
            }
        }
        if (inputValues.vta) {
            if (inputValues.vta.value) facturesCustomerOrderCriteria.addFilter('vat.value', inputValues.vta.value)
            if (inputValues.vta.code) {
                const requestedcurrencie = props.currenciesOptions.find(currencie => currencie.value === inputValues.vta.code)
                facturesCustomerOrderCriteria.addFilter('vat.code', requestedcurrencie.value)
            }
        }
        if (inputValues.invoiceDate) facturesCustomerOrderCriteria.addFilter('billingDate', inputValues.invoiceDate)
        if (inputValues.deadlineDate) facturesCustomerOrderCriteria.addFilter('dueDate', inputValues.deadlineDate)
        if (inputValues.currentPlace) facturesCustomerOrderCriteria.addFilter('embState.state', inputValues.currentPlace)
        await storeFacturesCustomerOrderItems.fetch(facturesCustomerOrderCriteria.getFetchCriteria)
    }
    async function cancelSearchFacturesCustomerOrders() {
        initFilters()
        await storeFacturesCustomerOrderItems.fetch(facturesCustomerOrderCriteria.getFetchCriteria)
    }
    const billingAddressesCustomer = {
        AdresseFacturation: props.order.billedTo
    }
</script>

<template>
    <AppCardShow
        id="addressFacture"
        :component-attribute="billingAddressesCustomer"
        :fields="Facturesfields"
        @update:model-value="updateBillingAdress"
        @update="updateBAdressCustomerOrder"/>
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
            :user="roleuser.toString()"
            :should-see="false"
            :should-delete="false"
            form="facturesCustomerOrderTable"
            @deleted="deletedFacturesCustomerOrders"
            @get-page="getPageFacturesCustomerOrders"
            @trier-alphabet="trierAlphabetFacturesCustomerOrders"
            @search="searchFacturesCustomerOrders"
            @cancel-search="cancelSearchFacturesCustomerOrders"/>
    </AppSuspense>
</template>
