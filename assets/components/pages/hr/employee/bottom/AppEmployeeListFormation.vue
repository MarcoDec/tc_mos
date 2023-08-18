<script setup>
    import {computed, ref} from 'vue'
    import {useEmployeeListFormationStore} from '../../../../../stores/hr/employee/employeeListFormation'
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

    const storeEmployeeListFormation = useEmployeeListFormationStore()
    storeEmployeeListFormation.setIdEmployee(employeeId)
    await storeEmployeeListFormation.fetch()
    const itemsTable = ref(storeEmployeeListFormation.itemsEmployeeFormation)
    const formData = ref({
        date: null, dateCloture: null, rappel: null, competence: null, groupeMachine: null, machine: null, niveau: null, commentaire: null, formateurInt: null, formateurExt: null
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
    //         label: 'Date clôture',
    //         name: 'dateCloture',
    //         sort: false,
    //         type: 'date',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Date de rappel',
    //         name: 'rappel',
    //         sort: false,
    //         type: 'date',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Compétence',
    //         name: 'competence',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Niveau',
    //         name: 'niveau',
    //         sort: false,
    //         type: 'int',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Groupe de machine',
    //         name: 'groupeMachine',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Machine',
    //         name: 'machine',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Commentaire',
    //         name: 'commentaire',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Formateur Interne',
    //         name: 'formateurInt',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Formateur Externe',
    //         name: 'formateurExt',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     }
    // ]
    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Date',
            name: 'date',
            sort: false,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date clôture',
            name: 'dateCloture',
            sort: false,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de rappel',
            name: 'rappel',
            sort: false,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Compétence',
            name: 'competence',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Niveau',
            name: 'niveau',
            sort: false,
            type: 'int',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Groupe de machine',
            name: 'groupeMachine',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Machine',
            name: 'machine',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Commentaire',
            name: 'commentaire',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Formateur Interne',
            name: 'formateurInt',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Formateur Externe',
            name: 'formateurExt',
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

    // async function ajoutEmployeeFormation(){
    //     // const form = document.getElementById('addEmployeeFormation')
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
    //     violations = await storeEmployeeListFormation.addEmployeeFormation(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeEmployeeListFormation.itemsEmployeeFormation]
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
            dateCloture: item.dateCloture,
            rappel: item.rappel,
            competence: item.competence,
            groupeMachine: item.groupeMachine,
            machine: item.machine,
            niveau: item.niveau,
            commentaire: item.commentaire,
            formateurInt: item.formateurInt,
            formateurExt: item.formateurExt
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeEmployeeListFormation.deleted(id)
        itemsTable.value = [...storeEmployeeListFormation.itemsEmployeeFormation]
    }
    async function getPage(nPage){
        await storeEmployeeListFormation.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeEmployeeListFormation.itemsEmployeeFormation]
    }
    async function trierAlphabet(payload) {
        await storeEmployeeListFormation.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }
    async function search(inputValues) {
        let mac = ''
        if (inputValues.machine) {
            mac = inputValues.machine.trim().split(' ')
            if (mac.length === 2){
                mac = {name: mac[0], surname: mac[1], same: false}
            }
            if (mac.length === 1) {
                mac = {name: mac[0], surname: mac[0], same: true}
            }
        }
        // let gpMachine = inputValues.comptence.trim().split(' ')
        // if (gpMachine.length === 2){
        //     gpMachine = {name: gpMachine[0], surname: gpMachine[1]}
        // }
        // if (gpMachine.length === 1) {
        //     gpMachine = {name: gpMachine[0], surname: gpMachine[0]}
        // }

        let formExt = ''
        if (inputValues.formateurExt){
            formExt = inputValues.formateurExt.trim().split(' ')
            if (formExt.length === 2){
                formExt = {name: formExt[0], surname: formExt[1], same: false}
            }
            if (formExt.length === 1) {
                formExt = {name: formExt[0], surname: formExt[0], same: true}
            }
        }

        let formInt = ''
        if (inputValues.formateurInt){
            formInt = inputValues.formateurInt.trim().split(' ')
            if (formInt.length === 2){
                formInt = {name: formInt[0], surname: formInt[1], same: false}
            }
            if (formInt.length === 1) {
                formInt = {name: formInt[0], surname: formInt[0], same: true}
            }
        }

        const payload = {
            date: inputValues.date ?? '',
            dateCloture: inputValues.dateCloture ?? '',
            rappel: inputValues.rappel ?? '',
            competence: inputValues.competence ?? '',
            groupeMachine: inputValues.groupeMachine ?? '',
            machine: mac,
            niveau: inputValues.niveau ?? '',
            commentaire: inputValues.commentaire ?? '',
            formateurInt: formInt ?? '',
            formateurExt: formExt ?? ''
        }

        await storeEmployeeListFormation.filterBy(payload)
        itemsTable.value = [...storeEmployeeListFormation.itemsEmployeeFormation]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeEmployeeListFormation.fetch()
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
                    :current-page="storeEmployeeListFormation.currentPage"
                    :fields="tabFields"
                    :first-page="storeEmployeeListFormation.firstPage"
                    :items="itemsTable"
                    :last-page="storeEmployeeListFormation.lastPage"
                    :min="AddForm"
                    :next-page="storeEmployeeListFormation.nextPage"
                    :pag="storeEmployeeListFormation.pagination"
                    :previous-page="storeEmployeeListFormation.previousPage"
                    :user="roleuser"
                    form="formEmployeeFormationCardableTable"
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
                    <AppFormCardable id="addEmployeeFormation" :fields="fieldsForm" :model-value="formData" label-cols/>
                    <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                        <div v-for="violation in violations" :key="violation">
                            <li>{{ violation.message }}</li>
                        </div>
                    </div>
                    <AppCol class="btnright">
                        <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutEmployeeFormation">
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
