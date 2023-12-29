<script setup>
    import {computed, ref} from 'vue'
    import {useCompanyListPrevisionStore} from '../../../../stores/equipement/equipementListPrevision'
    import {useRoute} from 'vue-router'
    import useField from '../../../../stores/field/field'

    const roleuser = ref('reader')
    const updated = ref(false)
    const AddForm = ref(false)
    const sortable = ref(false)
    const filter = ref(false)
    let trierAlpha = {}
    let filterBy = {}

    const maRoute = useRoute()
    const equipementId = maRoute.params.id_company

    const storeEquipementListPrevision = useCompanyListPrevisionStore()
    storeEquipementListPrevision.setIdCompany(equipementId)
    await storeEquipementListPrevision.fetch()
    const itemsTable = ref(storeEquipementListPrevision.itemsEquipementPrevision)
    const formData = ref({
        date: null, type: null, name: null, quantite: null
    })

    const fieldsForm = [
        {
            create: false,
            filter: true,
            label: 'Date',
            name: 'date',
            sort: true,
            type: 'date',
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
            label: 'Note',
            name: 'name',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'quantite',
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

    const parentQty = {
        $id: 'equipementPrevisionQty'
    }
    const storeUnitEquipementPrevisionQty = useField(fieldsForm[3], parentQty)
    await storeUnitEquipementPrevisionQty.fetch()

    fieldsForm[3].measure.code = storeUnitEquipementPrevisionQty.measure.code
    fieldsForm[3].measure.value = storeUnitEquipementPrevisionQty.measure.value

    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Date',
            name: 'date',
            sort: true,
            type: 'date',
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
            label: 'Note',
            name: 'name',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'quantite',
            name: 'quantite',
            measure: {
                code: storeUnitEquipementPrevisionQty.measure.code,
                value: storeUnitEquipementPrevisionQty.measure.value
            },
            sort: true,
            type: 'measure',
            update: true
        }
    ]

    function update(item) {
        updated.value = true
        AddForm.value = true

        const itemsData = {
            date: item.date,
            type: item.type,
            name: item.name,
            quantite: item.quantite
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeEquipementListPrevision.deleted(id)
        itemsTable.value = [...storeEquipementListPrevision.itemsEquipementPrevision]
    }
    async function getPage(nPage){
        await storeEquipementListPrevision.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeEquipementListPrevision.itemsEquipementPrevision]
    }
    async function trierAlphabet(payload) {
        await storeEquipementListPrevision.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }

    async function search(inputValues) {
        const payload = {
            date: inputValues.date ?? '',
            type: inputValues.type ?? '',
            name: inputValues.name ?? '',
            quantite: inputValues.quantite ?? ''
        }

        if (typeof payload.quantite.value === 'undefined' && payload.quantite !== '') {
            payload.quantite.value = ''
        }
        if (typeof payload.quantite.code === 'undefined' && payload.quantite !== '') {
            payload.quantite.code = ''
        }

        await storeEquipementListPrevision.filterBy(payload)
        itemsTable.value = [...storeEquipementListPrevision.itemsEquipementPrevision]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeEquipementListPrevision.fetch()
    }
</script>

<template>
    <div class="gui-bottom">
        <AppRow>
            <AppCol>
                <AppCardableTable
                    :current-page="storeEquipementListPrevision.currentPage"
                    :fields="tabFields"
                    :first-page="storeEquipementListPrevision.firstPage"
                    :items="itemsTable"
                    :last-page="storeEquipementListPrevision.lastPage"
                    :min="AddForm"
                    :next-page="storeEquipementListPrevision.nextPage"
                    :pag="storeEquipementListPrevision.pagination"
                    :previous-page="storeEquipementListPrevision.previousPage"
                    :user="roleuser"
                    form="formCompanyPrevisionCardableTable"
                    @update="update"
                    @deleted="deleted"
                    @get-page="getPage"
                    @trier-alphabet="trierAlphabet"
                    @search="search"
                    @cancel-search="cancelSearch"/>
            </AppCol>
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
