<script setup>
    import {computed, ref} from 'vue'
    import {useProductListCommandeStore} from './storeProvisoir/productListCommande'
    // import {useRoute} from 'vue-router'
    import useField from '../../../stores/field/field'

    const roleuser = ref('reader')
    let violations = []
    const updated = ref(false)
    const AddForm = ref(false)
    const isPopupVisible = ref(false)
    const sortable = ref(false)
    const filter = ref(false)
    let trierAlpha = {}
    let filterBy = {}

    // const maRoute = useRoute()
    // const Id = maRoute.params.id_warehouse

    const storeProductListCommande = useProductListCommandeStore()
    // storeProductListCommande.setIdWarehouse(warehouseId)
    await storeProductListCommande.fetch()
    const itemsTable = ref(storeProductListCommande.itemsProductCommande)
    const formData = ref({
        client: null, reference: null, quantiteConfirmee: null, quantiteSouhaitee: null, quantiteEnvoyee: null, dateLivraison: null, dateLivraisonSouhaitee: null
    })

    const fieldsForm = [
        {
            create: false,
            filter: true,
            label: 'Client',
            name: 'client',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'reference',
            name: 'reference',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité confirmée',
            name: 'quantiteConfirmee',
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
            label: 'Quantité souhaitée',
            name: 'quantiteSouhaitee',
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
            label: 'Quantité envoyée',
            name: 'quantiteEnvoyee',
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
            label: 'date de livraison',
            name: 'dateLivraison',
            sort: false,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'date de livraison souhaitée',
            name: 'dateLivraisonSouhaitee',
            sort: false,
            type: 'date',
            update: true
        }
    ]
    const parentQtConfirmee = {
        //$id: `${warehouseId}Stock`
        $id: 'productCommandeQtConfirmee'
    }
    const storeUnitQtConfirmee = useField(fieldsForm[2], parentQtConfirmee)
    // storeUnitQtConfirmee.fetch()

    fieldsForm[2].measure.code = storeUnitQtConfirmee.measure.code
    fieldsForm[2].measure.value = storeUnitQtConfirmee.measure.value

    const parentQtSouhaitee = {
        //$id: `${warehouseId}Stock`
        $id: 'productCommandeQtSouhaitee'
    }
    const storeUnitQtSouhaitee = useField(fieldsForm[3], parentQtSouhaitee)
    // storeUnitQtSouhaitee.fetch()

    fieldsForm[3].measure.code = storeUnitQtSouhaitee.measure.code
    fieldsForm[3].measure.value = storeUnitQtSouhaitee.measure.value

    const parentQtEnvoyee = {
        //$id: `${warehouseId}Stock`
        $id: 'productCommandeQtEnvoyee'
    }
    const storeUnitQtEnvoyee = useField(fieldsForm[4], parentQtEnvoyee)
    // storeUnitQtEnvoyee.fetch()

    fieldsForm[4].measure.code = storeUnitQtEnvoyee.measure.code
    fieldsForm[4].measure.value = storeUnitQtEnvoyee.measure.value

    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Client',
            name: 'client',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'reference',
            name: 'reference',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité confirmée',
            name: 'quantiteConfirmee',
            measure: {
                code: storeUnitQtConfirmee.measure.code,
                value: storeUnitQtConfirmee.measure.value
            },
            sort: false,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité souhaitée',
            name: 'quantiteSouhaitee',
            measure: {
                code: storeUnitQtSouhaitee.measure.code,
                value: storeUnitQtSouhaitee.measure.value
            },
            sort: false,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité envoyée',
            name: 'quantiteEnvoyee',
            measure: {
                code: storeUnitQtEnvoyee.measure.code,
                value: storeUnitQtEnvoyee.measure.value
            },
            sort: false,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'date de livraison',
            name: 'dateLivraison',
            sort: false,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'date de livraison souhaitée',
            name: 'dateLivraisonSouhaitee',
            sort: false,
            type: 'date',
            update: true
        }
    ]

    function ajoute(){
        AddForm.value = true
        updated.value = false
        const itemsNull = {
            client: null,
            reference: null,
            quantiteConfirmee: null,
            quantiteSouhaitee: null,
            quantiteEnvoyee: null,
            dateLivraison: null,
            dateLivraisonSouhaitee: null
        }
        formData.value = itemsNull
    }

    async function ajoutProductCommande(){
        // const form = document.getElementById('addProductCommande')
        // const formData1 = new FormData(form)

        // if (typeof formData.value.families !== 'undefined') {
        //     formData.value.famille = JSON.parse(JSON.stringify(formData.value.famille))
        // }

        const itemsAddData = {
            client: formData.value.client,
            reference: formData.value.reference,
            quantiteConfirmee: formData.value.quantiteConfirmee,
            //quantite: {code: formData1.get('quantite[code]'), value: formData1.get('quantite[value]')},
            quantiteSouhaitee: formData.value.quantiteSouhaitee,
            quantiteEnvoyee: formData.value.quantiteEnvoyee,
            dateLivraison: formData.value.dateLivraison,
            dateLivraisonSouhaitee: formData.value.dateLivraisonSouhaitee
        }
        violations = await storeProductListCommande.addProductCommande(itemsAddData)

        if (violations.length > 0){
            isPopupVisible.value = true
        } else {
            AddForm.value = false
            updated.value = false
            isPopupVisible.value = false
            itemsTable.value = [...storeProductListCommande.itemsProductCommande]
        }
    }
    function annule(){
        AddForm.value = false
        updated.value = false
        const itemsNull = {
            client: null,
            reference: null,
            quantiteConfirmee: null,
            quantiteSouhaitee: null,
            quantiteEnvoyee: null,
            dateLivraison: null,
            dateLivraisonSouhaitee: null
        }
        formData.value = itemsNull
        isPopupVisible.value = false
    }

    function update(item) {
        updated.value = true
        AddForm.value = true
        const itemsData = {
            client: item.client,
            reference: item.reference,
            quantiteConfirmee: item.quantiteConfirmee,
            quantiteSouhaitee: item.quantiteSouhaitee,
            quantiteEnvoyee: item.quantiteEnvoyee,
            dateLivraison: item.dateLivraison,
            dateLivraisonSouhaitee: item.dateLivraisonSouhaitee
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeProductListCommande.deleted(id)
        itemsTable.value = [...storeProductListCommande.itemsProductCommande]
    }
    async function getPage(nPage){
        await storeProductListCommande.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeProductListCommande.itemsProductCommande]
    }
    async function trierAlphabet(payload) {
        await storeProductListCommande.sortableItems(payload, filterBy, filter)
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
            client: inputValues.client ?? '',
            reference: inputValues.reference ?? '',
            quantiteConfirmee: inputValues.quantiteConfirmee ?? '',
            quantiteSouhaitee: inputValues.quantiteSouhaitee ?? '',
            quantiteEnvoyee: inputValues.quantiteEnvoyee ?? '',
            dateLivraison: inputValues.dateLivraison ?? '',
            dateLivraisonSouhaitee: inputValues.dateLivraisonSouhaitee ?? ''
        }

        // if (typeof payload.quantite.value === 'undefined' && payload.quantite !== '') {
        //     payload.quantite.value = ''
        // }
        // if (typeof payload.quantite.code === 'undefined' && payload.quantite !== '') {
        //     payload.quantite.code = ''
        // }
        await storeProductListCommande.filterBy(payload)
        itemsTable.value = [...storeProductListCommande.itemsProductCommande]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeProductListCommande.fetch()
    }
</script>

<template>
    <AppCol class="d-flex justify-content-between mb-2">
        <AppBtn variant="success" label="Ajout" @click="ajoute">
            <Fa icon="plus"/>
            Ajouter
        </AppBtn>
    </AppCol>
    <AppRow>
        <AppCol>
            <AppCardableTable
                :current-page="storeProductListCommande.currentPage"
                :fields="tabFields"
                :first-page="storeProductListCommande.firstPage"
                :items="itemsTable"
                :last-page="storeProductListCommande.lastPage"
                :min="AddForm"
                :next-page="storeProductListCommande.nextPage"
                :pag="storeProductListCommande.pagination"
                :previous-page="storeProductListCommande.previousPage"
                :user="roleuser"
                form="formProductCommandeCardableTable"
                @update="update"
                @deleted="deleted"
                @get-page="getPage"
                @trier-alphabet="trierAlphabet"
                @search="search"
                @cancel-search="cancelSearch"/>
        </AppCol>
        <AppCol v-if="AddForm && !updated" class="col-7">
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
                <AppFormCardable id="addProductCommande" :fields="fieldsForm" :model-value="formData" label-cols/>
                <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                    <div v-for="violation in violations" :key="violation">
                        <li>{{ violation.message }}</li>
                    </div>
                </div>
                <AppCol class="btnright">
                    <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutProductCommande">
                        <Fa icon="plus"/> Ajouter
                    </AppBtn>
                </AppCol>
            </AppCard>
        </AppCol>
    </AppRow>
</template>

<style scoped>
    .btn-float-right{
        float: right;
    }
</style>
