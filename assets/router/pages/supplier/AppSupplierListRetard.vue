<script setup>
    import {computed, ref} from 'vue'
    import {useSupplierListRetardStore} from './storeProvisoir/supplierListRetard'
    import {useRoute} from 'vue-router'
    import useField from '../../../stores/field/field'

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

    const storeSupplierListRetard = useSupplierListRetardStore()

    storeSupplierListRetard.setIdSupplier(supplierID)
    await storeSupplierListRetard.fetch()
    const itemsTable = ref(storeSupplierListRetard.itemsSupplierRetard)
    const formData = ref({
        creeLe: null, composant: null, retard: null, dateSouhaitee: null, note: null, /*fournisseurFerme: null, composantFournisseur: null,*/ quantiteSouhaitee: null, quantiteEffectuee: null
    })

    const optionComposant = await storeSupplierListRetard.getOptionComposant

    const fieldsForm = [
        {
            create: false,
            filter: true,
            label: 'Crée le ',
            name: 'creeLe ',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Composant',
            name: 'composant',
            options: {label: value => optionComposant.find(option => option.value === value)?.text.code ?? null, options: optionComposant},
            type: 'select',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Retard ',
            name: 'retard',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date souhaitée',
            name: 'dateSouhaitee',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Note',
            name: 'note',
            sort: true,
            type: 'text',
            update: true
        },
        /*
        {
            create: false,
            filter: true,
            label: 'Cmde Fournisseur Ferme',
            name: 'fournisseurFerme',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Cmde de Composant Fournisseur',
            name: 'composantFournisseur',
            sort: true,
            type: 'text',
            update: true
        },
        */
        {
            create: false,
            filter: true,
            label: 'Quantité souhaitée',
            name: 'quantiteSouhaitee',
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
            label: 'Quantité effectuée',
            name: 'quantiteEffectuee',
            measure: {
                code: null,
                value: null
            },
            sort: true,
            type: 'measure',
            update: true
        }
    ]

    const parentQtSouhaitee = {
        //$id: `${supplierID}Stock`
        $id: 'supplierRetardQtSouhaitee'
    }
    const storeUnitQtSouhaitee = useField(fieldsForm[5], parentQtSouhaitee)
    //storeUnit.fetch()
    fieldsForm[5].measure.code = storeUnitQtSouhaitee.measure.code
    fieldsForm[5].measure.value = storeUnitQtSouhaitee.measure.value

    const parentQtEffectuee = {
        //$id: `${supplierID}Stock`
        $id: 'supplierRetardQtEffectuee'
    }
    const storeUnitQtEffectuee = useField(fieldsForm[6], parentQtEffectuee)
    //storeUnit.fetch()
    fieldsForm[6].measure.code = storeUnitQtEffectuee.measure.code
    fieldsForm[6].measure.value = storeUnitQtEffectuee.measure.value

    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Crée le ',
            name: 'creeLe',
            sort: true,
            type: 'date',
            update: true
        },
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
            label: 'Retard ',
            name: 'retard',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date souhaitée',
            name: 'dateSouhaitee',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Note',
            name: 'note',
            sort: true,
            type: 'text',
            update: true
        },
        /*
        {
            create: false,
            filter: true,
            label: 'Cmde Fournisseur Ferme',
            name: 'fournisseurFerme',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Cmde de Composant Fournisseur',
            name: 'composantFournisseur',
            sort: true,
            type: 'text',
            update: true
        },
        */
        {
            create: false,
            filter: true,
            label: 'Quantité souhaitée',
            name: 'quantiteSouhaitee',
            measure: {
                code: storeUnitQtSouhaitee.measure.code,
                value: storeUnitQtSouhaitee.measure.value
            },
            sort: true,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité effectuée',
            name: 'quantiteEffectuee',
            measure: {
                code: storeUnitQtEffectuee.measure.code,
                value: storeUnitQtEffectuee.measure.value
            },
            sort: true,
            type: 'measure',
            update: true
        }
    ]

    // function ajoute(){
    //     AddForm.value = true
    //     updated.value = false
    //     const itemsNull = {
    //         creeLe: null,
    //         composant: null,
    //         retard: null,
    //         dateSouhaitee: null,
    //         note: null,
    //         // fournisseurFerme: null,
    //         // composantFournisseur: null,
    //         quantiteSouhaitee: null,
    //         quantiteEffectuee: null
    //     }
    //     formData.value = itemsNull
    // }

    // async function ajoutSupplierRetard(){
    //     const form = document.getElementById('addSupplierRetard')
    //     const formData1 = new FormData(form)

    //     const itemsAddData = {
    //         creeLe: formData.value.creeLe,
    //         composant: formData.value.composant,
    //         retard: formData.value.retard,
    //         dateSouhaitee: formData.value.dateSouhaitee,
    //         // fournisseurFerme: formData.value.fournisseurFerme,
    //         // composantFournisseur: formData.value.composantFournisseur,
    //         quantiteSouhaitee: {code: formData1.get('quantiteSouhaitee[code]'), value: formData1.get('quantiteSouhaitee[value]')},
    //         quantiteEffectuee: {code: formData1.get('quantiteEffectuee[code]'), value: formData1.get('quantiteEffectuee[value]')}
    //     }
    //     violations = await storeSupplierListRetard.addSupplierRetard(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeSupplierListRetard.itemsSupplierRetard]
    //     }
    // }
    // function annule(){
    //     AddForm.value = false
    //     updated.value = false
    //     const itemsNull = {
    //         creeLe: null,
    //         composant: null,
    //         retard: null,
    //         dateSouhaitee: null,
    //         note: null,
    //         // fournisseurFerme: null,
    //         // composantFournisseur: null,
    //         quantiteSouhaitee: null,
    //         quantiteEffectuee: null
    //     }
    //     formData.value = itemsNull
    //     isPopupVisible.value = false
    // }

    function update(item) {
        updated.value = true
        AddForm.value = true
        const itemsData = {
            creeLe: item.creeLe,
            composant: item.composant,
            retard: item.retard,
            dateSouhaitee: item.dateSouhaitee,
            note: item.note,
            // fournisseurFerme: item.fournisseurFerme,
            // composantFournisseur: item.composantFournisseur,
            quantiteSouhaitee: item.quantiteSouhaitee,
            quantiteEffectuee: item.quantiteEffectuee
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeSupplierListRetard.deleted(id)
        itemsTable.value = [...storeSupplierListRetard.itemsSupplierRetard]
    }
    async function getPage(nPage){
        await storeSupplierListRetard.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeSupplierListRetard.itemsSupplierRetard]
    }
    async function trierAlphabet(payload) {
        await storeSupplierListRetard.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }
    async function search(inputValues) {
        let comp = ''
        if (typeof inputValues.composant !== 'undefined'){
            comp = inputValues.composant
        }

        const payload = {
            creeLe: inputValues.creeLe ?? '',
            composant: comp,
            retard: inputValues.retard ?? '',
            dateSouhaitee: inputValues.dateSouhaitee ?? '',
            note: inputValues.note ?? '',
            // fournisseurFerme: inputValues.fournisseurFerme ?? '',
            // composantFournisseur: inputValues.composantFournisseur ?? '',
            quantiteSouhaitee: inputValues.quantiteSouhaitee ?? '',
            quantiteEffectuee: inputValues.quantiteEffectuee ?? ''
        }

        if (typeof payload.quantiteSouhaitee.value === 'undefined' && payload.quantiteSouhaitee !== '') {
            payload.quantiteSouhaitee.value = ''
        }
        if (typeof payload.quantiteSouhaitee.code === 'undefined' && payload.quantiteSouhaitee !== '') {
            payload.quantiteSouhaitee.code = ''
        }

        if (typeof payload.quantiteEffectuee.value === 'undefined' && payload.quantiteEffectuee !== '') {
            payload.quantiteEffectuee.value = ''
        }
        if (typeof payload.quantiteEffectuee.code === 'undefined' && payload.quantiteEffectuee !== '') {
            payload.quantiteEffectuee.code = ''
        }
        await storeSupplierListRetard.filterBy(payload)
        itemsTable.value = [...storeSupplierListRetard.itemsSupplierRetard]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeSupplierListRetard.fetch()
    }
</script>

<template>
    <!-- <AppCol class="d-flex justify-content-between mb-2">
        <AppBtn variant="success" label="Ajout" @click="ajoute">
            <Fa icon="plus"/>
            Ajouter
        </AppBtn>
    </AppCol> -->
    <AppRow>
        <AppCol>
            <AppCardableTable
                :current-page="storeSupplierListRetard.currentPage"
                :fields="tabFields"
                :first-page="storeSupplierListRetard.firstPage"
                :items="itemsTable"
                :last-page="storeSupplierListRetard.lastPage"
                :min="AddForm"
                :next-page="storeSupplierListRetard.nextPage"
                :pag="storeSupplierListRetard.pagination"
                :previous-page="storeSupplierListRetard.previousPage"
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
                <AppFormCardable id="addSupplierRetard" :fields="fieldsForm" :model-value="formData" label-cols/>
                <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                    <div v-for="violation in violations" :key="violation">
                        <li>{{ violation.message }}</li>
                    </div>
                </div>
                <AppCol class="btnright">
                    <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutSupplierRetard">
                        <Fa icon="plus"/> Ajouter
                    </AppBtn>
                </AppCol>
            </AppCard>
        </AppCol> -->
    </AppRow>
</template>

<style scoped>
    .btn-float-right{
        float: right;
    }
</style>
