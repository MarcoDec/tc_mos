<script setup>
    import {computed, ref} from 'vue'
    import useOptions from '../../../../../stores/option/options'
    import {useProductStocksListStore} from '../../../../../stores/project/product/productStockList'
    import {useRoute} from 'vue-router'
    import useField from '../../../../../stores/field/field'

    const roleuser = ref('reader')
    const updated = ref(false)
    const AddForm = ref(false)
    const sortable = ref(false)
    const filter = ref(false)
    const itemsTable = ref([])
    let trierAlpha = {}
    let filterBy = {}

    const maRoute = useRoute()
    const productId = maRoute.params.id_product
    //récupération des unités déjà chargés
    const fetchUnits = useOptions('units')
    //console.log('units', fetchUnits.options)

    const storeProductListComposition = useProductStocksListStore()
    storeProductListComposition.setIdProduct(productId)
    await storeProductListComposition.fetch()
    console.log('productComposition', storeProductListComposition.productComposition)
    console.log('itemsProductComposition', storeProductListComposition.itemsProductComposition)
    itemsTable.value = storeProductListComposition.itemsProductComposition
    const formData = ref({
        refDesignation: null, photo: null, famille: null, quantity: null, stkSite: null, capFabSite: null, stkTotal: null, capFabTconcept: null
    })

    const fieldsForm = [
        {
            label: 'Ref - Designation',
            name: 'refDesignation',
            type: 'text'
        },
        {
            label: 'Photo.',
            name: 'photo ',
            type: 'text'
        },
        {
            label: 'Famille ',
            name: 'famille',
            type: 'text'
        },
        {
            label: 'Quantity ',
            name: 'quantity ',
            measure: {
                code: null,
                value: null
            },
            type: 'measure'
        },
        {
            label: 'Stk site',
            name: 'stkSite',
            type: 'text'
        },
        {
            label: 'Cap.Fab.Site',
            name: 'capFabSite ',
            type: 'text'
        },
        {
            label: 'Stk total',
            name: 'stkTotal',
            type: 'text'
        },
        {
            label: 'Cap.Fab. Tconcept',
            name: 'capFabTconcept',
            type: 'text'
        }
    ]

    const parent = {
        $id: 'productProductComposition'
    }
    const storeUnit = useField(fieldsForm[3], parent)
    storeUnit.fetch()

    fieldsForm[3].measure.code = storeUnit.measure.code
    fieldsForm[3].measure.value = storeUnit.measure.value

    const tabFields = [
        {
            filter: true,
            label: 'Ref - Designation',
            name: 'refDesignation',
            sort: false,
            type: 'text'
        },
        {
            filter: true,
            label: 'Photo.',
            name: 'photo ',
            sort: false,
            type: 'text'
        },
        {
            filter: false,
            label: 'Famille ',
            name: 'famile',
            sort: false,
            type: 'text'
        },
        {
            filter: true,
            label: 'Quantity',
            name: 'quantity',
            measure: {
                code: {
                    label: 'Unité',
                    name: 'code',
                    options: {
                        label: value =>
                            fetchUnits.options.find(option => option.type === value)?.text ?? null,
                        options: fetchUnits.options
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
        },
        {
            filter: true,
            label: 'Stk site',
            name: 'stkSite',
            sort: false,
            type: 'text'
        },
        {
            filter: true,
            label: 'Cap.Fab.Site',
            name: 'capFabSite ',
            sort: false,
            type: 'text'
        },
        {
            filter: true,
            label: 'Stk total',
            name: 'stkTotal',
            sort: false,
            type: 'text'
        },
        {
            filter: true,
            label: 'Cap.Fab. Tconcept',
            name: 'capFabTconcept',
            sort: false,
            type: 'text'
        }
    ]

    function update(item) {
        updated.value = true
        AddForm.value = true
        const itemsData = {
            refDesignation: item.refDesignation,
            photo: item.photo,
            famille: item.famille,
            quantity: item.quantity,
            stkSite: item.stkSite,
            capFabSite: item.capFabSite,
            stkTotal: item.stkTotal,
            capFabTconcept: item.capFabTconcept
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeProductListComposition.deleted(id)
        itemsTable.value = [...storeProductListComposition.itemsProductComposition]
    }
    async function getPage(nPage){
        await storeProductListComposition.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeProductListComposition.itemsProductComposition]
    }
    async function trierAlphabet(payload) {
        await storeProductListComposition.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }
    async function search(inputValues) {
        const payload = {
            refDesignation: inputValues.refDesignation ?? '',
            photo: inputValues.photo ?? '',
            famille: inputValues.famille ?? '',
            quantite: inputValues.quantity ?? '',
            stkSite: inputValues.stkSite ?? '',
            capFabSite: inputValues.capFabSite ?? '',
            stkTotal: inputValues.stkTotal ?? '',
            capFabTconcept: inputValues.capFabTconcept ?? ''
        }

        if (typeof payload.quantite.value === 'undefined' && payload.quantite !== '') {
            payload.quantite.value = ''
        }
        if (typeof payload.quantite.code === 'undefined' && payload.quantite !== '') {
            payload.quantite.code = ''
        }
        await storeProductListComposition.filterBy(payload)
        itemsTable.value = [...storeProductListComposition.itemsProductComposition]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeProductListComposition.fetch()
    }
</script>

<template>
    <div class="gui-bottom">
        <!-- <AppCol class="d-flex justify-content-between mb-2">
            <AppBtn variant="success" label="Ajout" @click="ajoute">
                <Fa icon="plus"/>
                Ajouter
            </AppBtn>
        </AppCol> -->
        <AppRow>
            <AppCol>
                <AppCardableTable
                    :current-page="storeProductListComposition.currentPage"
                    :fields="tabFields"
                    :first-page="storeProductListComposition.firstPage"
                    :items="itemsTable"
                    :last-page="storeProductListComposition.lastPage"
                    :min="AddForm"
                    :next-page="storeProductListComposition.nextPage"
                    :pag="storeProductListComposition.pagination"
                    :previous-page="storeProductListComposition.previousPage"
                    :user="roleuser"
                    form="formProductCompositionCardableTable"
                    @update="update"
                    @deleted="deleted"
                    @get-page="getPage"
                    @trier-alphabet="trierAlphabet"
                    @search="search"
                    @cancel-search="cancelSearch"/>
            </AppCol>
            <!-- <AppCol v-if="AddForm && !updated" class="col-7">
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
                    <AppFormCardable id="addProductComposition" :fields="fieldsForm" :model-value="formData" label-cols/>
                    <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                        <div v-for="violation in violations" :key="violation">
                            <li>{{ violation.message }}</li>
                        </div>
                    </div>
                    <AppCol class="btnright">
                        <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutProductComposition">
                            <Fa icon="plus"/> Ajouter
                        </AppBtn>
                    </AppCol>
                </AppCard>
            </AppCol> -->
        </AppRow>
    </div>
</template>

<style scoped>
    .btn-float-right{
        float: right;
    }

    .gui-bottom {
        overflow: hidden;
    }
</style>
