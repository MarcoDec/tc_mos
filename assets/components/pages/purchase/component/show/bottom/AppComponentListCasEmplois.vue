<script setup>
    import {computed, ref} from 'vue'
    import {useComponentListCasEmploisStore} from '../../../../../../stores/purchase/component/componentListCasEmplois'
    import {useRoute} from 'vue-router'
    import useField from '../../../../../../stores/field/field'

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

    const storeComponentListCasEmplois = useComponentListCasEmploisStore()
    storeComponentListCasEmplois.setIdComponent(componentId)
    await storeComponentListCasEmplois.fetch()
    const itemsTable = ref(storeComponentListCasEmplois.itemsComponentCasEmplois)
    const formData = ref({
        ref: null, etat: null, client: null, volumeAnnuel: null, name: null, quantiteProduit: null, volumeAnnuelComposant: null
    })

    const fieldsForm = [
        {
            create: false,
            filter: true,
            label: 'Référence produit ',
            name: 'ref',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Client',
            name: 'client',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Volume Annuel',
            name: 'volumeAnnuel',
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
            label: 'Etat',
            name: 'etat',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Nom',
            name: 'name',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité par produit',
            name: 'quantiteProduit',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: false,
            label: 'Volume annuel par composant',
            name: 'volumeAnnuelComposant',
            sort: false,
            measure: {
                value: null,
                code: null
            },
            type: 'measure',
            update: true
        }
    ]

    const parentQtyProduit = {
        $id: 'componentCasEmploisQtyProduitAnnuel'
    }
    const storeUnitCasEmploisQtyAnnuel = useField(fieldsForm[2], parentQtyProduit)
    await storeUnitCasEmploisQtyAnnuel.fetch()

    fieldsForm[2].measure.code = storeUnitCasEmploisQtyAnnuel.measure.code
    fieldsForm[2].measure.value = storeUnitCasEmploisQtyAnnuel.measure.value

    const parentVolumeAnnuel = {
        $id: 'componentCasEmploisVolumeAnnuel'
    }
    const storeUnitCasEmploisVolumeAnnuel = useField(fieldsForm[6], parentVolumeAnnuel)
    fieldsForm[6].measure.code = storeUnitCasEmploisVolumeAnnuel.measure.code
    fieldsForm[6].measure.value = storeUnitCasEmploisVolumeAnnuel.measure.value

    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Référence produit ',
            name: 'ref',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Client',
            name: 'client',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Volume Annuel',
            name: 'volumeAnnuel',
            sort: true,
            measure: {
                value: storeUnitCasEmploisQtyAnnuel.measure.value,
                code: storeUnitCasEmploisQtyAnnuel.measure.code
            },
            type: 'measure',
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
            label: 'Nom',
            name: 'name',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité par produit',
            name: 'quantiteProduit',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: false,
            label: 'Volume annuel par composant',
            name: 'volumeAnnuelComposant',
            sort: false,
            measure: {
                value: storeUnitCasEmploisVolumeAnnuel.measure.value,
                code: storeUnitCasEmploisVolumeAnnuel.measure.code
            },
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

    // async function ajoutComponentCasEmplois(){
    //     // const form = document.getElementById('addComponentCasEmplois')
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
    //     violations = await storeComponentListCasEmplois.addComponentCasEmplois(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeComponentListCasEmplois.itemsComponentCasEmplois]
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
            etat: item.etat,
            client: item.client,
            volumeAnnuel: item.volumeAnnuel,
            name: item.name,
            quantiteProduit: item.quantiteProduit,
            volumeAnnuelComposant: item.volumeAnnuelComposant

        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeComponentListCasEmplois.deleted(id)
        itemsTable.value = [...storeComponentListCasEmplois.itemsComponentCasEmplois]
    }
    async function getPage(nPage){
        await storeComponentListCasEmplois.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeComponentListCasEmplois.itemsComponentCasEmplois]
    }
    async function trierAlphabet(payload) {
        await storeComponentListCasEmplois.sortableItems(payload, filterBy, filter)
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
            etat: inputValues.etat ?? '',
            client: inputValues.client ?? '',
            volumeAnnuel: inputValues.volumeAnnuel ?? '',
            name: inputValues.name ?? '',
            quantiteProduit: inputValues.quantiteProduit ?? '',
            volumeAnnuelComposant: inputValues.volumeAnnuelComposant ?? ''
        }

        if (typeof payload.volumeAnnuel.value === 'undefined' && payload.volumeAnnuel !== '') {
            payload.volumeAnnuel.value = ''
        }
        if (typeof payload.volumeAnnuel.code === 'undefined' && payload.volumeAnnuel !== '') {
            payload.volumeAnnuel.code = ''
        }

        // if (typeof payload.volumeAnnuelComposant.value === 'undefined' && payload.volumeAnnuelComposant !== '') {
        //     payload.volumeAnnuelComposant.value = ''
        // }
        // if (typeof payload.volumeAnnuelComposant.code === 'undefined' && payload.volumeAnnuelComposant !== '') {
        //     payload.volumeAnnuelComposant.code = ''
        // }

        await storeComponentListCasEmplois.filterBy(payload)
        itemsTable.value = [...storeComponentListCasEmplois.itemsComponentCasEmplois]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        await storeComponentListCasEmplois.fetch()
    }
</script>

<template>
    <AppCardableTable
        :current-page="storeComponentListCasEmplois.currentPage"
        :fields="tabFields"
        :first-page="storeComponentListCasEmplois.firstPage"
        :items="itemsTable"
        :last-page="storeComponentListCasEmplois.lastPage"
        :min="AddForm"
        :next-page="storeComponentListCasEmplois.nextPage"
        :pag="storeComponentListCasEmplois.pagination"
        :previous-page="storeComponentListCasEmplois.previousPage"
        :user="roleuser"
        form="formComponentCasEmploisCardableTable"
        @update="update"
        @deleted="deleted"
        @get-page="getPage"
        @trier-alphabet="trierAlphabet"
        @search="search"
        @cancel-search="cancelSearch"/>
</template>
