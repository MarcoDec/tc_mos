<script setup>
    import {computed, ref} from 'vue'
    import {useSocietyListSupplierStore} from './storeProvisoir/societyListSupplier'
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

    const storeSocietyListSupplier = useSocietyListSupplierStore()
    storeSocietyListSupplier.setIdSociety(societyId)
    await storeSocietyListSupplier.fetch()
    const itemsTable = ref(storeSocietyListSupplier.itemsSocietySupplier)
    const formData = ref({
        name: null, city: null, country: null, email: null, phoneNumber: null
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
            label: 'Ville',
            name: 'city',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Pays',
            name: 'country',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Email',
            name: 'email',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Téléphone',
            name: 'phoneNumber',
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

    // async function ajoutSocietySupplier(){
    //     // const form = document.getElementById('addSocietySupplier')
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
    //     violations = await storeSocietyListSupplier.addSocietySupplier(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeSocietyListSupplier.itemsSocietySupplier]
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
            city: item.city,
            country: item.country,
            email: item.email,
            phoneNumber: item.phoneNumber
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeSocietyListSupplier.deleted(id)
        itemsTable.value = [...storeSocietyListSupplier.itemsSocietySupplier]
    }
    async function getPage(nPage){
        await storeSocietyListSupplier.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeSocietyListSupplier.itemsSocietySupplier]
    }
    async function trierAlphabet(payload) {
        await storeSocietyListSupplier.sortableItems(payload, filterBy, filter)
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
            city: inputValues.city ?? '',
            country: inputValues.country ?? '',
            email: inputValues.email ?? '',
            phoneNumber: inputValues.phoneNumber ?? ''
        }
        await storeSocietyListSupplier.filterBy(payload)
        itemsTable.value = [...storeSocietyListSupplier.itemsSocietySupplier]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeSocietyListSupplier.fetch()
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
                :current-page="storeSocietyListSupplier.currentPage"
                :fields="tabFields"
                :first-page="storeSocietyListSupplier.firstPage"
                :items="itemsTable"
                :last-page="storeSocietyListSupplier.lastPage"
                :min="AddForm"
                :next-page="storeSocietyListSupplier.nextPage"
                :pag="storeSocietyListSupplier.pagination"
                :previous-page="storeSocietyListSupplier.previousPage"
                :user="roleuser"
                form="formSocietySupplierCardableTable"
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
                <AppFormCardable id="addSocietySupplier" :fields="fieldsForm" :model-value="formData" label-cols/>
                <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                    <div v-for="violation in violations" :key="violation">
                        <li>{{ violation.message }}</li>
                    </div>
                </div>
                <AppCol class="btnright">
                    <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutSocietySupplier">
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
