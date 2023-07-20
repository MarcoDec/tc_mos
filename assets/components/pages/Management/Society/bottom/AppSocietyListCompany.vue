<script setup>
    import {computed, ref} from 'vue'
    import {useSocietyListCompanyStore} from '../../../../../stores/societies/societyListCompany'
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
    const societyId = maRoute.params.id_society

    const storeSocietyListCompany = useSocietyListCompanyStore()
    storeSocietyListCompany.setIdSociety(societyId)
    await storeSocietyListCompany.fetch()
    const itemsTable = ref(storeSocietyListCompany.itemsSocietyCompany)
    const formData = ref({
        name: null, deliveryTime: null, deliveryTimeOpenDays: null, engineHourRate: null, generalMargin: null,
        handlingHourRate: null, managementFees: null, numberOfTeamPerDay: null
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
            label: 'Nom du Client',
            name: 'name',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Temps de livraison',
            name: 'deliveryTime',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Est-ce un temps de livraison en jours ouvrés ?',
            name: 'deliveryTimeOpenDays',
            sort: false,
            type: 'boolean',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Taux horaire machine',
            name: 'engineHourRate',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Marge générale',
            name: 'generalMargin',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Taux horaire manutention',
            name: 'handlingHourRate',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Frais de gestion',
            name: 'managementFees',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Nombre de travailleurs dans l\'équipe par jour',
            name: 'numberOfTeamPerDay',
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

    // async function ajoutSocietyCompany(){
    //     // const form = document.getElementById('addSocietyCompany')
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
    //     violations = await storeSocietyListCompany.addSocietyCompany(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeSocietyListCompany.itemsSocietyCompany]
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
            name: item.name,
            deliveryTime: item.deliveryTime,
            deliveryTimeOpenDays: item.deliveryTimeOpenDays,
            engineHourRate: item.engineHourRate,
            generalMargin: item.generalMargin,
            handlingHourRate: item.handlingHourRate,
            managementFees: item.managementFees,
            numberOfTeamPerDay: item.numberOfTeamPerDay
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeSocietyListCompany.deleted(id)
        itemsTable.value = [...storeSocietyListCompany.itemsSocietyCompany]
    }
    async function getPage(nPage){
        await storeSocietyListCompany.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeSocietyListCompany.itemsSocietyCompany]
    }
    async function trierAlphabet(payload) {
        await storeSocietyListCompany.sortableItems(payload, filterBy, filter)
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
            deliveryTime: inputValues.deliveryTime ?? '',
            deliveryTimeOpenDays: inputValues.deliveryTimeOpenDays ?? '',
            engineHourRate: inputValues.engineHourRate ?? '',
            generalMargin: inputValues.generalMargin ?? '',
            handlingHourRate: inputValues.handlingHourRate ?? '',
            managementFees: inputValues.managementFees ?? '',
            numberOfTeamPerDay: inputValues.numberOfTeamPerDay ?? ''
        }
        await storeSocietyListCompany.filterBy(payload)
        itemsTable.value = [...storeSocietyListCompany.itemsSocietyCompany]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeSocietyListCompany.fetch()
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
                    :current-page="storeSocietyListCompany.currentPage"
                    :fields="tabFields"
                    :first-page="storeSocietyListCompany.firstPage"
                    :items="itemsTable"
                    :last-page="storeSocietyListCompany.lastPage"
                    :min="AddForm"
                    :next-page="storeSocietyListCompany.nextPage"
                    :pag="storeSocietyListCompany.pagination"
                    :previous-page="storeSocietyListCompany.previousPage"
                    :user="roleuser"
                    form="formSocietyCompanyCardableTable"
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
                    <AppFormCardable id="addSocietyCompany" :fields="fieldsForm" :model-value="formData" label-cols/>
                    <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                        <div v-for="violation in violations" :key="violation">
                            <li>{{ violation.message }}</li>
                        </div>
                    </div>
                    <AppCol class="btnright">
                        <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutSocietyCompany">
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
