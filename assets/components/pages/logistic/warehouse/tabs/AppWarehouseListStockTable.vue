<script setup>
    import {computed, ref} from 'vue'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    //import {useWarehouseStockListStore} from '../provisoir/warehouseStockList'
    import {useStockListStore} from '../../../../../stores/logistic/stocks/stocks'
    import {useRoute} from 'vue-router'
    import {useComponentListStore} from '../../../../../stores/purchase/component/components'
    import {useProductStore} from '../../../../../stores/project/product/products'
    import AppFormCardable from '../../../../form-cardable/AppFormCardable'
    import useOptions from '../../../../../stores/option/options'

    // const roleuser = ref('reader')
    let violations = []
    const updated = ref(false)
    const AddForm = ref(false)
    const isPopupVisible = ref(false)
    const itemsAddData = ref({})
    // const sortable = ref(false)
    // const filter = ref(false)
    // let trierAlpha = {}
    // let filterBy = {}
    let addFormKey = 0

    //region récupération des informations de route
    const maRoute = useRoute()
    const warehouseId = maRoute.params.id_warehouse
    //endregion
    const fetchUnits = useOptions('units')
    await fetchUnits.fetchOp()
    const optionsUnit = fetchUnits.options.map(op => {
        const text = op.text
        const value = op.value
        return {text, value}
    })
    //console.log('optionsUnit', optionsUnit)
    const fetchStocks = useStockListStore()
    //region initialisation des champs pour le formulaire d'ajout d'un stock
    //region champ multi-select composant
    const addStockFormComponentSearchCriteria = useFetchCriteria('addStockFormComponent')
    const fetchComponentStore = useComponentListStore()
    async function updateComponents() {
        await fetchComponentStore.fetchAll(addStockFormComponentSearchCriteria.getFetchCriteria)
        return fetchComponentStore.components
    }
    await updateComponents()
    const optionComposant = computed(() => fetchComponentStore.components.map(item => ({text: `${item.code}`, value: item['@id']})))
    //endregion
    //region champ multi-select produit
    const addStockFormProductSearchCriteria = useFetchCriteria('addStockFormProduct')
    const fetchProductStore = useProductStore()
    async function updateProducts() {
        await fetchProductStore.fetchAll(addStockFormProductSearchCriteria.getFetchCriteria)
        return fetchProductStore.products
    }
    await updateProducts()
    const optionProduit = computed(() => fetchProductStore.products.map(item => ({text: `${item.code}`, value: item['@id']})))
    //endregion
    const itemsNull = {
        itemType: false,
        batchNumber: null,
        jail: false,
        location: null,
        component: null,
        product: null,
        quantity: {
            code: 'U',
            value: 0
        }
    }
    const localAddFormData = ref(itemsNull)
    //region établissement de fieldsForm
    const commonAddFormFields = [
        {
            label: 'Composant(0)/Produit(1)',
            name: 'itemType',
            type: 'boolean'
        },
        {
            label: 'Numéro de série',
            name: 'batchNumber',
            type: 'text'
        },
        {
            label: 'Localisation',
            name: 'location',
            type: 'text'
        },
        {
            label: 'Prison',
            name: 'jail',
            type: 'boolean'
        }
    ]
    const specificComponentAddFormFields = computed(() => [
        {
            label: 'Composant',
            name: 'component',
            options: {label: value => optionComposant.value.find(option => option.value === value)?.text ?? null, options: optionComposant.value},
            type: 'multiselect',
            max: 1
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
            type: 'measure'
        }
    ])
    const specificProductAddFormFields = computed(() => [
        {
            label: 'Produit',
            name: 'product',
            options: {label: value => optionProduit.value.find(option => option.value === value)?.text ?? null, options: optionProduit.value},
            type: 'multiselect',
            max: 1
        },
        {
            label: 'Quantité ',
            name: 'quantity',
            measure: {
                code: { // Mettre U en permanence par défault
                    label: 'Unité',
                    name: 'code',
                    type: 'text',
                    readonly: true
                },
                value: {
                    label: 'Valeur',
                    name: 'value',
                    type: 'number'
                }
            },
            type: 'measure'
        }
    ])
    const fieldsForm = computed(() => {
        if (localAddFormData.value.itemType === true) return [...commonAddFormFields, ...specificProductAddFormFields.value]
        return [...commonAddFormFields, ...specificComponentAddFormFields.value]
    })
    //endregion
    //region définition des fonctions associées au formulaire d'ajout d'un stock

    function ajoute(){
        AddForm.value = true
        updated.value = false
        localAddFormData.value = itemsNull
    }
    function addFormChange(data) {
        //survient la plupart du temps lorsqu'on modifie les valeurs d'un input sans besoin de valider le formulaire ou de sortir du champ
        //Attention ne fonctionne pas pour les MultiSelect => Voir updatedSearch
        console.log('addFormChange', localAddFormData.value, data)
        if (data.itemType === !localAddFormData.value.itemType) {
            switch (data.itemType) {
                case true:
                    localAddFormData.value.quantity = {code: 'U', value: data.quantity.value}
                    localAddFormData.value.component = null
                    localAddFormData.value.itemType = true
                    break
                default:
                    localAddFormData.value.quantity = {code: '', value: data.quantity.value}
                    localAddFormData.value.product = null
                    localAddFormData.value.itemType = false
            }
            //console.log('changement type d\'item', localAddFormData.value)
            addFormKey++
        } else {
            localAddFormData.value = data
        }
    }
    async function updatedSearch(data) {
        //console.log('updatedSearch', data)
        //inputValue.value[data.field.name]=data.data
        switch (data.field.name) {
            case 'component':
                addStockFormComponentSearchCriteria.addFilter('code', data.data)
                //console.log(addStockFormComponentSearchCriteria.getFetchCriteria)
                await updateComponents()
                break
            case 'product':
                addStockFormProductSearchCriteria.addFilter('code', data.data)
                //console.log(addStockFormProductSearchCriteria.getFetchCriteria)
                await updateProducts()
                break
            default:
            //nothing
        }
    }

    async function ajoutWarehouseStock(){
        console.log(localAddFormData.value)
        itemsAddData.value = {
            batchNumber: localAddFormData.value.batchNumber,
            location: localAddFormData.value.location,
            quantity: {code: localAddFormData.value.quantity.code, value: localAddFormData.value.quantity.value},
            jail: localAddFormData.value.jail
        }
        switch (localAddFormData.value.itemType) {
            case false:
                //Ajout d'un stock composant en base
                console.log('composant', fetchUnits.options)
                itemsAddData.value.item = localAddFormData.value.component[0] ?? null
                itemsAddData.value.product = null
                itemsAddData.value.quantity = {
                    code: fetchUnits.find(localAddFormData.value.quantity.code)?.code,
                    value: localAddFormData.value.quantity.value
                }
                break
            default:
                console.log('product')
                //Ajout d'un stock produit en base
                itemsAddData.value.component = null
                itemsAddData.value.item = localAddFormData.value.product[0] ?? null
                itemsAddData.value.quantity = {
                    code: localAddFormData.value.quantity.code,
                    value: localAddFormData.value.quantity.value
                }
        }
        itemsAddData.value.warehouse = `/api/warehouses/${warehouseId}`
        console.log('data to send', itemsAddData.value)
        try {
            await fetchStocks.addStock(itemsAddData.value)
            AddForm.value = false
        } catch (e) {
            alert(e.message)
            violations = fetchStocks.violations
            isPopupVisible.value = true
        }
    }
    function annuleAddStock(){
        AddForm.value = false
        violations = []
        isPopupVisible.value = false
        localAddFormData.value = itemsNull
    }
    //endregion
    //endregion
    //region initalisation des champs pour le tableau de liste des stocks
    // const storeWarehouseStockList = useWarehouseStockListStore()
    // storeWarehouseStockList.setIdWarehouse(warehouseId)
    // await storeWarehouseStockList.fetch()
    // const itemsTable = ref(storeWarehouseStockList.itemsWarehousesStock)
    // const formData = ref({
    //     composant: null, produit: null, numeroDeSerie: null, localisation: null, quantite: null, prison: null
    // })
    //endregion
    // const parent = {
    //     $id: `${warehouseId}Stock`
    // }
    // const storeUnit = useField(fieldsForm[4], parent)
    // storeUnit.fetch()
    //
    // fieldsForm[4].measure.code = storeUnit.measure.code
    // fieldsForm[4].measure.value = storeUnit.measure.value

    // const tabFields = [
    //     {
    //         create: true,
    //         filter: true,
    //         label: 'Composant',
    //         name: 'composant',
    //         options: {label: value => optionComposant.find(option => option.value === value)?.text ?? null, options: optionComposant},
    //         sort: true,
    //         type: 'select',
    //         update: true
    //     },
    //     {
    //         create: true,
    //         filter: true,
    //         label: 'Produit',
    //         name: 'produit',
    //         options: {label: value => optionProduit.find(option => option.value === value)?.text ?? null, options: optionProduit},
    //         sort: true,
    //         type: 'select',
    //         update: true
    //     },
    //     {
    //         create: true,
    //         filter: true,
    //         label: 'Numéro de série',
    //         name: 'numeroDeSerie',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: true,
    //         filter: true,
    //         label: 'Localisation',
    //         name: 'localisation',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: true,
    //         filter: true,
    //         label: 'Quantité ',
    //         name: 'quantite',
    //         measure: {
    //             code: storeUnit.measure.code,
    //             value: storeUnit.measure.value
    //         },
    //         sort: true,
    //         type: 'measure',
    //         update: true
    //     },
    //     {
    //         create: true,
    //         filter: true,
    //         label: 'Prison',
    //         name: 'prison',
    //         sort: true,
    //         type: 'boolean',
    //         update: true
    //     }
    // ]
    //
    // function update(item) {
    //     updated.value = true
    //     AddForm.value = true
    //     const itemsData = {
    //         composant: item.composant,
    //         produit: item.produit,
    //         numeroDeSerie: item.numeroDeSerie,
    //         localisation: item.localisation,
    //         quantite: item.quantite,
    //         prison: item.prison
    //     }
    //     formData.value = itemsData
    // }
    //
    // async function deleted(id){
    //     await storeWarehouseStockList.deleted(id)
    //     itemsTable.value = [...storeWarehouseStockList.itemsWarehousesStock]
    // }
    // async function getPage(nPage){
    //     await storeWarehouseStockList.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
    //     itemsTable.value = [...storeWarehouseStockList.itemsWarehousesStock]
    // }
    // async function trierAlphabet(payload) {
    //     await storeWarehouseStockList.sortableItems(payload, filterBy, filter)
    //     sortable.value = true
    //     trierAlpha = computed(() => payload)
    // }
    // async function search(inputValues) {
    //     let comp = ''
    //     if (typeof inputValues.composant !== 'undefined'){
    //         comp = inputValues.composant
    //     }
    //
    //     let prod = ''
    //     if (typeof inputValues.produit !== 'undefined'){
    //         prod = inputValues.produit
    //     }
    //
    //     const payload = {
    //         composant: comp,
    //         produit: prod,
    //         numeroDeSerie: inputValues.numeroDeSerie ?? '',
    //         localisation: inputValues.localisation ?? '',
    //         quantite: inputValues.quantite ?? '',
    //         prison: inputValues.prison ?? ''
    //     }
    //
    //     if (typeof payload.quantite.value === 'undefined' && payload.quantite !== '') {
    //         payload.quantite.value = ''
    //     }
    //     if (typeof payload.quantite.code === 'undefined' && payload.quantite !== '') {
    //         payload.quantite.code = ''
    //     }
    //     await storeWarehouseStockList.filterBy(payload)
    //     itemsTable.value = [...storeWarehouseStockList.itemsWarehousesStock]
    //     filter.value = true
    //     filterBy = computed(() => payload)
    // }
    // async function cancelSearch() {
    //     filter.value = true
    //     storeWarehouseStockList.fetch()
    // }
    // const allCountries = ref([])
    // allCountries.value = [
    //     { value: 'FR', text: 'France'},
    //     { value: 'TN', text: 'Tunisie'},
    //     { value: 'MD', text: 'Moldavie'}
    // ]
    // const isLoading = ref(false)
    // const countries = ref([])
    // const selectedCountries = ref([])
    // function asyncFind(query) {
    //     isLoading.value = true
    //     const result = allCountries.value.filter(item => {
    //         return item.text.toLowerCase().includes(query.toLowerCase())
    //     })
    //     countries.value = result
    //     isLoading.value = false
    // }
    // function limitText(count) {
    //     return `and ${count} other countries`
    // }
</script>

<template>
    <AppCol class="d-flex justify-content-between mb-2">
        <span>
            <AppBtn variant="success" label="Ajout" @click="ajoute">
                <Fa icon="plus"/>
                Créer un nouveau stock
            </AppBtn>
        </span>
    </AppCol>
    <AppRow>
        <AppCol>
        <!--            <AppCardableTable-->
        <!--                :current-page="storeWarehouseStockList.currentPage"-->
        <!--                :fields="tabFields"-->
        <!--                :first-page="storeWarehouseStockList.firstPage"-->
        <!--                :items="itemsTable"-->
        <!--                :last-page="storeWarehouseStockList.lastPage"-->
        <!--                :min="AddForm"-->
        <!--                :next-page="storeWarehouseStockList.nextPage"-->
        <!--                :pag="storeWarehouseStockList.pagination"-->
        <!--                :previous-page="storeWarehouseStockList.previousPage"-->
        <!--                :user="roleuser"-->
        <!--                form="formWarehouseCardableTable"-->
        <!--                @update="update"-->
        <!--                @deleted="deleted"-->
        <!--                @get-page="getPage"-->
        <!--                @trier-alphabet="trierAlphabet"-->
        <!--                @search="search"-->
        <!--                @cancel-search="cancelSearch"/>-->
        </AppCol>
        <AppCol v-if="AddForm && !updated" class="col-7">
            <AppCard class="bg-blue col" title="">
                <AppRow>
                    <button id="btnRetour1" class="btn btn-danger btn-icon btn-sm col-1" @click="annuleAddStock">
                        <Fa icon="angle-double-left"/>
                    </button>
                    <h4 class="col">
                        <Fa icon="plus"/> Ajout
                    </h4>
                </AppRow>
                <br/>
                <AppFormCardable
                    id="addWarehouseStock"
                    :key="addFormKey"
                    :fields="fieldsForm"
                    :model-value="localAddFormData"
                    label-cols
                    @update:model-value="addFormChange"
                    @search-change="updatedSearch"/>
                <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                    <div v-for="violation in violations" :key="violation">
                        <li>{{ violation.message }}</li>
                    </div>
                </div>
                <AppCol class="btnright">
                    <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutWarehouseStock">
                        <Fa icon="plus"/> Ajouter
                    </AppBtn>
                </AppCol>
            </AppCard>
        </AppCol>
    </AppRow>
</template>

<style scoped>
    .btn-float-right{
        float: right;
    }
</style>
