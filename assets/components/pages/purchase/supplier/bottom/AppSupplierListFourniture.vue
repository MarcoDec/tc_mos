<script setup>
    import {computed, ref} from 'vue'
    import {useSupplierListFournitureStore} from '../../../../../stores/supplier/supplierListFourniture'
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
    const supplierID = maRoute.params.id_supplier

    const storeSupplierListFourniture = useSupplierListFournitureStore()
    storeSupplierListFourniture.setIdSupplier(supplierID)
    await storeSupplierListFourniture.fetch()
    const itemsTable = ref(storeSupplierListFourniture.itemsSupplierFourniture)
    const formData = ref({
        composant: null, refFournisseur: null, prix: null, quantite: null, texte: null
    })

    const optionComposant = await storeSupplierListFourniture.getOptionComposant
    const fieldsForm = [
        {
            create: false,
            filter: true,
            label: 'Composant',
            name: 'composant',
            options: {label: value => optionComposant.find(option => option.value === value)?.text.code ?? null, options: optionComposant},
            sort: true,
            type: 'select',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Ref. Fournisseur ',
            name: 'refFournisseur',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Prix ',
            name: 'prix',
            measure: {
                code: null,
                value: null
            },
            sort: true,
            type: 'price',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité ',
            name: 'quantite',
            measure: {
                code: null,
                value: null
            },
            sort: true,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Texte ',
            name: 'texte',
            sort: true,
            type: 'text',
            update: true
        }
    ]

    const parentQuantityFourniture = {
        //$id: `${warehouseId}Stock`
        $id: 'supplierFourniture'
    }
    const storeUnitQtyFourniture = useField(fieldsForm[3], parentQuantityFourniture)
    storeUnitQtyFourniture.fetch()

    fieldsForm[3].measure.code = storeUnitQtyFourniture.measure.code
    fieldsForm[3].measure.value = storeUnitQtyFourniture.measure.value

    const parentPrice = {
        $id: 'supplierFourniturePrice'
    }
    const storeUnitPrice = useField(fieldsForm[2], parentPrice)
    storeUnitPrice.fetch()

    fieldsForm[2].measure.code = storeUnitPrice.measure.code
    fieldsForm[2].measure.value = storeUnitPrice.measure.value

    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Composant',
            name: 'composant',
            options: {label: value => optionComposant.find(option => option.value === value)?.text.code ?? null, options: optionComposant},
            sort: true,
            type: 'select',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Ref. Fournisseur ',
            name: 'refFournisseur',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Prix ',
            name: 'prix',
            measure: {
                code: storeUnitPrice.measure.code,
                value: storeUnitPrice.measure.value
            },
            sort: true,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité ',
            name: 'quantite',
            measure: {
                code: storeUnitQtyFourniture.measure.code,
                value: storeUnitQtyFourniture.measure.value
            },
            sort: true,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Texte ',
            name: 'texte',
            sort: true,
            type: 'text',
            update: true
        }
    ]

    // function ajoute(){
    //     AddForm.value = true
    //     updated.value = false
    //     const itemsNull = {
    //         composant: null,
    //         refFournisseur: null,
    //         prix: null,
    //         quantité: null,
    //         texte: null
    //     }
    //     formData.value = itemsNull
    // }

    // async function ajoutSupplierFourniture(){
    //     const form = document.getElementById('addSupplierFourniture')
    //     const formData1 = new FormData(form)

    //     const itemsAddData = {
    //         composant: formData.value.composant,
    //         refFournisseur: formData.value.refFournisseur,
    //         prix: {code: formData1.get('prix[code]'), value: formData1.get('prix[value]')},
    //         quantite: {code: formData1.get('quantite[code]'), value: formData1.get('quantite[value]')},
    //         texte: formData.value.texte
    //     }
    //     violations = await storeSupplierListFourniture.addSupplierFourniture(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeSupplierListFourniture.itemsSupplierFourniture]
    //     }
    // }
    // function annule(){
    //     AddForm.value = false
    //     updated.value = false
    //     const itemsNull = {
    //         composant: null,
    //         refFournisseur: null,
    //         prix: null,
    //         quantite: null,
    //         texte: null
    //     }
    //     formData.value = itemsNull
    //     isPopupVisible.value = false
    // }

    function update(item) {
        updated.value = true
        AddForm.value = true
        const itemsData = {
            composant: item.composant,
            refFournisseur: item.refFournisseur,
            prix: item.prix,
            quantite: item.quantite,
            texte: item.texte
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeSupplierListFourniture.deleted(id)
        itemsTable.value = [...storeSupplierListFourniture.itemsSupplierFourniture]
    }
    async function getPage(nPage){
        await storeSupplierListFourniture.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeSupplierListFourniture.itemsSupplierFourniture]
    }
    async function trierAlphabet(payload) {
        await storeSupplierListFourniture.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }
    async function search(inputValues) {
        let comp = ''
        if (typeof inputValues.composant !== 'undefined'){
            comp = inputValues.composant
        }

        // let prod = ''
        // if (typeof inputValues.produit !== 'undefined'){
        //     prod = inputValues.produit
        // }

        const payload = {
            composant: comp,
            refFournisseur: inputValues.refFournisseur ?? '',
            prix: inputValues.prix ?? '',
            quantite: inputValues.quantite ?? '',
            texte: inputValues.texte ?? ''
        }

        if (typeof payload.quantite.value === 'undefined' && payload.quantite !== '') {
            payload.quantite.value = ''
        }
        if (typeof payload.quantite.code === 'undefined' && payload.quantite !== '') {
            payload.quantite.code = ''
        }

        if (typeof payload.prix.value === 'undefined' && payload.prix !== '') {
            payload.prix.value = ''
        }
        if (typeof payload.prix.code === 'undefined' && payload.prix !== '') {
            payload.prix.code = ''
        }

        await storeSupplierListFourniture.filterBy(payload)
        itemsTable.value = [...storeSupplierListFourniture.itemsSupplierFourniture]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeSupplierListFourniture.fetch()
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
                    :current-page="storeSupplierListFourniture.currentPage"
                    :fields="tabFields"
                    :first-page="storeSupplierListFourniture.firstPage"
                    :items="itemsTable"
                    :last-page="storeSupplierListFourniture.lastPage"
                    :min="AddForm"
                    :next-page="storeSupplierListFourniture.nextPage"
                    :pag="storeSupplierListFourniture.pagination"
                    :previous-page="storeSupplierListFourniture.previousPage"
                    :user="roleuser"
                    form="formSupplierCardableTable"
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
                    <AppFormCardable id="addSupplierFourniture" :fields="fieldsForm" :model-value="formData" label-cols/>
                    <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                        <div v-for="violation in violations" :key="violation">
                            <li>{{ violation.message }}</li>
                        </div>
                    </div>
                    <AppCol class="btnright">
                        <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutSupplierFourniture">
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
