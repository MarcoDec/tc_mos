<script setup>
    import {computed, ref} from 'vue'
    import {useCustomerListProductStore} from './storeProvisoir/customerListProduct'
    import {useRoute} from 'vue-router'
    import useField from '../../../stores/field/field'

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

    const storeCustomerListProduct = useCustomerListProductStore()
    storeCustomerListProduct.setIdCustomer(customerId)
    await storeCustomerListProduct.fetch()
    const itemsTable = ref(storeCustomerListProduct.itemsCustomerProduct)
    const formData = ref({
        produit: null, ref: null, indiceClient: null, prix: null, quantite: null
    })

    const fieldsForm = [
        {
            create: false,
            filter: true,
            label: 'Produit',
            name: 'produit',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Réf.',
            name: 'ref',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'indice client ',
            name: 'indiceClient',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Prix ',
            name: 'prix',
            measure: {
                code: null,
                value: null
            },
            sort: true,
            type: 'price',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité Prévisionelle',
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

    const parentQuantityProduct = {
        //$id: `${warehouseId}Stock`
        $id: 'customerProduct'
    }
    const storeUnitQtyProduct = useField(fieldsForm[8], parentQuantityProduct)
    storeUnitQtyProduct.fetch()

    fieldsForm[8].measure.code = storeUnitQtyProduct.measure.code
    fieldsForm[8].measure.value = storeUnitQtyProduct.measure.value

    const parentPrice = {
        $id: 'customerProductPrice'
    }
    const storeUnitPrice = useField(fieldsForm[7], parentPrice)
    storeUnitPrice.fetch()

    fieldsForm[7].measure.code = storeUnitPrice.measure.code
    fieldsForm[7].measure.value = storeUnitPrice.measure.value

    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Produit',
            name: 'produit',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Réf.',
            name: 'ref',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'indice client ',
            name: 'indiceClient',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Prix ',
            name: 'prix',
            measure: {
                code: storeUnitPrice.measure.code,
                value: storeUnitPrice.measure.value
            },
            sort: true,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité Prévisionnelle',
            name: 'quantite',
            measure: {
                code: storeUnitQtyProduct.measure.code,
                value: storeUnitQtyProduct.measure.value
            },
            sort: false,
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
    //         quantiteEffetctuee: null,
    //         dateLivraison: null,
    //         dateLivraisonSouhaitee: null
    //     }
    //     formData.value = itemsNull
    // }

    // async function ajoutCustomerProduct(){
    //     // const form = document.getElementById('addCustomerProduct')
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
    //     violations = await storeCustomerListProduct.addCustomerProduct(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeCustomerListProduct.itemsCustomerProduct]
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
            produit: item.produit,
            ref: item.ref,
            indiceClient: item.indiceClient,
            prix: item.prix,
            quantite: item.quantite
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeCustomerListProduct.deleted(id)
        itemsTable.value = [...storeCustomerListProduct.itemsCustomerProduct]
    }
    async function getPage(nPage){
        await storeCustomerListProduct.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeCustomerListProduct.itemsCustomerProduct]
    }
    async function trierAlphabet(payload) {
        await storeCustomerListProduct.sortableItems(payload, filterBy, filter)
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
            produit: inputValues.produit ?? '',
            ref: inputValues.ref ?? '',
            indiceClient: inputValues.indiceClient ?? '',
            prix: inputValues.prix ?? '',
            quantite: inputValues.quantite ?? ''
        }

        if (typeof payload.quantite.value === 'undefined' && payload.quantite !== '') {
            payload.quantite.value = ''
        }
        if (typeof payload.quantite.code === 'undefined' && payload.quantite !== '') {
            payload.quantite.code = ''
        }

        if (typeof payload.prix.value === 'undefined' && payload.prix !== '') {
            payload.prix.value = ''
        }
        if (typeof payload.prix.code === 'undefined' && payload.prix !== '') {
            payload.prix.code = ''
        }

        await storeCustomerListProduct.filterBy(payload)
        itemsTable.value = [...storeCustomerListProduct.itemsCustomerProduct]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeCustomerListProduct.fetch()
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
                :current-page="storeCustomerListProduct.currentPage"
                :fields="tabFields"
                :first-page="storeCustomerListProduct.firstPage"
                :items="itemsTable"
                :last-page="storeCustomerListProduct.lastPage"
                :min="AddForm"
                :next-page="storeCustomerListProduct.nextPage"
                :pag="storeCustomerListProduct.pagination"
                :previous-page="storeCustomerListProduct.previousPage"
                :user="roleuser"
                form="formCustomerProductCardableTable"
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
                <AppFormCardable id="addCustomerProduct" :fields="fieldsForm" :model-value="formData" label-cols/>
                <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                    <div v-for="violation in violations" :key="violation">
                        <li>{{ violation.message }}</li>
                    </div>
                </div>
                <AppCol class="btnright">
                    <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutCustomerProduct">
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
