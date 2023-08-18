<script setup>
    import {computed, ref} from 'vue'
    import {useProductListCompositionStore} from '../../../../../stores/project/product/productListComposition'
    import {useRoute} from 'vue-router'
    import useField from '../../../../../stores/field/field'

    const roleuser = ref('reader')
    // let violations = []
    const updated = ref(false)
    const AddForm = ref(false)
    // const isPopupVisible = ref(false)
    const sortable = ref(false)
    const filter = ref(false)
    let trierAlpha = {}
    let filterBy = {}

    const maRoute = useRoute()
    const productId = maRoute.params.id_product

    const storeProductListComposition = useProductListCompositionStore()
    storeProductListComposition.setIdProduct(productId)
    await storeProductListComposition.fetch()
    const itemsTable = ref(storeProductListComposition.itemsProductComposition)
    const formData = ref({
        refDesignation: null, photo: null, famille: null, quantity: null, stkSite: null, capFabSite: null, stkTotal: null, capFabTconcept: null
    })

    const fieldsForm = [
        {
            create: false,
            filter: true,
            label: 'Ref - Designation',
            name: 'refDesignation',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Photo.',
            name: 'photo ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            label: 'Famille ',
            name: 'famille',
            sort: false,
            // options: [
            //     {
            //         disabled: false,
            //         label: 'Prison',
            //         value: 'prison'
            //     },
            //     {
            //         disabled: false,
            //         label: 'ProductCompositionion',
            //         value: 'production'
            //     },
            //     {
            //         disabled: false,
            //         label: 'Réception',
            //         value: 'réception'
            //     },
            //     {
            //         disabled: false,
            //         label: 'Magasin pièces finies',
            //         value: 'magasin pièces finies'
            //     },
            //     {
            //         disabled: false,
            //         label: 'Expédition',
            //         value: 'expédition'
            //     },
            //     {
            //         disabled: false,
            //         label: 'Magasin matières premières',
            //         value: 'magasin matières premières'
            //     },
            //     {
            //         disabled: false,
            //         label: 'Camion',
            //         value: 'camion'
            //     }
            // ],
            // optionsList: {
            //     camion: 'camion',
            //     expédition: 'expédition',
            //     'magasin matières premières': 'magasin matières premières',
            //     'magasin pièces finies': 'magasin pièces finies',
            //     prison: 'prison',
            //     production: 'production',
            //     réception: 'réception'
            // },
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantity ',
            name: 'quantity ',
            measure: {
                code: null,
                value: null
            },
            sort: false,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Stk site',
            name: 'stkSite',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Cap.Fab.Site',
            name: 'capFabSite ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Stk total',
            name: 'stkTotal',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Cap.Fab. Tconcept',
            name: 'capFabTconcept',
            sort: false,
            type: 'text',
            update: true
        }
    ]

    const parent = {
        //$id: `${warehouseId}Stock`
        $id: 'productProductComposition'
    }
    const storeUnit = useField(fieldsForm[3], parent)
    storeUnit.fetch()

    fieldsForm[3].measure.code = storeUnit.measure.code
    fieldsForm[3].measure.value = storeUnit.measure.value

    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Ref - Designation',
            name: 'refDesignation',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Photo.',
            name: 'photo ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            label: 'Famille ',
            name: 'famile',
            sort: false,
            // options: [
            //     {
            //         disabled: false,
            //         label: 'Prison',
            //         value: 'prison'
            //     },
            //     {
            //         disabled: false,
            //         label: 'ProductCompositionion',
            //         value: 'production'
            //     },
            //     {
            //         disabled: false,
            //         label: 'Réception',
            //         value: 'réception'
            //     },
            //     {
            //         disabled: false,
            //         label: 'Magasin pièces finies',
            //         value: 'magasin pièces finies'
            //     },
            //     {
            //         disabled: false,
            //         label: 'Expédition',
            //         value: 'expédition'
            //     },
            //     {
            //         disabled: false,
            //         label: 'Magasin matières premières',
            //         value: 'magasin matières premières'
            //     },
            //     {
            //         disabled: false,
            //         label: 'Camion',
            //         value: 'camion'
            //     }
            // ],
            // optionsList: {
            //     camion: 'camion',
            //     expédition: 'expédition',
            //     'magasin matières premières': 'magasin matières premières',
            //     'magasin pièces finies': 'magasin pièces finies',
            //     prison: 'prison',
            //     production: 'production',
            //     réception: 'réception'
            // },
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantity ',
            name: 'quantity ',
            measure: {
                code: storeUnit.measure.code,
                value: storeUnit.measure.value
            },
            sort: false,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Stk site',
            name: 'stkSite',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Cap.Fab.Site',
            name: 'capFabSite ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Stk total',
            name: 'stkTotal',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Cap.Fab. Tconcept',
            name: 'capFabTconcept',
            sort: false,
            type: 'text',
            update: true
        }
    ]

    // function ajoute(){
    //     AddForm.value = true
    //     updated.value = false
    //     const itemsNull = {
    //         refDesignation: null,
    //         photo: null,
    //         famille: null,
    //         quantity: null,
    //         stkSite: null,
    //         capFabSite: null,
    //         stkTotal: null,
    //         capFabTconcept: null
    //     }
    //     formData.value = itemsNull
    // }

    // async function ajoutProductComposition(){
    //     const form = document.getElementById('addProductComposition')
    //     const formData1 = new FormData(form)

    //     // if (typeof formData.value.families !== 'undefined') {
    //     //     formData.value.famille = JSON.parse(JSON.stringify(formData.value.famille))
    //     // }

    //     const itemsAddData = {
    //         refDesignation: formData.value.refDesignation,
    //         photo: formData.value.photo,
    //         famille: formData.value.dateConfifamillermation,
    //         quantite: {code: formData1.get('quantite[code]'), value: formData1.get('quantite[value]')},
    //         stkSite: formData.value.stkSite,
    //         stkTotal: formData.value.stkTotal,
    //         capFabTconcept: formData.value.capFabTconcept
    //     }
    //     violations = await storeProductListComposition.addProductComposition(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeProductListComposition.itemsProductComposition]
    //     }
    // }
    // function annule(){
    //     AddForm.value = false
    //     updated.value = false
    //     const itemsNull = {
    //         refDesignation: null,
    //         photo: null,
    //         famille: null,
    //         quantity: null,
    //         stkSite: null,
    //         capFabSite: null,
    //         stkTotal: null,
    //         capFabTconcept: null
    //     }
    //     formData.value = itemsNull
    //     isPopupVisible.value = false
    // }

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
        // let comp = ''
        // if (typeof inputValues.composant !== 'undefined'){
        //     comp = inputValues.composant
        // }

        // let prod = ''
        // if (typeof inputValues.produit !== 'undefined'){
        //     prod = inputValues.produit
        // }

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
