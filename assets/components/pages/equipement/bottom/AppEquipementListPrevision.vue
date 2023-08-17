<script setup>
    import {computed, ref} from 'vue'
    import {useCompanyListPrevisionStore} from '../../../../stores/equipement/equipementListPrevision'
    import {useRoute} from 'vue-router'
    import useField from '../../../../stores/field/field'

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

    const storeEquipementListPrevision = useCompanyListPrevisionStore()
    storeEquipementListPrevision.setIdCompany(equipementId)
    await storeEquipementListPrevision.fetch()
    const itemsTable = ref(storeEquipementListPrevision.itemsEquipementPrevision)
    const formData = ref({
        date: null, type: null, name: null, quantite: null
    })

    const fieldsForm = [
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
            label: 'Type',
            name: 'type',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Note',
            name: 'name',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'quantite',
            name: 'quantite',
            measure: {
                code: null,
                value: null
            },
            sort: true,
            type: 'measure',
            update: true
        }
    ]

    const parentQty = {
        $id: 'equipementPrevisionQty'
    }
    const storeUnitEquipementPrevisionQty = useField(fieldsForm[3], parentQty)
    await storeUnitEquipementPrevisionQty.fetch()

    fieldsForm[3].measure.code = storeUnitEquipementPrevisionQty.measure.code
    fieldsForm[3].measure.value = storeUnitEquipementPrevisionQty.measure.value

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
            label: 'Type',
            name: 'type',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Note',
            name: 'name',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'quantite',
            name: 'quantite',
            measure: {
                code: storeUnitEquipementPrevisionQty.measure.code,
                value: storeUnitEquipementPrevisionQty.measure.value
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
    //     violations = await storeEquipementListPrevision.addCompanyEvenementQualite(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeEquipementListPrevision.itemsEquipementPrevision]
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
            type: item.type,
            name: item.name,
            quantite: item.quantite
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeEquipementListPrevision.deleted(id)
        itemsTable.value = [...storeEquipementListPrevision.itemsEquipementPrevision]
    }
    async function getPage(nPage){
        await storeEquipementListPrevision.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeEquipementListPrevision.itemsEquipementPrevision]
    }
    async function trierAlphabet(payload) {
        await storeEquipementListPrevision.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }

    async function search(inputValues) {
        const payload = {
            date: inputValues.date ?? '',
            type: inputValues.type ?? '',
            name: inputValues.name ?? '',
            quantite: inputValues.quantite ?? ''
        }

        if (typeof payload.quantite.value === 'undefined' && payload.quantite !== '') {
            payload.quantite.value = ''
        }
        if (typeof payload.quantite.code === 'undefined' && payload.quantite !== '') {
            payload.quantite.code = ''
        }

        await storeEquipementListPrevision.filterBy(payload)
        itemsTable.value = [...storeEquipementListPrevision.itemsEquipementPrevision]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeEquipementListPrevision.fetch()
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
                    :current-page="storeEquipementListPrevision.currentPage"
                    :fields="tabFields"
                    :first-page="storeEquipementListPrevision.firstPage"
                    :items="itemsTable"
                    :last-page="storeEquipementListPrevision.lastPage"
                    :min="AddForm"
                    :next-page="storeEquipementListPrevision.nextPage"
                    :pag="storeEquipementListPrevision.pagination"
                    :previous-page="storeEquipementListPrevision.previousPage"
                    :user="roleuser"
                    form="formCompanyPrevisionCardableTable"
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
