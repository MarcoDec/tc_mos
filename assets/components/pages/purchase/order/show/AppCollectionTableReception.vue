<script setup>
    import {computed, ref} from 'vue'
    import useUser from '../../../../../stores/security'
    import {useReceiptsStore} from '../../../../../stores/logistic/order/receipts'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import useOptions from '../../../../../stores/option/options'
    import {usePurchaseOrderItemComponentsStore} from '../../../../../stores/purchase/order/purchaseOrderItem'

    const fetchUser = useUser()
    const isPurchaseWriterOrAdmin = fetchUser.isPurchaseWriter || fetchUser.isPurchaseAdmin
    const roleuser = ref(isPurchaseWriterOrAdmin ? 'writer' : 'reader')
    const fetchUnitOptions = useOptions('units')
    await fetchUnitOptions.fetchOp()
    const optionsUnit = computed(() =>
        fetchUnitOptions.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))

    const stateOptions = [
        {text: 'asked', value: 'asked'},
        {text: 'blocked', value: 'blocked'},
        {text: 'closed', value: 'closed'},
        {text: 'to_validate', value: 'to_validate'}
    ]

    const fields = [
        {
            label: 'Composant',
            name: 'componentObject',
            type: 'multiselect-fetch',
            api: '/api/components',
            filteredProperty: 'code',
            max: 1,
            trie: true
        },
        // {
        //     label: 'Quantité confirmée',
        //     name: 'confirmedQuantity',
        //     filter: true,
        //     min: true,
        //     measure: {
        //         code: {
        //             label: 'Code',
        //             name: 'confirmedQuantity.code',
        //             options: {
        //                 label: value =>
        //                     optionsUnit.value.find(option => option.type === value)?.text ?? null,
        //                 options: optionsUnit.value
        //             },
        //             type: 'select'
        //         },
        //         value: {
        //             label: 'Valeur',
        //             name: 'confirmedQuantity.value',
        //             type: 'number',
        //             step: 0.1
        //         }
        //     },
        //     trie: true,
        //     type: 'measure'
        // },
        {
            label: 'Quantité Reçue',
            name: 'quantityReceived',
            filter: true,
            min: true,
            measure: {
                code: {
                    label: 'Code',
                    name: 'quantityReceived.code',
                    options: {
                        label: value =>
                            optionsUnit.value.find(option => option.type === value)?.text ?? null,
                        options: optionsUnit.value
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'quantityReceived.value',
                    type: 'number',
                    step: 0.1
                }
            },
            trie: true,
            type: 'measure'
        },
        {
            label: 'Date Souhaitée',
            name: 'requestedDate',
            trie: true,
            type: 'date'
        },
        {
            label: 'Etat',
            name: 'state',
            options: {
                label: value =>
                    stateOptions.find(option => option.type === value)?.text ?? null,
                options: stateOptions
            },
            trie: false,
            type: 'select'
        }
    ]
    const storeReceiptsItems = useReceiptsStore()
    const receiptsCriteria = useFetchCriteria('receipts')
    // receiptsCriteria.addFilter('purchaseOrder', `/api/purchase-orders/${id}`)
    await storeReceiptsItems.fetch(receiptsCriteria.getFetchCriteria)
    const itemsReceipts = computed(() => storeReceiptsItems.itemsReceipts)
    // console.log('itemsReceipts', itemsReceipts);
    async function refreshReceipts() {
        await storeReceiptsItems.fetch(receiptsCriteria.getFetchCriteria)
    }
    await refreshReceipts()
    async function getPageReceipts(nPage) {
        receiptsCriteria.gotoPage(parseFloat(nPage))
        await storeReceiptsItems.fetch(receiptsCriteria.getFetchCriteria)
    }

    async function extraireItems(itemsReceipts2) {
        const promises = itemsReceipts2.map(async item => {
            const parties = item.requestedDate.split('T')
            const date = parties[0]
            delete item.requestedDate
            item.requestedDate = date

            const idComponent = item.component
            const partiesComponent = idComponent.split('/')
            const id2 = parseInt(partiesComponent[partiesComponent.length - 1], 10)

            const storePurchaseOrderItemComponentItems = usePurchaseOrderItemComponentsStore()
            await storePurchaseOrderItemComponentItems.fetchById(id2)

            const itemsPurchaseOrderItemComponent = storePurchaseOrderItemComponentItems.purchaseOrderitemComponent
            //eslint-disable-next-line require-atomic-updates
            item.componentObject = itemsPurchaseOrderItemComponent.item
            return item
        })
        return Promise.all(promises)
    }

    const resultat = await extraireItems(itemsReceipts)
    // console.log('resultat',resultat);

    // async function trierReceipts(payload) {
    //     console.log('payload', payload);
    //     if (payload.name === 'componentObject') {
    //         receiptsCriteria.addSort('item.code', payload.direction)
    //         await storeReceiptsItems.fetch(receiptsCriteria.getFetchCriteria)
    //     } else if (payload.name === 'quantityReceived') {
    //         receiptsCriteria.addSort('quantityReceived.value', payload.direction)
    //         await storeReceiptsItems.fetch(receiptsCriteria.getFetchCriteria)
    //     } else {
    //         receiptsCriteria.addSort(payload.name, payload.direction)
    //         await storeReceiptsItems.fetch(receiptsCriteria.getFetchCriteria)
    //     }
    // }
    //  async function searchReceipts(inputValues) {
    //     console.log('inputValues', inputValues)
    //     receiptsCriteria.resetAllFilter()
    //     if (inputValues.component) receiptsCriteria.addFilter('item.code', inputValues.component)
    //     if (inputValues.product) receiptsCriteria.addFilter('product', inputValues.product)
    //     if (inputValues.ref) receiptsCriteria.addFilter('item.manufacturerCode', inputValues.ref)
    //     if (inputValues.requestedQuantity) receiptsCriteria.addFilter('requestedQuantity.value', inputValues.requestedQuantity.value)
    //     if (inputValues.requestedQuantity) {
    //         const requestedUnit = units.find(unit => unit['@id'] === inputValues.requestedQuantity.code)
    //         receiptsCriteria.addFilter('requestedQuantity.code', requestedUnit.code)
    //     }
    //     if (inputValues.requestedDate) receiptsCriteria.addFilter('requestedDate', inputValues.requestedDate)
    //     if (inputValues.confirmedQuantity) receiptsCriteria.addFilter('confirmedQuantity.value', inputValues.confirmedQuantity.value)
    //     if (inputValues.confirmedQuantity) {
    //         const requestedUnit = units.find(unit => unit['@id'] === inputValues.confirmedQuantity.code)
    //         receiptsCriteria.addFilter('confirmedQuantity.code', requestedUnit.code)
    //     }
    //     if (inputValues.confirmedDate) receiptsCriteria.addFilter('confirmedDate', inputValues.confirmedDate)
    //     if (inputValues.state) receiptsCriteria.addFilter('embState.state[]', inputValues.state)
    //     if (inputValues.notes) receiptsCriteria.addFilter('notes', inputValues.notes)
    //     if (inputValues.targetCompany) receiptsCriteria.addFilter('targetCompany', inputValues.targetCompany)
    //     await storeReceiptsItems.fetch(receiptsCriteria.getFetchCriteria)
    // }
    function searchReceipts(data) {
        console.log('searchReceipts data', data)
    }
    async function cancelReceipts() {
        receiptsCriteria.resetAllFilter()
        await storeReceiptsItems.fetch(receiptsCriteria.getFetchCriteria)
    }
    function trierReceipts(data) {
        console.log('trierReceipts data', data)
    }
</script>

<template>
    <AppSuspense>
        <AppCardableTable
            :current-page="storeReceiptsItems.currentPage"
            :fields="fields"
            :first-page="storeReceiptsItems.firstPage"
            :items="resultat"
            :last-page="storeReceiptsItems.lastPage"
            :next-page="storeReceiptsItems.nextPage"
            :pag="storeReceiptsItems.pagination"
            :previous-page="storeReceiptsItems.previousPage"
            :user="roleuser"
            form="receptionCardableTable"
            @get-page="getPageReceipts"
            @trier-alphabet="trierReceipts"
            @search="searchReceipts"
            @cancel-search="cancelReceipts"/>
    </AppSuspense>
</template>
