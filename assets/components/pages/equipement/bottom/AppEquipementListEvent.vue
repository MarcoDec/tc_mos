<script setup>
    import {computed, ref} from 'vue'
    import {useCompanyListEventStore} from '../../../../stores/equipement/equipementListEvent'
    import {useRoute} from 'vue-router'
    // import useField from '../../../../stores/field/field'

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
    const equipementId = maRoute.params.id_company

    const storeEquipementListEvent = useCompanyListEventStore()
    storeEquipementListEvent.setIdCompany(equipementId)
    await storeEquipementListEvent.fetch()
    const itemsTable = ref(storeEquipementListEvent.itemsEquipementEvent)
    const formData = ref({
        date: null, done: null, emergency: null, etat: null, interventionNotes: null, type: null
    })

    // const fieldsForm = [
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Nom',
    //         name: 'name',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Date',
    //         name: 'date',
    //         sort: true,
    //         type: 'date',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Type',
    //         name: 'type',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Fini',
    //         name: 'fini',
    //         sort: true,
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
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Fait',
            name: 'done',
            sort: true,
            type: 'boolean',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Urgence',
            name: 'emergency',
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
            label: 'Notes d\'intervention',
            name: 'interventionNotes',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Type',
            name: 'type',
            sort: true,
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
    //         quantiteEffectuee: null,
    //         dateLivraison: null,
    //         dateLivraisonSouhaitee: null
    //     }
    //     formData.value = itemsNull
    // }

    // async function ajoutCompanyEvenementQualite(){
    //     // const form = document.getElementById('addCompanyEvenementQualite')
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
    //     violations = await storeEquipementListEvent.addCompanyEvenementQualite(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeEquipementListEvent.itemsEquipementEvent]
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
            date: item.date,
            done: item.done,
            emergency: item.emergency,
            etat: item.etat,
            interventionNotes: item.interventionNotes,
            type: item.type
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeEquipementListEvent.deleted(id)
        itemsTable.value = [...storeEquipementListEvent.itemsEquipementEvent]
    }
    async function getPage(nPage){
        await storeEquipementListEvent.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeEquipementListEvent.itemsEquipementEvent]
    }
    async function trierAlphabet(payload) {
        await storeEquipementListEvent.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }

    async function search(inputValues) {
        const payload = {
            date: inputValues.date ?? '',
            done: inputValues.done ?? '',
            emergency: inputValues.emergency ?? '',
            etat: inputValues.etat ?? '',
            interventionNotes: inputValues.interventionNotes ?? '',
            type: inputValues.type ?? ''
        }

        await storeEquipementListEvent.filterBy(payload)
        itemsTable.value = [...storeEquipementListEvent.itemsEquipementEvent]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeEquipementListEvent.fetch()
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
                    :current-page="storeEquipementListEvent.currentPage"
                    :fields="tabFields"
                    :first-page="storeEquipementListEvent.firstPage"
                    :items="itemsTable"
                    :last-page="storeEquipementListEvent.lastPage"
                    :min="AddForm"
                    :next-page="storeEquipementListEvent.nextPage"
                    :pag="storeEquipementListEvent.pagination"
                    :previous-page="storeEquipementListEvent.previousPage"
                    :user="roleuser"
                    form="formCompanyEventCardableTable"
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
                    <AppFormCardable id="addCompanyEvenementQualite" :fields="fieldsForm" :model-value="formData" label-cols/>
                    <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                        <div v-for="violation in violations" :key="violation">
                            <li>{{ violation.message }}</li>
                        </div>
                    </div>
                    <AppCol class="btnright">
                        <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutCompanyEvenementQualite">
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
