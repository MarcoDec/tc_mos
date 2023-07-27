<script setup>
    import {computed, ref} from 'vue'
    import {useCustomerListFactureStore} from '../../../../../stores/customers/customerListFacture'
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

    const storeCustomerListFacture = useCustomerListFactureStore()
    storeCustomerListFacture.setIdCustomer(customerId)
    await storeCustomerListFacture.fetch()
    const itemsTable = ref(storeCustomerListFacture.itemsCustomerFacture)

    const formData = ref({
        ref: null, dateFacture: null, dateEcheance: null, prixHT: null, forceTVA: null, prixTTC: null, note: null, tva: null, msgTVA: null
    })

    const fieldsForm = [
        {
            create: false,
            filter: true,
            label: 'Référence',
            name: 'ref',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de Facturation',
            name: 'dateFacture',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date d\'échéance',
            name: 'dateEcheance',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Prix HT',
            name: 'prixHT',
            sort: true,
            measure: {
                value: null,
                code: null
            },
            type: 'price',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Forcer la TVA',
            name: 'forceTVA',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Prix TTC',
            name: 'prixTTC',
            sort: true,
            measure: {
                value: null,
                code: null
            },
            type: 'price',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Note',
            name: 'note',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'TVA',
            name: 'tva',
            sort: true,
            measure: {
                value: null,
                code: null
            },
            type: 'price',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Message TVA',
            name: 'msgTVA',
            sort: true,
            type: 'text',
            update: true
        }
    ]

    const parentPrixHt = {
        $id: 'customerFacturePrixHT'
    }
    const storeUnitFacturePrixHT = useField(fieldsForm[3], parentPrixHt)
    storeUnitFacturePrixHT.fetch()

    fieldsForm[3].measure.code = storeUnitFacturePrixHT.measure.code
    fieldsForm[3].measure.value = storeUnitFacturePrixHT.measure.value

    const parentPrixTtc = {
        $id: 'customerFacturePrixTTC'
    }
    const storeUnitFacturePrixTTC = useField(fieldsForm[5], parentPrixTtc)
    // storeUnitFacturePrixTTC.fetch()

    fieldsForm[5].measure.code = storeUnitFacturePrixTTC.measure.code
    fieldsForm[5].measure.value = storeUnitFacturePrixTTC.measure.value

    const parentPrixTva = {
        $id: 'customerFacturePrixTva'
    }
    const storeUnitFacturePrixTVA = useField(fieldsForm[7], parentPrixTva)
    // storeUnitFacturePrixTVA.fetch()

    fieldsForm[7].measure.code = storeUnitFacturePrixTVA.measure.code
    fieldsForm[7].measure.value = storeUnitFacturePrixTVA.measure.value

    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Référence',
            name: 'ref',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de Facturation',
            name: 'dateFacture',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date d\'échéance',
            name: 'dateEcheance',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Prix HT',
            name: 'prixHT',
            sort: true,
            measure: {
                value: storeUnitFacturePrixHT.measure.value,
                code: storeUnitFacturePrixHT.measure.code
            },
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Forcer la TVA',
            name: 'forceTVA',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Prix TTC',
            name: 'prixTTC',
            sort: true,
            measure: {
                value: storeUnitFacturePrixTTC.measure.value,
                code: storeUnitFacturePrixTTC.measure.code
            },
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Note',
            name: 'note',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'TVA',
            name: 'tva',
            sort: true,
            measure: {
                value: storeUnitFacturePrixTVA.measure.value,
                code: storeUnitFacturePrixTVA.measure.code
            },
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Message TVA',
            name: 'msgTVA',
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

    // async function ajoutCustomerFacture(){
    //     // const form = document.getElementById('addCustomerFacture')
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
    //     violations = await storeCustomerListFacture.addCustomerFacture(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeCustomerListFacture.itemsCustomerFacture]
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
            ref: item.ref,
            dateFacture: item.dateFacture,
            dateEcheance: item.dateEcheance,
            prixHT: item.prixHT,
            forceTVA: item.forceTVA,
            prixTTC: item.prixTTC,
            note: item.note,
            tva: item.tva,
            msgTVA: item.msgTVA

        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeCustomerListFacture.deleted(id)
        itemsTable.value = [...storeCustomerListFacture.itemsCustomerFacture]
    }
    async function getPage(nPage){
        await storeCustomerListFacture.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeCustomerListFacture.itemsCustomerFacture]
    }
    async function trierAlphabet(payload) {
        await storeCustomerListFacture.sortableItems(payload, filterBy, filter)
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
            ref: inputValues.ref ?? '',
            dateFacture: inputValues.dateFacture ?? '',
            dateEcheance: inputValues.dateEcheance ?? '',
            prixHT: inputValues.prixHT ?? '',
            forceTVA: inputValues.forceTVA ?? '',
            prixTTC: inputValues.prixTTC ?? '',
            note: inputValues.note ?? '',
            tva: inputValues.tva ?? '',
            msgTVA: inputValues.msgTVA ?? ''
        }

        if (typeof payload.prixHT.value === 'undefined' && payload.prixHT !== '') {
            payload.prixHT.value = ''
        }
        if (typeof payload.prixHT.code === 'undefined' && payload.prixHT !== '') {
            payload.prixHT.code = ''
        }

        if (typeof payload.prixTTC.value === 'undefined' && payload.prixTTC !== '') {
            payload.prixTTC.value = ''
        }
        if (typeof payload.prixTTC.code === 'undefined' && payload.prixTTC !== '') {
            payload.prixTTC.code = ''
        }
        if (typeof payload.tva.value === 'undefined' && payload.tva !== '') {
            payload.tva.value = ''
        }
        if (typeof payload.tva.code === 'undefined' && payload.tva !== '') {
            payload.tva.code = ''
        }

        await storeCustomerListFacture.filterBy(payload)
        itemsTable.value = [...storeCustomerListFacture.itemsCustomerFacture]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeCustomerListFacture.fetch()
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
                    :current-page="storeCustomerListFacture.currentPage"
                    :fields="tabFields"
                    :first-page="storeCustomerListFacture.firstPage"
                    :items="itemsTable"
                    :last-page="storeCustomerListFacture.lastPage"
                    :min="AddForm"
                    :next-page="storeCustomerListFacture.nextPage"
                    :pag="storeCustomerListFacture.pagination"
                    :previous-page="storeCustomerListFacture.previousPage"
                    :user="roleuser"
                    form="formCustomerFactureCardableTable"
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
                    <AppFormCardable id="addCustomerFacture" :fields="fieldsForm" :model-value="formData" label-cols/>
                    <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                        <div v-for="violation in violations" :key="violation">
                            <li>{{ violation.message }}</li>
                        </div>
                    </div>
                    <AppCol class="btnright">
                        <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutCustomerFacture">
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
