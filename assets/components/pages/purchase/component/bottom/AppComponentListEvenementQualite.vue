<script setup>
    import {computed, ref} from 'vue'
    import {useComponentListEvenementQualiteStore} from '../../../../../stores/purchase/component/componentListEvenementQualite'
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

    const storeComponentListEvenementQualite = useComponentListEvenementQualiteStore()
    storeComponentListEvenementQualite.setIdComponent(componentId)
    await storeComponentListEvenementQualite.fetch()
    const itemsTable = ref(storeComponentListEvenementQualite.itemsComponentEvenementQualite)
    const formData = ref({
        creeLe: null, detectePar: null, ref: null, description: null, responsable: null, localisation: null, societe: null, progression: null, statut: null
    })

    const fieldsForm = [
        {
            create: false,
            filter: true,
            label: 'Crée le',
            name: 'creeLe',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Détecté par',
            name: 'detectePar',
            sort: true,
            type: 'text',
            update: true
        },
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
            label: 'Description',
            name: 'description',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Responsable',
            name: 'responsable',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Localisation',
            name: 'localisation',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Société',
            name: 'societe',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Progression',
            name: 'progression',
            sort: true,
            measure: {
                code: null,
                value: null
            },
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Statut',
            name: 'statut',
            sort: true,
            type: 'text',
            update: true
        }
    ]

    const parentQtyComponent = {
        $id: 'componentEvenementQualiteQtyComponent'
    }
    const storeUnitEvenementQualiteQtyComponent = useField(fieldsForm[7], parentQtyComponent)
    await storeUnitEvenementQualiteQtyComponent.fetch()

    fieldsForm[7].measure.code = storeUnitEvenementQualiteQtyComponent.measure.code
    fieldsForm[7].measure.value = storeUnitEvenementQualiteQtyComponent.measure.value

    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Crée le',
            name: 'creeLe',
            sort: true,
            type: 'date',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Détecté par',
            name: 'detectePar',
            sort: true,
            type: 'text',
            update: true
        },
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
            label: 'Description',
            name: 'description',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Responsable',
            name: 'responsable',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Localisation',
            name: 'localisation',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Société',
            name: 'societe',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Progression',
            name: 'progression',
            sort: true,
            measure: {
                code: storeUnitEvenementQualiteQtyComponent.measure.code,
                value: storeUnitEvenementQualiteQtyComponent.measure.value
            },
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Statut',
            name: 'statut',
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

    // async function ajoutComponentEvenementQualite(){
    //     // const form = document.getElementById('addComponentEvenementQualite')
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
    //     violations = await storeComponentListEvenementQualite.addComponentEvenementQualite(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeComponentListEvenementQualite.itemsComponentEvenementQualite]
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
            creeLe: item.creeLe,
            detectePar: item.detectePar,
            ref: item.ref,
            description: item.description,
            responsable: item.responsable,
            localisation: item.localisationa,
            societe: item.societe,
            progression: item.progression,
            statut: item.statut
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeComponentListEvenementQualite.deleted(id)
        itemsTable.value = [...storeComponentListEvenementQualite.itemsComponentEvenementQualite]
    }
    async function getPage(nPage){
        await storeComponentListEvenementQualite.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeComponentListEvenementQualite.itemsComponentEvenementQualite]
    }
    async function trierAlphabet(payload) {
        await storeComponentListEvenementQualite.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }

    async function search(inputValues) {
        const payload = {
            creeLe: inputValues.creeLe ?? '',
            detectePar: inputValues.detectePar ?? '',
            ref: inputValues.ref ?? '',
            description: inputValues.description ?? '',
            responsable: inputValues.responsable ?? '',
            localisation: inputValues.localisation ?? '',
            societe: inputValues.societe ?? '',
            progression: inputValues.progression ?? '',
            statut: inputValues.statut ?? ''
        }

        if (typeof payload.progression.value === 'undefined' && payload.progression !== '') {
            payload.progression.value = ''
        }
        if (typeof payload.progression.code === 'undefined' && payload.progression !== '') {
            payload.progression.code = ''
        }

        await storeComponentListEvenementQualite.filterBy(payload)
        itemsTable.value = [...storeComponentListEvenementQualite.itemsComponentEvenementQualite]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        await storeComponentListEvenementQualite.fetch()
    }
</script>

<template>
    <AppCardableTable
        :current-page="storeComponentListEvenementQualite.currentPage"
        :fields="tabFields"
        :first-page="storeComponentListEvenementQualite.firstPage"
        :items="itemsTable"
        :last-page="storeComponentListEvenementQualite.lastPage"
        :min="AddForm"
        :next-page="storeComponentListEvenementQualite.nextPage"
        :pag="storeComponentListEvenementQualite.pagination"
        :previous-page="storeComponentListEvenementQualite.previousPage"
        :user="roleuser"
        form="formComponentEvenementQualiteCardableTable"
        @update="update"
        @deleted="deleted"
        @get-page="getPage"
        @trier-alphabet="trierAlphabet"
        @search="search"
        @cancel-search="cancelSearch"/>
</template>
