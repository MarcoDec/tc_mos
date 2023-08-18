<script setup>
    import {computed, ref} from 'vue'
    import {useEmployeeListOFStore} from '../../../../../stores/hr/employee/employeeListOF'
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
    const employeeId = maRoute.params.id_employee

    const storeEmployeeListOF = useEmployeeListOFStore()
    storeEmployeeListOF.setIdEmployee(employeeId)
    await storeEmployeeListOF.fetch()
    const itemsTable = ref(storeEmployeeListOF.itemsEmployeeOF)
    const formData = ref({
        of: null, poste: null, startDate: null, endDate: null, actualQuantity: null, quantityProduced: null, cadence: null, statut: null, cloture: null
    })

    const fieldsForm = [
        {
            create: false,
            filter: true,
            label: 'Numéro OF',
            name: 'of',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Poste',
            name: 'poste',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de début',
            name: 'startDate',
            sort: false,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de fin',
            name: 'endDate',
            sort: false,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité actuelle',
            name: 'actualQuantity',
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
            label: 'Quantité produit',
            name: 'quantityProduced',
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
            label: 'Cadence',
            name: 'cadence',
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
            label: 'Statut',
            name: 'statut',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Clôture',
            name: 'cloture',
            sort: false,
            type: 'text',
            update: true
        }
    ]
    const parentQtActuelle = {
        //$id: `${warehouseId}Stock`
        $id: 'employeeOFQtActuelle'
    }
    const storeUnitQtActuelle = useField(fieldsForm[4], parentQtActuelle)
    storeUnitQtActuelle.fetch()

    fieldsForm[4].measure.code = storeUnitQtActuelle.measure.code
    fieldsForm[4].measure.value = storeUnitQtActuelle.measure.value

    const parentQtProduit = {
        //$id: `${warehouseId}Stock`
        $id: 'employeeOFQtProduit'
    }
    const storeUnitQtProduit = useField(fieldsForm[5], parentQtProduit)
    // storeUnitQtSouhaitee.fetch()

    fieldsForm[5].measure.code = storeUnitQtProduit.measure.code
    fieldsForm[5].measure.value = storeUnitQtProduit.measure.value

    const parentQtCadence = {
        //$id: `${warehouseId}Stock`
        $id: 'employeeOFQtCandence'
    }
    const storeUnitQtCadence = useField(fieldsForm[6], parentQtCadence)
    // storeUnitQtSouhaitee.fetch()

    fieldsForm[6].measure.code = storeUnitQtCadence.measure.code
    fieldsForm[6].measure.value = storeUnitQtCadence.measure.value

    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Numéro OF',
            name: 'of',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Poste',
            name: 'poste',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de début',
            name: 'startDate',
            sort: false,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de fin',
            name: 'endDate',
            sort: false,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité actuelle',
            name: 'actualQuantity',
            measure: {
                code: storeUnitQtActuelle.measure.code,
                value: storeUnitQtActuelle.measure.value
            },
            sort: false,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité produit',
            name: 'quantityProduced',
            measure: {
                code: storeUnitQtProduit.measure.code,
                value: storeUnitQtProduit.measure.value
            },
            sort: false,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Cadence',
            name: 'cadence',
            measure: {
                code: storeUnitQtCadence.measure.code,
                value: storeUnitQtCadence.measure.value
            },
            sort: false,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Statut',
            name: 'statut',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Clôture',
            name: 'cloture',
            sort: false,
            type: 'text',
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
    //         quantiteEffetctuee: null,
    //         dateLivraison: null,
    //         dateLivraisonSouhaitee: null
    //     }
    //     formData.value = itemsNull
    // }

    // async function ajoutEmployeeOF(){
    //     // const form = document.getElementById('addEmployeeOF')
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
    //         quantiteEffetctuee: formData.value.quantiteEffetctuee,
    //         dateLivraison: formData.value.dateLivraison,
    //         dateLivraisonSouhaitee: formData.value.dateLivraisonSouhaitee
    //     }
    //     violations = await storeEmployeeListOF.addEmployeeOF(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeEmployeeListOF.itemsEmployeeOF]
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
    //         quantiteEffetctuee: null,
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
            of: item.of,
            poste: item.poste,
            startDate: item.startDate,
            endDate: item.endDate,
            actualQuantity: item.actualQuantity,
            quantityProduced: item.quantityProduced,
            cadence: item.cadence,
            statut: item.statut,
            cloture: item.cloture
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeEmployeeListOF.deleted(id)
        itemsTable.value = [...storeEmployeeListOF.itemsEmployeeOF]
    }
    async function getPage(nPage){
        await storeEmployeeListOF.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeEmployeeListOF.itemsEmployeeOF]
    }
    async function trierAlphabet(payload) {
        await storeEmployeeListOF.sortableItems(payload, filterBy, filter)
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
            of: inputValues.of ?? '',
            poste: inputValues.poste ?? '',
            startDate: inputValues.startDate ?? '',
            endDate: inputValues.endDate ?? '',
            actualQuantity: inputValues.actualQuantity ?? '',
            quantityProduced: inputValues.quantityProduced ?? '',
            cadence: inputValues.cadence ?? '',
            statut: inputValues.statut ?? '',
            cloture: inputValues.cloture ?? ''
        }

        if (typeof payload.actualQuantity.value === 'undefined' && payload.actualQuantity !== '') {
            payload.actualQuantity.value = ''
        }
        if (typeof payload.actualQuantity.code === 'undefined' && payload.actualQuantity !== '') {
            payload.actualQuantity.code = ''
        }

        if (typeof payload.quantityProduced.value === 'undefined' && payload.quantityProduced !== '') {
            payload.quantityProduced.value = ''
        }
        if (typeof payload.quantityProduced.code === 'undefined' && payload.quantityProduced !== '') {
            payload.quantityProduced.code = ''
        }

        if (typeof payload.cadence.value === 'undefined' && payload.cadence !== '') {
            payload.cadence.value = ''
        }
        if (typeof payload.cadence.code === 'undefined' && payload.cadence !== '') {
            payload.cadence.code = ''
        }
        await storeEmployeeListOF.filterBy(payload)
        itemsTable.value = [...storeEmployeeListOF.itemsEmployeeOF]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeEmployeeListOF.fetch()
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
                    :current-page="storeEmployeeListOF.currentPage"
                    :fields="tabFields"
                    :first-page="storeEmployeeListOF.firstPage"
                    :items="itemsTable"
                    :last-page="storeEmployeeListOF.lastPage"
                    :min="AddForm"
                    :next-page="storeEmployeeListOF.nextPage"
                    :pag="storeEmployeeListOF.pagination"
                    :previous-page="storeEmployeeListOF.previousPage"
                    :user="roleuser"
                    form="formEmployeeOFCardableTable"
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
                    <AppFormCardable id="addEmployeeOF" :fields="fieldsForm" :model-value="formData" label-cols/>
                    <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                        <div v-for="violation in violations" :key="violation">
                            <li>{{ violation.message }}</li>
                        </div>
                    </div>
                    <AppCol class="btnright">
                        <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutEmployeeOF">
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
