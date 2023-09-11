<script setup>
    import {computed, ref} from 'vue'
    import useFetchCriteria from '../../../../../../stores/fetch-criteria/fetchCriteria'
    //import {useWarehouseStockListStore} from '../provisoir/warehouseStockList'
    import {useStockListStore} from '../../../../../../stores/logistic/stocks/stocks'
    import {useRoute} from 'vue-router'
    import {useComponentListStore} from '../../../../../../stores/purchase/component/components'
    import {useProductStore} from '../../../../../../stores/project/product/products'
    import AppSuspense from '../../../../../AppSuspense.vue'
    import AppFormCardable from '../../../../../form-cardable/AppFormCardable'
    import useOptions from '../../../../../../stores/option/options'
    import WarehouseStockAddForm from './WarehouseStockAddForm.vue'

    // const roleuser = ref('reader')
    const updated = ref(false)
    const AddForm = ref(false)
    // const sortable = ref(false)
    // const filter = ref(false)
    // let trierAlpha = {}
    // let filterBy = {}


    function ajoute(){
        AddForm.value = true
        updated.value = false
    }
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
            <AppCardableTable
                :current-page="storeWarehouseStockList.currentPage"
                :fields="tabFields"
                :first-page="storeWarehouseStockList.firstPage"
                :items="itemsTable"
                :last-page="storeWarehouseStockList.lastPage"
                :min="AddForm"
                :next-page="storeWarehouseStockList.nextPage"
                :pag="storeWarehouseStockList.pagination"
                :previous-page="storeWarehouseStockList.previousPage"
                :user="roleuser"
                form="formWarehouseCardableTable"
                @update="update"
                @deleted="deleted"
                @get-page="getPage"
                @trier-alphabet="trierAlphabet"
                @search="search"
                @cancel-search="cancelSearch"/>
        </AppCol>
        <AppCol v-show="AddForm && !updated" class="col-7">
            <AppSuspense>
                <WarehouseStockAddForm @cancel="AddForm = false" @saved="AddForm = false"/>
            </AppSuspense>
        </AppCol>
    </AppRow>
</template>

<style scoped>
    .btn-float-right{
        float: right;
    }
</style>
