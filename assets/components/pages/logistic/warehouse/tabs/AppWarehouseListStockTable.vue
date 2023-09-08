<script setup>
    import {computed, ref} from 'vue'
    import Multiselect from '@vueform/multiselect'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import {useWarehouseStockListStore} from '../provisoir/warehouseStockList'
    import {useRoute} from 'vue-router'
    import useField from '../../../../../stores/field/field'
    import {useComponentListStore} from '../../../../../stores/purchase/component/components'
    import {useProductStore} from '../../../../../stores/project/product/products'
    import AppFormCardable from '../../../../form-cardable/AppFormCardable'

    const roleuser = ref('reader')
    let violations = []
    const updated = ref(false)
    const AddForm = ref(false)
    const isPopupVisible = ref(false)
    const sortable = ref(false)
    const filter = ref(false)
    let trierAlpha = {}
    let filterBy = {}

    //region récupération des informations de route
    const maRoute = useRoute()
    const warehouseId = maRoute.params.id_warehouse
    //endregion
    //region initialisation des champs pour le formulaire d'ajout d'un stock
    //region champ multi-select composant
    const addStockFormComponentSearchCriteria = useFetchCriteria('addStockFormComponent')
    const fetchComponentStore = useComponentListStore()
    async function updateComponents() {
        await fetchComponentStore.fetchAll(addStockFormComponentSearchCriteria.getFetchCriteria)
        return fetchComponentStore.components
    }
    await updateComponents()
    const optionComposant = computed(() => fetchComponentStore.components.map(item => {
        return {text: `${item.code}`, value: item['@id']}
    }))
    //endregion
    //region champ multi-select produit
    const addStockFormProductSearchCriteria = useFetchCriteria('addStockFormProduct')
    const fetchProductStore = useProductStore()
    async function updateProducts() {
        await fetchProductStore.fetchAll(addStockFormProductSearchCriteria.getFetchCriteria)
        return fetchProductStore.products
    }
    await updateProducts()
    const optionProduit = computed(() => fetchProductStore.products.map(item => {
        return {text: `${item.code}`, value: item['@id']}
    }))
    //endregion
    //region établissement de fieldsForm

    const fieldsForm = computed(()=>[
        {
            create: true,
            filter: true,
            label: 'Composant',
            name: 'composant',
            options: {label: value => optionComposant.value.find(option => option.value === value)?.text ?? null, options: optionComposant.value},
            type: 'multiselect',
            update: true
        }
        ,
        {
            create: true,
            filter: true,
            label: 'Produit',
            name: 'produit',
            options: {label: value => optionProduit.value.find(option => option.value === value)?.text ?? null, options: optionProduit.value},
            type: 'multiselect',
            update: true
        }
        // ,
        // {
        //     create: true,
        //     filter: true,
        //     label: 'Numéro de série',
        //     name: 'numeroDeSerie',
        //     sort: true,
        //     type: 'text',
        //     update: true
        // },
        // {
        //     create: true,
        //     filter: true,
        //     label: 'Localisation',
        //     name: 'localisation',
        //     sort: true,
        //     type: 'text',
        //     update: true
        // },
        // {
        //     create: true,
        //     filter: true,
        //     label: 'Quantité ',
        //     name: 'quantite',
        //     measure: {
        //         code: null,
        //         value: null
        //     },
        //     sort: true,
        //     type: 'measure',
        //     update: true
        // },
        // {
        //     create: true,
        //     filter: true,
        //     label: 'Prison',
        //     name: 'prison',
        //     sort: true,
        //     type: 'boolean',
        //     update: true
        // }
    ])
    //endregion
    //endregion
    //region initalisation des champs pour le tableau de liste des stocks
    const storeWarehouseStockList = useWarehouseStockListStore()
    storeWarehouseStockList.setIdWarehouse(warehouseId)
    await storeWarehouseStockList.fetch()
    const itemsTable = ref(storeWarehouseStockList.itemsWarehousesStock)
    const formData = ref({
        composant: null, produit: null, numeroDeSerie: null, localisation: null, quantite: null, prison: null
    })
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

    function ajoute(){
        AddForm.value = true
        updated.value = false
        const itemsNull = {
            numeroDeSerie: null,
            localisation: null,
            quantite: null,
            prison: null
        }
        formData.value = itemsNull
    }
    // async function ajoutWarehouseStock(){
    //     const form = document.getElementById('addWarehouseStock')
    //     const formData1 = new FormData(form)
    //
    //     const itemsAddData = {
    //         composant: formData1.get('composant'),
    //         produit: formData1.get('produit'),
    //         numeroDeSerie: formData1.get('numeroDeSerie'),
    //         localisation: formData1.get('localisation'),
    //         quantite: {code: formData1.get('quantite[code]'), value: formData1.get('quantite[value]')},
    //         prison: formData1.get('prison')
    //     }
    //     violations = await storeWarehouseStockList.addWarehouseStock(itemsAddData)
    //
    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeWarehouseStockList.itemsWarehousesStock]
    //     }
    // }
    // function annule(){
    //     AddForm.value = false
    //     updated.value = false
    //     const itemsNull = {
    //         composant: null,
    //         produit: null,
    //         numeroDeSerie: null,
    //         localisation: null,
    //         quantite: null,
    //         prison: null
    //     }
    //     formData.value = itemsNull
    //     isPopupVisible.value = false
    // }
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
    function addFormChange(data) {
        console.log('addFormChange', data)
    }
    const inputValue = ref({
        composant: ''
    })
    async function updatedSearch(data) {
        inputValue.value[data.field.name]=data.data
        if (data.field.name === 'composant') {
            addStockFormComponentSearchCriteria.addFilter('code', data.data)
        }
        await updateComponents()
        console.log(fetchComponentStore.components)
    }
</script>

<template>
    <AppCol class="d-flex justify-content-between mb-2">
        <span>
            <AppBtn variant="success" label="Ajout" @click="ajoute">
                <Fa icon="plus"/>
                Créer un nouveau stock
            </AppBtn>
        </span>
        <!--        <span>-->
        <!--            <div>Selected Countries => {{ selectedCountries }}</div>-->
        <!--            <br/>-->
        <!--            <Multiselect-->
        <!--                v-model="selectedCountries"-->
        <!--                mode="tags"-->
        <!--                id="ajax"-->
        <!--                label="text"-->
        <!--                track-by="value"-->
        <!--                :options="allCountries"/>-->
        <!--        </span>-->
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
                    <button id="btnRetour1" class="btn btn-danger btn-icon btn-sm col-1" @click="annule">
                        <Fa icon="angle-double-left"/>
                    </button>
                    <h4 class="col">
                        <Fa icon="plus"/> Ajout
                    </h4>
                </AppRow>
                <br/>
                <AppFormCardable
                    id="addWarehouseStock"
                    :fields="fieldsForm"
                    :model-value="formData"
                    label-cols
                    @update:modelValue="addFormChange"
                    @search-change="updatedSearch"
                    @input="addFormInput"
                />
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
