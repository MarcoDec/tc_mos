<script setup>
    import {computed, ref} from 'vue'
    import {useComponentListStockStore} from '../../../../../stores/purchase/component/componentListStock'
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
    const componentId = maRoute.params.id_component

    const storeComponentListStock = useComponentListStockStore()
    storeComponentListStock.setIdComponent(componentId)
    await storeComponentListStock.fetch()
    const itemsTable = ref(storeComponentListStock.itemsComponentStock)
    const formData = ref({
        name: null, etat: null, quantiteDispo: null
    })

    const fieldsForm = [
        {
            create: false,
            filter: true,
            label: 'Nom de l\'entrepot',
            name: 'name',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Prison',
            name: 'etat',
            sort: true,
            type: 'boolean',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Stock Disponible',
            name: 'quantiteDispo',
            sort: true,
            measure: {
                value: null,
                code: null
            },
            type: 'measure',
            update: true
        }
    ]

    const parentQtyDispo = {
        $id: 'componentStockDispo'
    }
    const storeUnitStockDispo = useField(fieldsForm[2], parentQtyDispo)
    await storeUnitStockDispo.fetch()
    fieldsForm[2].measure.code = storeUnitStockDispo.measure.code
    fieldsForm[2].measure.value = storeUnitStockDispo.measure.value

    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Nom de l\'entrepot',
            name: 'name',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Prison',
            name: 'etat',
            sort: true,
            type: 'boolean',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Stock Disponible',
            name: 'quantiteDispo',
            sort: true,
            measure: {
                value: storeUnitStockDispo.measure.value,
                code: storeUnitStockDispo.measure.code
            },
            type: 'measure',
            update: true
        }
    ]

    // function ajoute(){
    //     AddForm.value = true
    //     updated.value = false
    //     const itemsNull = {
    //         client: null,
    //         reference: null,
    //         quantiteConfirmee: null,
    //         quantiteSouhaitee: null,
    //         quantiteEffectuee: null,
    //         dateLivraison: null,
    //         dateLivraisonSouhaitee: null
    //     }
    //     formData.value = itemsNull
    // }

    // async function ajoutComponentStock(){
    //     // const form = document.getElementById('addComponentStock')
    //     // const formData1 = new FormData(form)

    //     // if (typeof formData.value.families !== 'undefined') {
    //     //     formData.value.famille = JSON.parse(JSON.stringify(formData.value.famille))
    //     // }

    //     const itemsAddData = {
    //         client: formData.value.client,
    //         reference: formData.value.reference,
    //         quantiteConfirmee: formData.value.quantiteConfirmee,
    //         //quantite: {code: formData1.get('quantite[code]'), value: formData1.get('quantite[value]')},
    //         quantiteSouhaitee: formData.value.quantiteSouhaitee,
    //         quantiteEffectuee: formData.value.quantiteEffectuee,
    //         dateLivraison: formData.value.dateLivraison,
    //         dateLivraisonSouhaitee: formData.value.dateLivraisonSouhaitee
    //     }
    //     violations = await storeComponentListStock.addComponentStock(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeComponentListStock.itemsComponentStock]
    //     }
    // }
    // function annule(){
    //     AddForm.value = false
    //     updated.value = false
    //     const itemsNull = {
    //         client: null,
    //         reference: null,
    //         quantiteConfirmee: null,
    //         quantiteSouhaitee: null,
    //         quantiteEffectuee: null,
    //         dateLivraison: null,
    //         dateLivraisonSouhaitee: null
    //     }
    //     formData.value = itemsNull
    //     isPopupVisible.value = false
    // }

    function update(item) {
        updated.value = true
        AddForm.value = true
        const itemsData = {
            name: item.name,
            etat: item.etat,
            quantiteDispo: item.quantiteDispo
        }
        formData.value = itemsData
    }

    // async function deleted(id){
    //     await storeComponentListStock.deleted(id)
    //     itemsTable.value = [...storeComponentListStock.itemsComponentStock]
    // }
    async function getPage(nPage){
        await storeComponentListStock.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeComponentListStock.itemsComponentStock]
    }
    async function trierAlphabet(payload) {
        await storeComponentListStock.sortableItems(payload, filterBy, filter)
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
            name: inputValues.name ?? '',
            etat: inputValues.etat ?? '',
            quantiteDispo: inputValues.quantiteDispo ?? ''
        }

        if (typeof payload.quantiteDispo.value === 'undefined' && payload.quantiteDispo !== '') {
            payload.quantiteDispo.value = ''
        }
        if (typeof payload.quantiteDispo.code === 'undefined' && payload.quantiteDispo !== '') {
            payload.quantiteDispo.code = ''
        }

        await storeComponentListStock.filterBy(payload)
        itemsTable.value = [...storeComponentListStock.itemsComponentStock]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeComponentListStock.fetch()
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
                    :current-page="storeComponentListStock.currentPage"
                    :fields="tabFields"
                    :first-page="storeComponentListStock.firstPage"
                    :items="itemsTable"
                    :last-page="storeComponentListStock.lastPage"
                    :min="AddForm"
                    :next-page="storeComponentListStock.nextPage"
                    :pag="storeComponentListStock.pagination"
                    :previous-page="storeComponentListStock.previousPage"
                    :user="roleuser"
                    :should-delete="false"
                    form="formComponentStockCardableTable"
                    @update="update"
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
                    <AppFormCardable id="addComponentStock" :fields="fieldsForm" :model-value="formData" label-cols/>
                    <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                        <div v-for="violation in violations" :key="violation">
                            <li>{{ violation.message }}</li>
                        </div>
                    </div>
                    <AppCol class="btnright">
                        <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutComponentStock">
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
