<script setup>
    import {computed, ref} from 'vue'
    import {useComponentListBesoinEnregistreStore} from '../../../../../stores/purchase/component/componentListBesoinEnregistre'
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
    const componentId = maRoute.params.id_component

    const storeComponentListBesoinEnregistre = useComponentListBesoinEnregistreStore()
    storeComponentListBesoinEnregistre.setIdComponent(componentId)
    await storeComponentListBesoinEnregistre.fetch()
    const itemsTable = ref(storeComponentListBesoinEnregistre.itemsComponentBesoinEnregistre)
    const formData = ref({
        refVente: null, ref: null, stockReel: null, stockDispo: null, date: null, quantiteProduit: null, quantiteAExpedier: null, consomationStock: null, besoinProd: null, besoinComponent: null
    })

    const fieldsForm = [
        {
            create: false,
            filter: true,
            label: 'Vente',
            name: 'refVente',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Référence produit',
            name: 'ref',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Stock réel produit',
            name: 'stockReel',
            sort: true,
            measure: {
                value: null,
                code: null
            },
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Stock disponible produit',
            name: 'stockDispo',
            sort: true,
            measure: {
                value: null,
                code: null
            },
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de livraison',
            name: 'date',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité produit',
            name: 'quantiteProduit',
            sort: true,
            measure: {
                value: null,
                code: null
            },
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité à expédier',
            name: 'quantiteAExpedier',
            sort: true,
            measure: {
                value: null,
                code: null
            },
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: false,
            label: 'Consommation stock produit',
            name: 'consomationStock',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: false,
            label: 'Besoin produit',
            name: 'besoinProd',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: false,
            label: 'Besoin component',
            name: 'besoinComponent',
            sort: false,
            type: 'text',
            update: true
        }
    ]

    const parentStockReel = {
        $id: 'componentBesoinEnregistreStockReel'
    }
    const storeUnitBesoinEnregistreStockReel = useField(fieldsForm[2], parentStockReel)
    await storeUnitBesoinEnregistreStockReel.fetch()

    fieldsForm[2].measure.code = storeUnitBesoinEnregistreStockReel.measure.code
    fieldsForm[2].measure.value = storeUnitBesoinEnregistreStockReel.measure.value

    const parentStockDispo = {
        $id: 'componentBesoinEnregistreStockDispo'
    }
    const storeUnitBesoinEnregistreStockDispo = useField(fieldsForm[3], parentStockDispo)

    fieldsForm[3].measure.code = storeUnitBesoinEnregistreStockDispo.measure.code
    fieldsForm[3].measure.value = storeUnitBesoinEnregistreStockDispo.measure.value

    const parentQtyProd = {
        $id: 'componentBesoinEnregistreQtyProd'
    }
    const storeUnitBesoinEnregistreQtyProd = useField(fieldsForm[5], parentQtyProd)

    fieldsForm[5].measure.code = storeUnitBesoinEnregistreQtyProd.measure.code
    fieldsForm[5].measure.value = storeUnitBesoinEnregistreQtyProd.measure.value

    const parentQtyAExpedier = {
        $id: 'componentBesoinEnregistreQtyAExpedier'
    }
    const storeUnitBesoinEnregistreQtyAExpedier = useField(fieldsForm[6], parentQtyAExpedier)
    fieldsForm[6].measure.code = storeUnitBesoinEnregistreQtyAExpedier.measure.code
    fieldsForm[6].measure.value = storeUnitBesoinEnregistreQtyAExpedier.measure.value

    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Vente',
            name: 'refVente',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Référence produit',
            name: 'ref',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Stock réel produit',
            name: 'stockReel',
            sort: true,
            measure: {
                value: storeUnitBesoinEnregistreStockReel.measure.value,
                code: storeUnitBesoinEnregistreStockReel.measure.code
            },
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Stock disponible produit',
            name: 'stockDispo',
            sort: true,
            measure: {
                value: storeUnitBesoinEnregistreStockDispo.measure.value,
                code: storeUnitBesoinEnregistreStockDispo.measure.code
            },
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de livraison',
            name: 'date',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité produit',
            name: 'quantiteProduit',
            sort: true,
            measure: {
                value: storeUnitBesoinEnregistreQtyProd.measure.value,
                code: storeUnitBesoinEnregistreQtyProd.measure.code
            },
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité à expédier',
            name: 'quantiteAExpedier',
            sort: true,
            measure: {
                value: storeUnitBesoinEnregistreQtyAExpedier.measure.value,
                code: storeUnitBesoinEnregistreQtyAExpedier.measure.code
            },
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: false,
            label: 'Consommation stock par produit',
            name: 'consomationStock',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: false,
            label: 'Besoin produit',
            name: 'besoinProd',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: false,
            label: 'Besoin component',
            name: 'besoinComponent',
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
    //         quantiteEffectuee: null,
    //         dateLivraison: null,
    //         dateLivraisonSouhaitee: null
    //     }
    //     formData.value = itemsNull
    // }

    // async function ajoutComponentBesoinEnregistre(){
    //     // const form = document.getElementById('addComponentBesoinEnregistre')
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
    //     violations = await storeComponentListBesoinEnregistre.addComponentBesoinEnregistre(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeComponentListBesoinEnregistre.itemsComponentBesoinEnregistre]
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
            refVente: item.refVente,
            ref: item.ref,
            stockReel: item.stockReel,
            stockDispo: item.stockDispo,
            date: item.date,
            quantiteProduit: item.quantiteProduit,
            quantiteAExpedier: item.quantiteAExpedier,
            consomationStock: item.consomationStock,
            besoinProd: item.besoinProd,
            besoinComponent: item.besoinComponent

        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeComponentListBesoinEnregistre.deleted(id)
        itemsTable.value = [...storeComponentListBesoinEnregistre.itemsComponentBesoinEnregistre]
    }
    async function getPage(nPage){
        await storeComponentListBesoinEnregistre.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeComponentListBesoinEnregistre.itemsComponentBesoinEnregistre]
    }
    async function trierAlphabet(payload) {
        await storeComponentListBesoinEnregistre.sortableItems(payload, filterBy, filter)
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
            refVente: inputValues.refVente ?? '',
            ref: inputValues.ref ?? '',
            stockReel: inputValues.stockReel ?? '',
            stockDispo: inputValues.stockDispo ?? '',
            date: inputValues.date ?? '',
            quantiteProduit: inputValues.quantiteProduit ?? '',
            quantiteAExpedier: inputValues.quantiteAExpedier ?? '',
            consomationStock: inputValues.consomationStock ?? '',
            besoinProd: inputValues.besoinProd ?? '',
            besoinComponent: inputValues.besoinComponent ?? ''
        }

        if (typeof payload.stockReel.value === 'undefined' && payload.stockReel !== '') {
            payload.stockReel.value = ''
        }
        if (typeof payload.stockReel.code === 'undefined' && payload.stockReel !== '') {
            payload.stockReel.code = ''
        }
        if (typeof payload.stockDispo.value === 'undefined' && payload.stockDispo !== '') {
            payload.stockDispo.value = ''
        }
        if (typeof payload.stockDispo.code === 'undefined' && payload.stockDispo !== '') {
            payload.stockDispo.code = ''
        }
        if (typeof payload.quantiteProduit.value === 'undefined' && payload.quantiteProduit !== '') {
            payload.quantiteProduit.value = ''
        }
        if (typeof payload.quantiteProduit.code === 'undefined' && payload.quantiteProduit !== '') {
            payload.quantiteProduit.code = ''
        }
        if (typeof payload.quantiteAExpedier.value === 'undefined' && payload.quantiteAExpedier !== '') {
            payload.quantiteAExpedier.value = ''
        }
        if (typeof payload.quantiteAExpedier.code === 'undefined' && payload.quantiteAExpedier !== '') {
            payload.quantiteAExpedier.code = ''
        }

        await storeComponentListBesoinEnregistre.filterBy(payload)
        itemsTable.value = [...storeComponentListBesoinEnregistre.itemsComponentBesoinEnregistre]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeComponentListBesoinEnregistre.fetch()
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
                    :current-page="storeComponentListBesoinEnregistre.currentPage"
                    :fields="tabFields"
                    :first-page="storeComponentListBesoinEnregistre.firstPage"
                    :items="itemsTable"
                    :last-page="storeComponentListBesoinEnregistre.lastPage"
                    :min="AddForm"
                    :next-page="storeComponentListBesoinEnregistre.nextPage"
                    :pag="storeComponentListBesoinEnregistre.pagination"
                    :previous-page="storeComponentListBesoinEnregistre.previousPage"
                    :user="roleuser"
                    form="formComponentBesoinEnregistreCardableTable"
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
                    <AppFormCardable id="addComponentBesoinEnregistre" :fields="fieldsForm" :model-value="formData" label-cols/>
                    <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                        <div v-for="violation in violations" :key="violation">
                            <li>{{ violation.message }}</li>
                        </div>
                    </div>
                    <AppCol class="btnright">
                        <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutComponentBesoinEnregistre">
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
