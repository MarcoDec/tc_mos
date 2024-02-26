<script setup>
    import {computed, ref} from 'vue'
    import {useComponentListReferenceValueStore} from '../../../../../../stores/purchase/component/componentListReferenceValue'
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

    const storeComponentListReferenceValue = useComponentListReferenceValueStore()
    storeComponentListReferenceValue.setIdComponent(componentId)
    await storeComponentListReferenceValue.fetch()
    const itemsTable = ref(storeComponentListReferenceValue.itemsComponentReferenceValue)
    const formData = ref({
        height: null, section: null, tensile: null, width: null
    })

    const fieldsForm = [
        {
            create: false,
            filter: true,
            label: 'Hauteur',
            name: 'height',
            sort: false,
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
            label: 'Section',
            name: 'section',
            measure: {
                code: null,
                value: null
            },
            sort: true,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Résistance',
            name: 'tensile',
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
            label: 'Largeur',
            name: 'width',
            measure: {
                code: null,
                value: null
            },
            sort: true,
            type: 'measure',
            update: true
        }
    ]

    const parentQtyHauteur = {
        $id: 'componentReferenceValueQtyHauteur'
    }

    const storeUnitReferenceValueQtyHauteur = useField(fieldsForm[0], parentQtyHauteur)
    await storeUnitReferenceValueQtyHauteur.fetch()

    fieldsForm[0].measure.code = storeUnitReferenceValueQtyHauteur.measure.code
    fieldsForm[0].measure.value = storeUnitReferenceValueQtyHauteur.measure.value

    const parentQtySection = {
        $id: 'componentReferenceValueQtySection'
    }
    const storeUnitReferenceValueQtySection = useField(fieldsForm[1], parentQtySection)

    fieldsForm[1].measure.code = storeUnitReferenceValueQtySection.measure.code
    fieldsForm[1].measure.value = storeUnitReferenceValueQtySection.measure.value

    const parentQtyResistance = {
        $id: 'componentReferenceValueQtyResistance'
    }
    const storeUnitReferenceValueQtyResistance = useField(fieldsForm[2], parentQtyResistance)

    fieldsForm[2].measure.code = storeUnitReferenceValueQtyResistance.measure.code
    fieldsForm[2].measure.value = storeUnitReferenceValueQtyResistance.measure.value

    const parentQtyLargeur = {
        $id: 'componentReferenceValueQtyLargeur'
    }
    const storeUnitReferenceValueQtyLargeur = useField(fieldsForm[3], parentQtyLargeur)
    fieldsForm[3].measure.code = storeUnitReferenceValueQtyLargeur.measure.code
    fieldsForm[3].measure.value = storeUnitReferenceValueQtyLargeur.measure.value
    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Hauteur',
            name: 'height',
            measure: {
                value: storeUnitReferenceValueQtyHauteur.measure.value,
                code: storeUnitReferenceValueQtyHauteur.measure.code
            },
            sort: false,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Section',
            name: 'section',
            measure: {
                value: storeUnitReferenceValueQtySection.measure.value,
                code: storeUnitReferenceValueQtySection.measure.code
            },
            sort: true,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Résistance',
            name: 'tensile',
            measure: {
                value: storeUnitReferenceValueQtyResistance.measure.value,
                code: storeUnitReferenceValueQtyResistance.measure.code
            },
            sort: true,
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Largeur',
            name: 'width',
            measure: {
                value: storeUnitReferenceValueQtyLargeur.measure.value,
                code: storeUnitReferenceValueQtyLargeur.measure.code
            },
            sort: true,
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

    // async function ajoutComponentReferenceValue(){
    //     // const form = document.getElementById('addComponentReferenceValue')
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
    //     violations = await storeComponentListReferenceValue.addComponentReferenceValue(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeComponentListReferenceValue.itemsComponentReferenceValue]
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
            height: item.height,
            section: item.section,
            tensile: item.tensile,
            width: item.width
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeComponentListReferenceValue.deleted(id)
        itemsTable.value = [...storeComponentListReferenceValue.itemsComponentReferenceValue]
    }
    async function getPage(nPage){
        await storeComponentListReferenceValue.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeComponentListReferenceValue.itemsComponentReferenceValue]
    }
    async function trierAlphabet(payload) {
        await storeComponentListReferenceValue.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }

    async function search(inputValues) {
        const payload = {
            height: inputValues.height ?? '',
            section: inputValues.section ?? '',
            tensile: inputValues.tensile ?? '',
            width: inputValues.width ?? ''
        }
        if (typeof payload.height.value === 'undefined' && payload.height !== '') {
            payload.height.value = ''
        }
        if (typeof payload.height.code === 'undefined' && payload.height !== '') {
            payload.height.code = ''
        }
        if (typeof payload.section.code === 'undefined' && payload.section !== '') {
            payload.section.code = ''
        }
        if (typeof payload.section.value === 'undefined' && payload.section !== '') {
            payload.section.value = ''
        }
        if (typeof payload.tensile.value === 'undefined' && payload.tensile !== '') {
            payload.tensile.value = ''
        }
        if (typeof payload.tensile.code === 'undefined' && payload.tensile !== '') {
            payload.tensile.code = ''
        }
        if (typeof payload.width.value === 'undefined' && payload.width !== '') {
            payload.width.value = ''
        }
        if (typeof payload.width.code === 'undefined' && payload.width !== '') {
            payload.width.code = ''
        }

        await storeComponentListReferenceValue.filterBy(payload)
        itemsTable.value = [...storeComponentListReferenceValue.itemsComponentReferenceValue]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        await storeComponentListReferenceValue.fetch()
    }
</script>

<template>
    <AppCardableTable
        :current-page="storeComponentListReferenceValue.currentPage"
        :fields="tabFields"
        :first-page="storeComponentListReferenceValue.firstPage"
        :items="itemsTable"
        :last-page="storeComponentListReferenceValue.lastPage"
        :min="AddForm"
        :next-page="storeComponentListReferenceValue.nextPage"
        :pag="storeComponentListReferenceValue.pagination"
        :previous-page="storeComponentListReferenceValue.previousPage"
        :user="roleuser"
        form="formComponentReferenceValueCardableTable"
        @update="update"
        @deleted="deleted"
        @get-page="getPage"
        @trier-alphabet="trierAlphabet"
        @search="search"
        @cancel-search="cancelSearch"/>
</template>
