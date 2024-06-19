<script setup>
    import {computed, ref} from 'vue'
    import useUser from '../../../../../stores/security'
    import { useRoute } from 'vue-router';
    import { usePurchaseOrderItemComponentsStore } from '../../../../../stores/purchase/order/purchaseOrderItem';
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import useOptions from '../../../../../stores/option/options'

    const route = useRoute()
    const id = Number(route.params.id)

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
    
    const fetchcompaniesOptions = useOptions('companies')
    await fetchcompaniesOptions.fetchOp()
    const companiesOptions = computed(() =>
        fetchcompaniesOptions.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))

    const stateOptions = [
    {text: 'agreed', value: 'agreed'},
    {text: 'delivered', value: 'delivered'},
    {text: 'draft', value: 'draft'},
    {text: 'forecast', value: 'forecast'},
    {text: 'initial', value: 'initial'},
    {text: 'monthly', value: 'monthly'},
    {text: 'partially_delivered', value: 'partially_delivered'}
    ]

    const fields = computed(() => [
        {
            label: 'Composant',
            name: 'component',
            type: 'multiselect-fetch',
            api: '/api/components',
            filteredProperty: 'code',
            max: 1,
            trie: true
        },
        {
            label: 'Produit',
            name: 'product',
            type: 'multiselect-fetch',
            api: '/api/products',
            filteredProperty: 'code',
            max: 1,
            trie: false
        },
        {
            label: 'Référence Fournisseur',
            name: 'ref',
            trie: true,
            type: 'text'
        },
        {
            label: 'Quantité Souhaitée',
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
        {
            label: 'Date Souhaitée',
            name: 'requestedDate',
            trie: true,
            type: 'date',
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
        {
            label: 'Date de confirmation',
            name: 'confirmedDate',
            trie: true,
            type: 'date',
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
        },
        {
            label: 'Texte',
            name: 'notes',
            trie: true,
            type: 'text'
        },
        {
            label: 'Compagnie destinataire',
            name: 'targetCompany',
            options: {
                label: value =>
                    companiesOptions.value.find(option => option.type === value)?.text ?? null,
                options: companiesOptions.value
            },
            trie: false,
            type: 'select'
        }
    ])

    const storePurchaseOrderItemComponentItems = usePurchaseOrderItemComponentsStore()
    const purchaseOrderItemComponentCriteria = useFetchCriteria('purchase-order-item-components')
    purchaseOrderItemComponentCriteria.addFilter('order', `/api/purchase-orders/${id}`)
    await storePurchaseOrderItemComponentItems.fetch(purchaseOrderItemComponentCriteria.getFetchCriteria)
    const itemsPurchaseOrderItemComponents = computed(() => storePurchaseOrderItemComponentItems.itemsPurchaseOrderItemComponents)

    async function refreshPurchaseOrderItemComponent() {
        await storePurchaseOrderItemComponentItems.fetch(purchaseOrderItemComponentCriteria.getFetchCriteria)
    }
    await refreshPurchaseOrderItemComponent()
    async function deletedItemPurchaseOrderItemComponent(idRemove) {
        await storePurchaseOrderItemComponentItems.remove(idRemove)
        await refreshPurchaseOrderItemComponent()
    }
    async function getPagePurchaseOrderItemComponent(nPage) {
        purchaseOrderItemComponentCriteria.gotoPage(parseFloat(nPage))
        await storePurchaseOrderItemComponentItems.fetch(purchaseOrderItemComponentCriteria.getFetchCriteria)
    }
    async function trierPurchaseOrderItemComponent(payload) {
        console.log('payload', payload)
        if (payload.name === 'component') {
            purchaseOrderItemComponentCriteria.addSort('item.code', payload.direction)
            purchaseOrderItemComponentCriteria.addFilter('order', `/api/purchase-orders/${id}`)
            await storePurchaseOrderItemComponentItems.fetch(purchaseOrderItemComponentCriteria.getFetchCriteria)
        } else if (payload.name === 'ref') {
            purchaseOrderItemComponentCriteria.addSort('item.manufacturerCode', payload.direction)
            await storePurchaseOrderItemComponentItems.fetch(purchaseOrderItemComponentCriteria.getFetchCriteria)
        } else if (payload.name === 'requestedQuantity') {
            purchaseOrderItemComponentCriteria.addSort('requestedQuantity.value', payload.direction)
            await storePurchaseOrderItemComponentItems.fetch(purchaseOrderItemComponentCriteria.getFetchCriteria)
        } else if (payload.name === 'confirmedQuantity') {
            purchaseOrderItemComponentCriteria.addSort('confirmedQuantity.value', payload.direction)
            await storePurchaseOrderItemComponentItems.fetch(purchaseOrderItemComponentCriteria.getFetchCriteria)
        } else {
            purchaseOrderItemComponentCriteria.addSort(payload.name, payload.direction)
            await storePurchaseOrderItemComponentItems.fetch(purchaseOrderItemComponentCriteria.getFetchCriteria)
        }
    }
    async function searchPurchaseOrderItemComponent(inputValues) {
        console.log('inputValues', inputValues)
        purchaseOrderItemComponentCriteria.resetAllFilter()
        if (inputValues.component) purchaseOrderItemComponentCriteria.addFilter('item.code', inputValues.component)
        if (inputValues.product) purchaseOrderItemComponentCriteria.addFilter('product', inputValues.product)
        if (inputValues.ref) purchaseOrderItemComponentCriteria.addFilter('item.manufacturerCode', inputValues.ref)
        if (inputValues.requestedQuantity) purchaseOrderItemComponentCriteria.addFilter('requestedQuantity.value', inputValues.requestedQuantity.value)
        if (inputValues.requestedQuantity) {
            const requestedUnit = units.find(unit => unit['@id'] === inputValues.requestedQuantity.code)
            purchaseOrderItemComponentCriteria.addFilter('requestedQuantity.code', requestedUnit.code)
        }
        if (inputValues.requestedDate) purchaseOrderItemComponentCriteria.addFilter('requestedDate', inputValues.requestedDate)
        if (inputValues.confirmedQuantity) purchaseOrderItemComponentCriteria.addFilter('confirmedQuantity.value', inputValues.confirmedQuantity.value)
        if (inputValues.confirmedQuantity) {
            const requestedUnit = units.find(unit => unit['@id'] === inputValues.confirmedQuantity.code)
            purchaseOrderItemComponentCriteria.addFilter('confirmedQuantity.code', requestedUnit.code)
        }
        if (inputValues.confirmedDate) purchaseOrderItemComponentCriteria.addFilter('confirmedDate', inputValues.confirmedDate)
        if (inputValues.state) purchaseOrderItemComponentCriteria.addFilter('embState.state[]', inputValues.state)
        if (inputValues.notes) purchaseOrderItemComponentCriteria.addFilter('notes', inputValues.notes)
        if (inputValues.targetCompany) purchaseOrderItemComponentCriteria.addFilter('targetCompany', inputValues.targetCompany)
        await storePurchaseOrderItemComponentItems.fetch(purchaseOrderItemComponentCriteria.getFetchCriteria)
    }
    async function cancelPurchaseOrderItemComponent() {
        purchaseOrderItemComponentCriteria.resetAllFilter()
        await storePurchaseOrderItemComponentItems.fetch(purchaseOrderItemComponentCriteria.getFetchCriteria)
    }
</script>

<template>
    <AppSuspense>
        <AppCardableTable
            :current-page="storePurchaseOrderItemComponentItems.currentPage"
            :fields="fields"
            :first-page="storePurchaseOrderItemComponentItems.firstPage"
            :items="itemsPurchaseOrderItemComponents"
            :last-page="storePurchaseOrderItemComponentItems.lastPage"
            :next-page="storePurchaseOrderItemComponentItems.nextPage"
            :pag="storePurchaseOrderItemComponentItems.pagination"
            :previous-page="storePurchaseOrderItemComponentItems.previousPage"
            :user="roleuser"
            form="commandeCardableTable"
            @deleted="deletedItemPurchaseOrderItemComponent"
            @get-page="getPagePurchaseOrderItemComponent"
            @trier-alphabet="trierPurchaseOrderItemComponent"
            @search="searchPurchaseOrderItemComponent"
            @cancel-search="cancelPurchaseOrderItemComponent"/>
    </AppSuspense>
</template>
