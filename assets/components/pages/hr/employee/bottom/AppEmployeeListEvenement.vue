<script setup>
    import {computed, ref} from 'vue'
    import api from '../../../../../api'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import {useEmployeeListEvenementStore} from '../../../../../stores/hr/employee/employeeListEvenement'
    import {useRoute} from 'vue-router'
    import {getOptions} from '../../../../../utils'
    // import useField from '../../../stores/field/field'

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

    const eventTypes = (await api('/api/event-types', 'GET'))['hydra:member']
    const eventTypesOptions = getOptions(eventTypes, 'name')
    console.log(eventTypes)
    const storeEmployeeListEvenement = useEmployeeListEvenementStore()
    const employeeEventFetchCriteria = useFetchCriteria('employeeEvents')
    function resetFilters() {
        employeeEventFetchCriteria.resetAllFilter()
        employeeEventFetchCriteria.addFilter('employee', `/api/employees/${employeeId}`)
    }
    resetFilters()
    //storeEmployeeListEvenement.setIdEmployee(employeeId)
    await storeEmployeeListEvenement.fetch(employeeEventFetchCriteria.getFetchCriteria)
    const itemsTable = ref([])
    itemsTable.value = storeEmployeeListEvenement.itemsEmployeeEvenement

    const formData = ref({
        date: null, motif: null, description: null
    })

    // const fieldsForm = [
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Date',
    //         name: 'date',
    //         sort: false,
    //         type: 'date',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Motif',
    //         name: 'Motif',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Description',
    //         name: 'description',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     }
    // ]

    const tabFields = [
        {
            filter: true,
            label: 'Date',
            name: 'date',
            sort: false,
            type: 'date',
            min: true
        },
        {
            filter: true,
            label: 'Type Evènement',
            name: 'type',
            sort: false,
            type: 'select',
            options: eventTypesOptions,
            min: true
        },
        {
            filter: true,
            label: 'Description',
            name: 'name',
            sort: false,
            type: 'text',
            min: true
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

    // async function ajoutEmployeeEvenement(){
    //     // const form = document.getElementById('addEmployeeEvenement')
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
    //     violations = await storeEmployeeListEvenement.addEmployeeEvenement(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeEmployeeListEvenement.itemsEmployeeEvenement]
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
            date: item.date,
            motif: item.motif,
            description: item.description
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeEmployeeListEvenement.deleted(id)
        itemsTable.value = [...storeEmployeeListEvenement.itemsEmployeeEvenement]
    }
    async function getPage(nPage){
        await storeEmployeeListEvenement.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeEmployeeListEvenement.itemsEmployeeEvenement]
    }
    async function trierAlphabet(payload) {
        await storeEmployeeListEvenement.sortableItems(payload, filterBy, filter)
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
            date: inputValues.date ?? '',
            motif: inputValues.motif ?? '',
            description: inputValues.description ?? ''
        }

        await storeEmployeeListEvenement.filterBy(payload)
        itemsTable.value = [...storeEmployeeListEvenement.itemsEmployeeEvenement]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeEmployeeListEvenement.fetch()
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
                    :current-page="storeEmployeeListEvenement.currentPage"
                    :fields="tabFields"
                    :first-page="storeEmployeeListEvenement.firstPage"
                    :items="itemsTable"
                    :last-page="storeEmployeeListEvenement.lastPage"
                    :min="AddForm"
                    :next-page="storeEmployeeListEvenement.nextPage"
                    :pag="storeEmployeeListEvenement.pagination"
                    :previous-page="storeEmployeeListEvenement.previousPage"
                    :user="roleuser"
                    form="formEmployeeEvenementCardableTable"
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
                    <AppFormCardable id="addEmployeeEvenement" :fields="fieldsForm" :model-value="formData" label-cols/>
                    <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                        <div v-for="violation in violations" :key="violation">
                            <li>{{ violation.message }}</li>
                        </div>
                    </div>
                    <AppCol class="btnright">
                        <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutEmployeeEvenement">
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
