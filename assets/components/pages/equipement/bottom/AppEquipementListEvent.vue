<script setup>
    import {computed, ref} from 'vue'
    import {useCompanyListEventStore} from '../../../../stores/equipement/equipementListEvent'
    import {useRoute} from 'vue-router'

    const roleuser = ref('reader')
    const updated = ref(false)
    const AddForm = ref(false)
    const sortable = ref(false)
    const filter = ref(false)
    let trierAlpha = {}
    let filterBy = {}

    const maRoute = useRoute()
    const equipementId = maRoute.params.id_company

    const storeEquipementListEvent = useCompanyListEventStore()
    storeEquipementListEvent.setIdCompany(equipementId)
    await storeEquipementListEvent.fetch()
    const itemsTable = ref(storeEquipementListEvent.itemsEquipementEvent)
    const formData = ref({
        date: null, done: null, emergency: null, etat: null, interventionNotes: null, type: null
    })

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
            label: 'Fait',
            name: 'done',
            sort: true,
            type: 'boolean',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Urgence',
            name: 'emergency',
            sort: true,
            type: 'text',
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
            label: 'Notes d\'intervention',
            name: 'interventionNotes',
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
        }
    ]

    function update(item) {
        updated.value = true
        AddForm.value = true

        const itemsData = {
            date: item.date,
            done: item.done,
            emergency: item.emergency,
            etat: item.etat,
            interventionNotes: item.interventionNotes,
            type: item.type
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeEquipementListEvent.deleted(id)
        itemsTable.value = [...storeEquipementListEvent.itemsEquipementEvent]
    }
    async function getPage(nPage){
        await storeEquipementListEvent.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeEquipementListEvent.itemsEquipementEvent]
    }
    async function trierAlphabet(payload) {
        await storeEquipementListEvent.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }

    async function search(inputValues) {
        const payload = {
            date: inputValues.date ?? '',
            done: inputValues.done ?? '',
            emergency: inputValues.emergency ?? '',
            etat: inputValues.etat ?? '',
            interventionNotes: inputValues.interventionNotes ?? '',
            type: inputValues.type ?? ''
        }

        await storeEquipementListEvent.filterBy(payload)
        itemsTable.value = [...storeEquipementListEvent.itemsEquipementEvent]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        await storeEquipementListEvent.fetch()
        itemsTable.value = storeEquipementListEvent.itemsEquipementEvent
    }
</script>

<template>
    <div class="gui-bottom">
        <AppRow>
            <AppCol>
                <AppCardableTable
                    :current-page="storeEquipementListEvent.currentPage"
                    :fields="tabFields"
                    :first-page="storeEquipementListEvent.firstPage"
                    :items="itemsTable"
                    :last-page="storeEquipementListEvent.lastPage"
                    :min="AddForm"
                    :next-page="storeEquipementListEvent.nextPage"
                    :pag="storeEquipementListEvent.pagination"
                    :previous-page="storeEquipementListEvent.previousPage"
                    :user="roleuser"
                    form="formCompanyEventCardableTable"
                    should-delete="false"
                    should-see="false"
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
