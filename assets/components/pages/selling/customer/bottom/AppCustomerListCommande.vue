<script setup>
    import {computed, ref} from 'vue'
    import {useCustomerListCommandeStore} from '../../../../../stores/customers/customerListCommande'
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
    const customerId = maRoute.params.id_customer

    const storeCustomerListCommande = useCustomerListCommandeStore()
    storeCustomerListCommande.setIdCustomer(customerId)
    await storeCustomerListCommande.fetch()
    const itemsTable = ref(storeCustomerListCommande.itemsCustomerCommande)
    const formData = ref({
        company: null, etat: null, ref: null, DateLivraisonConfirmee: null, typeCommande: null,
        dateValidation: null
    })

    // const fieldsForm = [
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Compagnie',
    //         name: 'company',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Etat',
    //         name: 'etat',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Réf',
    //         name: 'ref',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Date de livraison confirmée',
    //         name: 'DateLivraisonConfirmee',
    //         sort: true,
    //         type: 'date',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'type de commande',
    //         name: 'typeCommande',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Date de validation',
    //         name: 'dateValidation',
    //         sort: true,
    //         type: 'date',
    //         update: true
    //     }
    // ]

    // const parentQuantityCommande = {
    //     //$id: `${warehouseId}Stock`
    //     $id: 'customerCommande'
    // }
    // const storeUnitQtyCommande = useField(fieldsForm[8], parentQuantityCommande)
    // storeUnitQtyCommande.fetch()

    // fieldsForm[8].measure.code = storeUnitQtyCommande.measure.code
    // fieldsForm[8].measure.value = storeUnitQtyCommande.measure.value

    // const parentPrice = {
    //     $id: 'customerCommandePrice'
    // }
    // const storeUnitPrice = useField(fieldsForm[7], parentPrice)
    // storeUnitPrice.fetch()

    // fieldsForm[7].measure.code = storeUnitPrice.measure.code
    // fieldsForm[7].measure.value = storeUnitPrice.measure.value

    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Compagnie',
            name: 'company',
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
            label: 'Réf',
            name: 'ref',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de livraison confirmée',
            name: 'DateLivraisonConfirmee',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'type de commande',
            name: 'typeCommande',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de validation',
            name: 'dateValidation',
            sort: true,
            type: 'date',
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

    // async function ajoutCustomerCommande(){
    //     // const form = document.getElementById('addCustomerCommande')
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
    //     violations = await storeCustomerListCommande.addCustomerCommande(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeCustomerListCommande.itemsCustomerCommande]
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
            company: item.company,
            etat: item.etat,
            ref: item.ref,
            DateLivraisonConfirmee: item.DateLivraisonConfirmee,
            typeCommande: item.typeCommande,
            dateValidation: item.dateValidation
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeCustomerListCommande.deleted(id)
        itemsTable.value = [...storeCustomerListCommande.itemsCustomerCommande]
    }
    async function getPage(nPage){
        await storeCustomerListCommande.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeCustomerListCommande.itemsCustomerCommande]
    }
    async function trierAlphabet(payload) {
        await storeCustomerListCommande.sortableItems(payload, filterBy, filter)
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
            company: inputValues.company ?? '',
            etat: inputValues.etat ?? '',
            ref: inputValues.ref ?? '',
            DateLivraisonConfirmee: inputValues.DateLivraisonConfirmee ?? '',
            typeCommande: inputValues.typeCommande ?? '',
            dateValidation: inputValues.dateValidation ?? ''
        }

        // if (typeof payload.quantite.value === 'undefined' && payload.quantite !== '') {
        //     payload.quantite.value = ''
        // }
        // if (typeof payload.quantite.code === 'undefined' && payload.quantite !== '') {
        //     payload.quantite.code = ''
        // }

        // if (typeof payload.prix.value === 'undefined' && payload.prix !== '') {
        //     payload.prix.value = ''
        // }
        // if (typeof payload.prix.code === 'undefined' && payload.prix !== '') {
        //     payload.prix.code = ''
        // }

        await storeCustomerListCommande.filterBy(payload)
        itemsTable.value = [...storeCustomerListCommande.itemsCustomerCommande]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeCustomerListCommande.fetch()
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
                    :current-page="storeCustomerListCommande.currentPage"
                    :fields="tabFields"
                    :first-page="storeCustomerListCommande.firstPage"
                    :items="itemsTable"
                    :last-page="storeCustomerListCommande.lastPage"
                    :min="AddForm"
                    :next-page="storeCustomerListCommande.nextPage"
                    :pag="storeCustomerListCommande.pagination"
                    :previous-page="storeCustomerListCommande.previousPage"
                    :user="roleuser"
                    form="formCustomerCommandeCardableTable"
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
                    <AppFormCardable id="addCustomerCommande" :fields="fieldsForm" :model-value="formData" label-cols/>
                    <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                        <div v-for="violation in violations" :key="violation">
                            <li>{{ violation.message }}</li>
                        </div>
                    </div>
                    <AppCol class="btnright">
                        <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutCustomerCommande">
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
