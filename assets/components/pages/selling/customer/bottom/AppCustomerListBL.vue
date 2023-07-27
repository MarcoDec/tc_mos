<script setup>
    import {computed, ref} from 'vue'
    import {useCustomerListBLStore} from '../../../../../stores/customers/customerListBL'
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
    const customerId = maRoute.params.id_customer

    const storeCustomerListBL = useCustomerListBLStore()
    storeCustomerListBL.setIdCustomer(customerId)
    await storeCustomerListBL.fetch()
    const itemsTable = ref(storeCustomerListBL.itemsCustomerBL)
    const formData = ref({
        ref: null, etat: null, date: null, supplementFret: null, noBl: null
    })

    const fieldsForm = [
        {
            create: false,
            filter: true,
            label: 'Bordereau ',
            name: 'ref',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Etat',
            name: 'etat',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date Départ ',
            name: 'date',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Supplément fret',
            name: 'supplementFret',
            sort: true,
            measure: {
                value: null,
                code: null
            },
            type: 'price',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Pas facturable',
            name: 'noBl',
            sort: true,
            type: 'boolean',
            update: true
        }
    ]

    const parentSupplementFret = {
        //$id: `${warehouseId}Stock`
        $id: 'customerBLSupplementFret'
    }
    const storeUnitBLSupplementFret = useField(fieldsForm[3], parentSupplementFret)
    storeUnitBLSupplementFret.fetch()

    fieldsForm[3].measure.code = storeUnitBLSupplementFret.measure.code
    fieldsForm[3].measure.value = storeUnitBLSupplementFret.measure.value

    // const parentQuantityBLEffectuee = {
    //     $id: 'customerBLQtyEffectuee'
    // }
    // const storeUnitQtyBLEffectuee = useField(fieldsForm[4], parentQuantityBLEffectuee)
    // // storeUnitQtyBLEffectuee.fetch()

    // fieldsForm[4].measure.code = storeUnitQtyBLEffectuee.measure.code
    // fieldsForm[4].measure.value = storeUnitQtyBLEffectuee.measure.value

    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Bordereau ',
            name: 'ref',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Etat',
            name: 'etat',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date Départ ',
            name: 'date',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Supplément fret',
            name: 'supplementFret',
            sort: true,
            measure: {
                value: storeUnitBLSupplementFret.measure.value,
                code: storeUnitBLSupplementFret.measure.code
            },
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Pas facturable',
            name: 'noBl',
            sort: true,
            type: 'boolean',
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

    // async function ajoutCustomerBL(){
    //     // const form = document.getElementById('addCustomerBL')
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
    //     violations = await storeCustomerListBL.addCustomerBL(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeCustomerListBL.itemsCustomerBL]
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
            bordereau: item.bordereau,
            etat: item.etat,
            ref: item.ref,
            date: item.date,
            supplementFret: item.supplementFret,
            noBl: item.noBl
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeCustomerListBL.deleted(id)
        itemsTable.value = [...storeCustomerListBL.itemsCustomerBL]
    }
    async function getPage(nPage){
        await storeCustomerListBL.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeCustomerListBL.itemsCustomerBL]
    }
    async function trierAlphabet(payload) {
        await storeCustomerListBL.sortableItems(payload, filterBy, filter)
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
            bordereau: inputValues.bordereau ?? '',
            etat: inputValues.etat ?? '',
            ref: inputValues.ref ?? '',
            date: inputValues.date ?? '',
            supplementFret: inputValues.supplementFret ?? '',
            noBl: inputValues.noBl ?? ''
        }

        if (typeof payload.supplementFret.value === 'undefined' && payload.supplementFret !== '') {
            payload.supplementFret.value = ''
        }
        if (typeof payload.supplementFret.code === 'undefined' && payload.supplementFret !== '') {
            payload.supplementFret.code = ''
        }

        await storeCustomerListBL.filterBy(payload)
        itemsTable.value = [...storeCustomerListBL.itemsCustomerBL]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeCustomerListBL.fetch()
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
                    :current-page="storeCustomerListBL.currentPage"
                    :fields="tabFields"
                    :first-page="storeCustomerListBL.firstPage"
                    :items="itemsTable"
                    :last-page="storeCustomerListBL.lastPage"
                    :min="AddForm"
                    :next-page="storeCustomerListBL.nextPage"
                    :pag="storeCustomerListBL.pagination"
                    :previous-page="storeCustomerListBL.previousPage"
                    :user="roleuser"
                    form="formCustomerBLCardableTable"
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
                    <AppFormCardable id="addCustomerBL" :fields="fieldsForm" :model-value="formData" label-cols/>
                    <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                        <div v-for="violation in violations" :key="violation">
                            <li>{{ violation.message }}</li>
                        </div>
                    </div>
                    <AppCol class="btnright">
                        <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutCustomerBL">
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
