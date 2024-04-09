<script setup>
    import {computed, ref} from 'vue'
    import {useComponentListReceiptStore} from '../../../../../../stores/purchase/component/componentListReceipt'
    import {useRoute} from 'vue-router'
    // import useField from '../../../../../stores/field/field'

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

    const storeComponentListReceipt = useComponentListReceiptStore()
    storeComponentListReceipt.setIdComponent(componentId)
    await storeComponentListReceipt.fetch()
    const itemsTable = ref(storeComponentListReceipt.itemsComponentReceipt)
    const formData = ref({
        date: null, libelle: null, type: null, nbPieceControle: null, valeurAttendue: null
    })

    // const fieldsForm = [
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Date création',
    //         name: 'date',
    //         sort: true,
    //         type: 'date',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Libellé',
    //         name: 'libelle',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Type ',
    //         name: 'type',
    //         sort: true,
    //         // options: {label: 'a'},//regardeez AppsupplierListCommande pour select
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Nombre de pièce à controler',
    //         name: 'nbPieceControle',
    //         sort: true,
    //         measure: {
    //             code: null,
    //             value: null
    //         },
    //         type: 'measure',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Valeur attendue',
    //         name: 'valeurAttendue',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     }
    // ]

    // const parentQtyComponent = {
    //     //$id: `${warehouseId}Stock`
    //     $id: 'componentReceiptQtyComponent'
    // }
    // const storeUnitReceiptQtyComponent = useField(fieldsForm[3], parentQtyComponent)
    // await storeUnitReceiptQtyComponent.fetch()

    // fieldsForm[3].measure.code = storeUnitReceiptQtyComponent.measure.code
    // fieldsForm[3].measure.value = storeUnitReceiptQtyComponent.measure.value

    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Date création',
            name: 'date',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Libellé',
            name: 'libelle',
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
        },
        {
            create: false,
            filter: true,
            label: 'Nombre de pièce à controler',
            name: 'nbPieceControle',
            sort: true,
            // measure: {
            //     code: null,
            //     value: null
            // },
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Valeur attendue',
            name: 'valeurAttendue',
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

    // async function ajoutComponentReceipt(){
    //     // const form = document.getElementById('addComponentReceipt')
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
    //     violations = await storeComponentListReceipt.addComponentReceipt(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeComponentListReceipt.itemsComponentReceipt]
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
            of: item.of,
            produit: item.produit,
            date: item.date,
            quantiteComposant: item.quantiteComposant
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeComponentListReceipt.deleted(id)
        itemsTable.value = [...storeComponentListReceipt.itemsComponentReceipt]
    }
    async function getPage(nPage){
        await storeComponentListReceipt.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeComponentListReceipt.itemsComponentReceipt]
    }
    async function trierAlphabet(payload) {
        await storeComponentListReceipt.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }

    async function search(inputValues) {
        const payload = {
            of: inputValues.of ?? '',
            produit: inputValues.produit ?? '',
            date: inputValues.date ?? '',
            quantiteComposant: inputValues.quantiteComposant ?? ''
        }

        if (typeof payload.quantiteComposant.value === 'undefined' && payload.quantiteComposant !== '') {
            payload.quantiteComposant.value = ''
        }
        if (typeof payload.quantiteComposant.code === 'undefined' && payload.quantiteComposant !== '') {
            payload.quantiteComposant.code = ''
        }

        await storeComponentListReceipt.filterBy(payload)
        itemsTable.value = [...storeComponentListReceipt.itemsComponentReceipt]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeComponentListReceipt.fetch()
    }
</script>

<template>
    <AppCardableTable
        :current-page="storeComponentListReceipt.currentPage"
        :fields="tabFields"
        :first-page="storeComponentListReceipt.firstPage"
        :items="itemsTable"
        :last-page="storeComponentListReceipt.lastPage"
        :min="AddForm"
        :next-page="storeComponentListReceipt.nextPage"
        :pag="storeComponentListReceipt.pagination"
        :previous-page="storeComponentListReceipt.previousPage"
        :user="roleuser"
        form="formComponentReceiptCardableTable"
        @update="update"
        @deleted="deleted"
        @get-page="getPage"
        @trier-alphabet="trierAlphabet"
        @search="search"
        @cancel-search="cancelSearch"/>
</template>
