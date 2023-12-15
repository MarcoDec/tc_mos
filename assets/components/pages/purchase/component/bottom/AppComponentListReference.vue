<script setup>
    import {computed, ref} from 'vue'
    import {useComponentListReferenceStore} from '../../../../../stores/purchase/component/componentListReference'
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

    const storeComponentListReference = useComponentListReferenceStore()
    storeComponentListReference.setIdComponent(componentId)
    await storeComponentListReference.fetch()
    const itemsTable = ref(storeComponentListReference.itemsComponentReference)
    const formData = ref({
        name: null, kind: null, sampleQuantity: null, minValue: null, maValue: null
    })

    const fieldsForm = [
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
            label: 'Type',
            name: 'kind',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Nombre d\'échantillon',
            name: 'sampleQuantity',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Valeur minimale',
            name: 'minValue',
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
            label: 'Valeur maximale',
            name: 'maxValue',
            sort: true,
            measure: {
                code: null,
                value: null
            },
            type: 'measure',
            update: true
        }
    ]

    const parentQtyComponentMin = {
        $id: 'componentReferenceQtyComponentMin'
    }
    const storeUnitReferenceQtyComponentMin = useField(fieldsForm[3], parentQtyComponentMin)
    await storeUnitReferenceQtyComponentMin.fetch()

    fieldsForm[3].measure.code = storeUnitReferenceQtyComponentMin.measure.code
    fieldsForm[3].measure.value = storeUnitReferenceQtyComponentMin.measure.value

    const parentQtyComponentMax = {
        $id: 'componentReferenceQtyComponentMax'
    }
    const storeUnitReferenceQtyComponentMax = useField(fieldsForm[4], parentQtyComponentMax)
    await storeUnitReferenceQtyComponentMax.fetch()

    fieldsForm[4].measure.code = storeUnitReferenceQtyComponentMax.measure.code
    fieldsForm[4].measure.value = storeUnitReferenceQtyComponentMax.measure.value

    const tabFields = [
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
            label: 'Type',
            name: 'kind',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Nombre d\'échantillon',
            name: 'sampleQuantity',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Valeur minimale',
            name: 'minValue',
            sort: true,
            measure: {
                code: storeUnitReferenceQtyComponentMin.measure.code,
                value: storeUnitReferenceQtyComponentMin.measure.value
            },
            type: 'measure',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Valeur maximale',
            name: 'maxValue',
            sort: true,
            measure: {
                code: storeUnitReferenceQtyComponentMax.measure.code,
                value: storeUnitReferenceQtyComponentMax.measure.value
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

    // async function ajoutComponentReference(){
    //     // const form = document.getElementById('addComponentReference')
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
    //     violations = await storeComponentListReference.addComponentReference(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeComponentListReference.itemsComponentReference]
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
            name: item.name,
            kind: item.kind,
            sampleQuantity: item.sampleQuantity,
            minValue: item.minValue,
            maxValue: item.maxValue
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeComponentListReference.deleted(id)
        itemsTable.value = [...storeComponentListReference.itemsComponentReference]
    }
    async function getPage(nPage){
        await storeComponentListReference.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeComponentListReference.itemsComponentReference]
    }
    async function trierAlphabet(payload) {
        await storeComponentListReference.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }

    async function search(inputValues) {
        const payload = {
            name: inputValues.name ?? '',
            kind: inputValues.kind ?? '',
            sampleQuantity: inputValues.sampleQuantity ?? '',
            minValue: inputValues.minValue ?? '',
            maxValue: inputValues.maxValue ?? ''
        }

        if (typeof payload.minValue.value === 'undefined' && payload.minValue !== '') {
            payload.minValue.value = ''
        }
        if (typeof payload.minValue.code === 'undefined' && payload.minValue !== '') {
            payload.minValue.code = ''
        }

        if (typeof payload.maxValue.value === 'undefined' && payload.maxValue !== '') {
            payload.maxValue.value = ''
        }
        if (typeof payload.maxValue.code === 'undefined' && payload.maxValue !== '') {
            payload.maxValue.code = ''
        }
        await storeComponentListReference.filterBy(payload)
        itemsTable.value = [...storeComponentListReference.itemsComponentReference]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeComponentListReference.fetch()
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
                    :current-page="storeComponentListReference.currentPage"
                    :fields="tabFields"
                    :first-page="storeComponentListReference.firstPage"
                    :items="itemsTable"
                    :last-page="storeComponentListReference.lastPage"
                    :min="AddForm"
                    :next-page="storeComponentListReference.nextPage"
                    :pag="storeComponentListReference.pagination"
                    :previous-page="storeComponentListReference.previousPage"
                    :user="roleuser"
                    form="formComponentReferenceCardableTable"
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
                    <AppFormCardable id="addComponentReference" :fields="fieldsForm" :model-value="formData" label-cols/>
                    <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                        <div v-for="violation in violations" :key="violation">
                            <li>{{ violation.message }}</li>
                        </div>
                    </div>
                    <AppCol class="btnright">
                        <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutComponentReference">
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
