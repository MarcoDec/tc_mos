<script setup>
    import {computed, ref} from 'vue'
    import {useEmployeeListClockingStore} from './storeProvisoir/employeeListClocking'
    import {useRoute} from 'vue-router'
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

    const storeEmployeeListClocking = useEmployeeListClockingStore()
    storeEmployeeListClocking.setIdEmployee(employeeId)
    await storeEmployeeListClocking.fetch()
    const itemsTable = ref(storeEmployeeListClocking.itemsEmployeeClocking)
    const formData = ref({
        date: null, creationDate: null, enter: null
    })

    // const fieldsForm = [
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Date de création',
    //         name: 'creationDate',
    //         sort: false,
    //         type: 'date',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Date et heure du pointage',
    //         name: 'reference',
    //         sort: false,
    //         type: 'date',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Etat',
    //         name: 'enter',
    //         sort: false,
    //         type: 'boolean',
    //         update: true
    //     }
    // ]
    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Date de création',
            name: 'creationDate',
            sort: false,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date et heure du pointage',
            name: 'date',
            sort: false,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Entrée',
            name: 'enter',
            sort: false,
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
    //         quantiteEffetctuee: null,
    //         dateLivraison: null,
    //         dateLivraisonSouhaitee: null
    //     }
    //     formData.value = itemsNull
    // }

    // async function ajoutEmployeeClocking(){
    //     // const form = document.getElementById('addEmployeeClocking')
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
    //     violations = await storeEmployeeListClocking.addEmployeeClocking(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeEmployeeListClocking.itemsEmployeeClocking]
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
            creationDate: item.creationDate,
            date: item.date,
            enter: item.enter
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeEmployeeListClocking.deleted(id)
        itemsTable.value = [...storeEmployeeListClocking.itemsEmployeeClocking]
    }
    async function getPage(nPage){
        await storeEmployeeListClocking.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeEmployeeListClocking.itemsEmployeeClocking]
    }
    async function trierAlphabet(payload) {
        await storeEmployeeListClocking.sortableItems(payload, filterBy, filter)
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
            creationDate: inputValues.creationDate ?? '',
            date: inputValues.date ?? '',
            enter: inputValues.enter ?? ''
        }
        await storeEmployeeListClocking.filterBy(payload)
        itemsTable.value = [...storeEmployeeListClocking.itemsEmployeeClocking]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeEmployeeListClocking.fetch()
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
                :current-page="storeEmployeeListClocking.currentPage"
                :fields="tabFields"
                :first-page="storeEmployeeListClocking.firstPage"
                :items="itemsTable"
                :last-page="storeEmployeeListClocking.lastPage"
                :min="AddForm"
                :next-page="storeEmployeeListClocking.nextPage"
                :pag="storeEmployeeListClocking.pagination"
                :previous-page="storeEmployeeListClocking.previousPage"
                :user="roleuser"
                form="formEmployeeClockingCardableTable"
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
                <AppFormCardable id="addEmployeeClocking" :fields="fieldsForm" :model-value="formData" label-cols/>
                <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                    <div v-for="violation in violations" :key="violation">
                        <li>{{ violation.message }}</li>
                    </div>
                </div>
                <AppCol class="btnright">
                    <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutEmployeeClocking">
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
