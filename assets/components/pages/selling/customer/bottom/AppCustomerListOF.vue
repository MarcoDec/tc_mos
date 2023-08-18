<script setup>
    import {computed, ref} from 'vue'
    import {useCustomerListOFStore} from '../../../../../stores/selling/customers/customerListOF'
    import {useRoute} from 'vue-router'
    import useField from '../../../../../stores/field/field'

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

    const storeCustomerListOF = useCustomerListOFStore()
    storeCustomerListOF.setIdCustomer(customerId)
    await storeCustomerListOF.fetch()
    const itemsTable = ref(storeCustomerListOF.itemsCustomerOF)
    const formData = ref({
        produit: null, ref: null, designation: null, indiceClient: null, derniereCommande: null,
        dernierOf: null, dateLivraison: null, prix: null, quantite: null
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
        // {
        //     create: false,
        //     filter: true,
        //     label: 'Désignation ',
        //     name: 'designation',
        //     sort: false,
        //     type: 'text',
        //     update: true
        // },
        {
            create: false,
            filter: true,
            label: 'indice client ',
            name: 'indiceClient',
            sort: false,
            type: 'text',
            update: true
        },
        // {
        //     create: false,
        //     filter: true,
        //     label: 'Dernière commande',
        //     name: 'derniereCommande',
        //     sort: false,
        //     type: 'text',
        //     update: true
        // },
        // {
        //     create: false,
        //     filter: true,
        //     label: 'Dernier OF',
        //     name: 'dernierOf',
        //     sort: false,
        //     type: 'text',
        //     update: true
        // },
        {
            create: false,
            filter: true,
            label: 'Date de livraison',
            name: 'dateLivraison',
            sort: false,
            type: 'date',
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
            label: 'Quantité Demandée',
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

    const parentQuantityOF = {
        //$id: `${warehouseId}Stock`
        $id: 'customerOF'
    }
    const storeUnitQtyOF = useField(fieldsForm[5], parentQuantityOF)
    storeUnitQtyOF.fetch()

    fieldsForm[5].measure.code = storeUnitQtyOF.measure.code
    fieldsForm[5].measure.value = storeUnitQtyOF.measure.value

    const parentPrice = {
        $id: 'customerOFPrice'
    }
    const storeUnitPrice = useField(fieldsForm[4], parentPrice)
    storeUnitPrice.fetch()

    fieldsForm[4].measure.code = storeUnitPrice.measure.code
    fieldsForm[4].measure.value = storeUnitPrice.measure.value

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
        // {
        //     create: false,
        //     filter: true,
        //     label: 'Désignation ',
        //     name: 'designation',
        //     sort: false,
        //     type: 'text',
        //     update: true
        // },
        {
            create: false,
            filter: true,
            label: 'indice client ',
            name: 'indiceClient',
            sort: false,
            type: 'text',
            update: true
        },
        // {
        //     create: false,
        //     filter: true,
        //     label: 'Dernière commande',
        //     name: 'derniereCommande',
        //     sort: false,
        //     type: 'text',
        //     update: true
        // },
        // {
        //     create: false,
        //     filter: true,
        //     label: 'Dernier OF',
        //     name: 'dernierOf',
        //     sort: false,
        //     type: 'text',
        //     update: true
        // },
        {
            create: false,
            filter: true,
            label: 'Date de livraison',
            name: 'dateLivraison',
            sort: false,
            type: 'date',
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
            label: 'Quantité Demandée',
            name: 'quantite',
            measure: {
                code: storeUnitQtyOF.measure.code,
                value: storeUnitQtyOF.measure.value
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

    // async function ajoutCustomerOF(){
    //     // const form = document.getElementById('addCustomerOF')
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
    //     violations = await storeCustomerListOF.addCustomerOF(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeCustomerListOF.itemsCustomerOF]
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
            designation: item.designation,
            indiceClient: item.indiceClient,
            derniereCommande: item.derniereCommande,
            dernierOf: item.dernierOf,
            dateLivraison: item.dateLivraison,
            prix: item.prix,
            quantite: item.quantite
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeCustomerListOF.deleted(id)
        itemsTable.value = [...storeCustomerListOF.itemsCustomerOF]
    }
    async function getPage(nPage){
        await storeCustomerListOF.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeCustomerListOF.itemsCustomerOF]
    }
    async function trierAlphabet(payload) {
        await storeCustomerListOF.sortableItems(payload, filterBy, filter)
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
            designation: inputValues.designation ?? '',
            indiceClient: inputValues.indiceClient ?? '',
            derniereCommande: inputValues.derniereCommande ?? '',
            dernierOf: inputValues.dernierOf ?? '',
            dateLivraison: inputValues.dateLivraison ?? '',
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

        await storeCustomerListOF.filterBy(payload)
        itemsTable.value = [...storeCustomerListOF.itemsCustomerOF]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeCustomerListOF.fetch()
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
                    :current-page="storeCustomerListOF.currentPage"
                    :fields="tabFields"
                    :first-page="storeCustomerListOF.firstPage"
                    :items="itemsTable"
                    :last-page="storeCustomerListOF.lastPage"
                    :min="AddForm"
                    :next-page="storeCustomerListOF.nextPage"
                    :pag="storeCustomerListOF.pagination"
                    :previous-page="storeCustomerListOF.previousPage"
                    :user="roleuser"
                    form="formCustomerOFCardableTable"
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
                    <AppFormCardable id="addCustomerOF" :fields="fieldsForm" :model-value="formData" label-cols/>
                    <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                        <div v-for="violation in violations" :key="violation">
                            <li>{{ violation.message }}</li>
                        </div>
                    </div>
                    <AppCol class="btnright">
                        <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutCustomerOF">
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
