<script setup>
    import {computed, ref} from 'vue'
    import useFetchCriteria from '../../../../../../stores/fetch-criteria/fetchCriteria'
    import useOptions from '../../../../../../stores/option/options'
    import {useComponentListStore} from '../../../../../../stores/purchase/component/components'
    import {useProductStore} from '../../../../../../stores/project/product/products'
    import {useRoute} from 'vue-router'
    import {useStockListStore} from '../../../../../../stores/logistic/stocks/stocks'
    import AppSuspense from '../../../../../AppSuspense.vue'
    import WarehouseStockAddForm from './WarehouseStockAddForm.vue'
    import WarehouseStockUpdateForm from './WarehouseStockUpdateForm.vue'

    //region récupération des informations de route
    const maRoute = useRoute()
    const warehouseId = maRoute.params.id_warehouse
    //endregion
    const roleuser = ref('reader')
    const updated = ref(false)
    const AddForm = ref(false)
    const filterBy = computed(() => null)
    const filter = ref(false)
    const trierAlpha = computed(() => null)
    const sortable = ref(true)
    let updateKey = 0
    //region champ multi-select composant
    const listStockComponentSearchCriteria = useFetchCriteria('listStockComponent')
    const fetchComponentStore = useComponentListStore()
    async function updateComponents() {
        await fetchComponentStore.fetchAll(listStockComponentSearchCriteria.getFetchCriteria)
        return fetchComponentStore.components
    }
    await updateComponents()
    const optionComponentFilter = computed(() => fetchComponentStore.components.map(item => ({text: `${item.code}`, value: item['@id']})))
    //endregion
    //region champ multi-select produit
    const listStockProductSearchCriteria = useFetchCriteria('listStockProduct')
    const fetchProductStore = useProductStore()
    async function updateProducts() {
        await fetchProductStore.fetchAll(listStockProductSearchCriteria.getFetchCriteria)
        return fetchProductStore.products
    }
    await updateProducts()
    const optionProductSearchFilter = computed(() => fetchProductStore.products.map(item => ({text: `${item.code}`, value: item['@id']})))
    //endregion
    //region champ quantity.code chargement des units
    const fetchUnits = useOptions('units')
    await fetchUnits.fetchOp()
    const optionsUnit = fetchUnits.options.map(op => {
        const text = op.text
        const value = op.value
        return {text, value}
    })
    //endregion

    //region récupération des stocks liés à l'entrepot
    const fetchStocks = useStockListStore()
    const fetchCriteria = useFetchCriteria('warehouseStockList')
    fetchStocks.warehouseID = warehouseId
    fetchCriteria.addFilter('warehouse', `/api/warehouses/${warehouseId}`)
    await fetchStocks.fetch(fetchCriteria.getFetchCriteria)
    //endregion
    //region définition des champs tableau et des variables locales de liste et de vecteur de recherche
    const itemsTable = ref([])
    itemsTable.value = fetchStocks.itemsWarehousesStock
    const formData = ref({
        component: null, product: null, batchNumber: null, location: null, quantity: null, jail: null
    })
    const tabFields = [
        {
            create: true,
            filter: true,
            label: 'Composant',
            name: 'component',
            options: {label: value => optionComponentFilter.value.find(option => option.value === value)?.text ?? null, options: optionComponentFilter.value},
            sort: true,
            type: 'select',
            update: true,
            min: true
        },
        {
            create: true,
            filter: true,
            label: 'Produit',
            name: 'product',
            options: {label: value => optionProductSearchFilter.value.find(option => option.value === value)?.text ?? null, options: optionProductSearchFilter.value},
            sort: true,
            type: 'select',
            update: true,
            min: true
        },
        {
            label: 'Quantité ',
            name: 'quantity',
            measure: {
                code: { //récupérer l'unité du composant sélectionné
                    label: 'Unité',
                    name: 'code',
                    options: {
                        label: value =>
                            optionsUnit.value.find(option => option.type === value)?.text ?? null,
                        options: optionsUnit
                    },
                    type: 'select'
                },
                value: {
                    label: 'Valeur',
                    name: 'value',
                    type: 'number'
                }
            },
            type: 'measure',
            min: true
        },
        {
            create: true,
            filter: true,
            label: 'Prison',
            name: 'jail',
            sort: true,
            type: 'boolean',
            update: true,
            min: false
        },
        {
            create: true,
            filter: true,
            label: 'Numéro de série',
            name: 'batchNumber',
            sort: true,
            type: 'text',
            update: true,
            min: false
        },
        {
            create: true,
            filter: true,
            label: 'Localisation',
            name: 'location',
            sort: true,
            type: 'text',
            update: true,
            min: false
        }
    ]
    //endregion
    //region fonctions de gestion de la liste des stocks
    function ajoute(){
        AddForm.value = true
        updated.value = false
    }
    function update(data) {
        formData.value = {
            id: data['@id'],
            component: data.item['@type'] === 'Component' ? data.item : null,
            product: data.item['@type'] === 'Product' ? data.item : null,
            batchNumber: data.batchNumber,
            location: data.location,
            quantity: {
                codeLabel: data.quantity.code,
                value: data.quantity.value,
                code: fetchUnits.options.filter(element => element.text === data.quantity.code)[0]['@id']
            },
            jail: data.jail
        }
        if (updated.value === false) {
            updated.value = true
            AddForm.value = false
        } else {
            updateKey++
        }
    }
    async function updateListe() {
        await fetchStocks.fetch(fetchCriteria.getFetchCriteria)
        itemsTable.value = fetchStocks.itemsWarehousesStock
    }
    async function deleted(id){
        await fetchStocks.deleted(id)
        updateListe()
    }
    async function getPage(nPage){
        await fetchStocks.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...fetchStocks.itemsWarehousesStock]
    }
    async function trierAlphabet(payload) {
        await fetchStocks.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha.value = payload
    }
    async function search(inputValues) {
        let comp = ''
        if (typeof inputValues.component !== 'undefined'){
            comp = inputValues.component
        }

        let prod = ''
        if (typeof inputValues.product !== 'undefined'){
            prod = inputValues.product
        }

        const payload = {
            component: comp,
            product: prod,
            batchNumber: inputValues.batchNumber ?? '',
            location: inputValues.location ?? '',
            quantity: inputValues.quantity ?? '',
            jail: inputValues.jail ?? ''
        }

        if (typeof payload.quantity.value === 'undefined' && payload.quantity !== '') {
            payload.quantity.value = ''
        }
        if (typeof payload.quantity.code === 'undefined' && payload.quantity !== '') {
            payload.quantity.code = ''
        }
        await fetchStocks.filterBy(payload)
        // itemsTable.value = [...fetchStocks.itemsWarehousesStock]
        itemsTable.value = fetchStocks.itemsWarehousesStock
        filter.value = true
        filterBy.value = payload
    }
    async function cancelSearch() {
        filter.value = true
        await fetchStocks.fetch(fetchCriteria.getFetchCriteria)
        itemsTable.value = fetchStocks.itemsWarehousesStock
    }
    //endregion
    async function afterCreation() {
        AddForm.value = false
        updateListe()
    }
    async function afterUpdate() {
        updated.value = false
        updateListe()
    }
</script>

<template>
    <AppRow>
        <AppCol class="d-flex justify-content-between mb-2">
            <span>
                <AppBtn variant="success" label="Ajout" @click="ajoute">
                    <Fa icon="plus"/>
                    Créer un nouveau stock
                </AppBtn>
            </span>
        </AppCol>
    </AppRow>
    <AppRow>
        <AppCol>
            <AppSuspense>
                <AppCardableTable
                    :current-page="fetchStocks.currentPage"
                    :fields="tabFields"
                    :first-page="fetchStocks.firstPage"
                    :items="itemsTable"
                    :last-page="fetchStocks.lastPage"
                    :min="AddForm || updated"
                    :next-page="fetchStocks.nextPage"
                    :pag="fetchStocks.pagination"
                    :previous-page="fetchStocks.previousPage"
                    :user="roleuser"
                    form="formWarehouseStockTable"
                    @update="update"
                    @deleted="deleted"
                    @get-page="getPage"
                    @trier-alphabet="trierAlphabet"
                    @search="search"
                    @cancel-search="cancelSearch"/>
            </AppSuspense>
        </AppCol>
        <AppCol v-if="AddForm">
            <AppSuspense>
                <WarehouseStockAddForm @cancel="AddForm = false" @saved="afterCreation"/>
            </AppSuspense>
        </AppCol>
        <AppCol v-if="updated">
            <WarehouseStockUpdateForm :key="`update_${updateKey}`" :item="formData" @cancel="updated = false" @saved="afterUpdate"/>
        </AppCol>
    </AppRow>
</template>

<style scoped>
    .btn-float-right{
        float: right;
    }
</style>
