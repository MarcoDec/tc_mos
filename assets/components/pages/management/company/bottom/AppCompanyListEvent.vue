<script setup>
    import {computed, ref} from 'vue'
    import {useCompanyListEventStore} from '../../../../../stores/company/companyListEvent'
    import {useRoute} from 'vue-router'
    import InlistAddForm from '../../../../form-cardable/inlist-add-form/InlistAddForm.vue'
    // import useField from '../../../../stores/field/field'

    const roleuser = ref('reader')
    const AddForm = ref(false)
    const UpdateForm = ref(false)
    const sortable = ref(false)
    const filter = ref(false)
    let trierAlpha = {}
    let filterBy = {}

    const maRoute = useRoute()
    const companyId = maRoute.params.id_company

    const storeCompanyListEvent = useCompanyListEventStore()
    storeCompanyListEvent.setIdCompany(companyId)
    await storeCompanyListEvent.fetch()
    const itemsTable = ref(storeCompanyListEvent.itemsCompanyEvent)
    const kindOptions = {
        label: value => value,
        options: [{text: 'holiday', value: 'holiday'}]
    }
    //region addFormData
    const addFormData = ref({name: null, date: null, type: null, fini: null, company: `/api/companies/${companyId}`})
    const addFormFields = [
        {
            label: 'Nom',
            name: 'name',
            type: 'text'
        },
        {
            label: 'Date',
            name: 'date',
            type: 'date'
        },
        {
            label: 'Type',
            name: 'kind',
            options: kindOptions,
            type: 'select'
        }
    ]
    //endregion
    //region updateFormData
    const updateFormData = ref({name: null, date: null, type: null, fini: null, company: `/api/companies/${companyId}`})
    const updateFormFields = [
        {
            label: 'Nom',
            name: 'name',
            type: 'text'
        },
        {
            label: 'Date',
            name: 'date',
            type: 'date'
        },
        {
            label: 'Type',
            name: 'kind',
            options: kindOptions,
            type: 'select'
        },
        {
            label: 'Fini',
            name: 'done',
            type: 'boolean'
        }
    ]
    //endregion
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
            options: kindOptions,
            name: 'kind',
            sort: false,
            type: 'select',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Fini',
            name: 'done',
            sort: true,
            type: 'boolean',
            update: true
        }
    ]

    function update(item) {
        const itemsData = item
        itemsData.company = `/api/companies/${companyId}`
        updateFormData.value = {...itemsData}
        UpdateForm.value = true
        AddForm.value = false
    }

    async function onDelete(id){
        await storeCompanyListEvent.deleted(id)
        itemsTable.value = [...storeCompanyListEvent.itemsCompanyEvent]
    }
    async function getPage(nPage){
        await storeCompanyListEvent.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeCompanyListEvent.itemsCompanyEvent]
    }
    async function trierAlphabet(payload) {
        await storeCompanyListEvent.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }
    let searchPayload = {}
    async function search() {
        await storeCompanyListEvent.filterBy(searchPayload)
        itemsTable.value = [...storeCompanyListEvent.itemsCompanyEvent]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        searchPayload = {}
        filter.value = true
        await storeCompanyListEvent.fetch()
        itemsTable.value = [...storeCompanyListEvent.itemsCompanyEvent]
    }
    function ajoute() {
        AddForm.value = true
    }
    function onAddCancel() {
        AddForm.value = false
    }
    function onUpdateCancel() {
        UpdateForm.value = false
    }
    async function onAddFormSubmitted() {
        AddForm.value = false
        await storeCompanyListEvent.fetch()
        itemsTable.value = storeCompanyListEvent.itemsCompanyEvent
    }
    async function onUpdateFormSubmitted() {
        UpdateForm.value = false
        await storeCompanyListEvent.fetch()
        itemsTable.value = storeCompanyListEvent.itemsCompanyEvent
    }
    function onTabModelValueUpdate(data) {
        console.log('onTabModelValueUpdated', data)
    }
    function onUpdateSearchModelValue(data) {
        searchPayload = data
    }
</script>

<template>
    <div class="gui-bottom">
        <AppCol class="d-flex justify-content-between mb-2">
            <AppBtn variant="success" label="Ajout" @click="ajoute">
                <Fa icon="plus"/>
                Ajouter
            </AppBtn>
        </AppCol>
        <AppRow>
            <AppCol>
                <AppCardableTable
                    :current-page="storeCompanyListEvent.currentPage"
                    :fields="tabFields"
                    :first-page="storeCompanyListEvent.firstPage"
                    :items="itemsTable"
                    :last-page="storeCompanyListEvent.lastPage"
                    :min="AddForm"
                    :next-page="storeCompanyListEvent.nextPage"
                    :pag="storeCompanyListEvent.pagination"
                    :previous-page="storeCompanyListEvent.previousPage"
                    :user="roleuser"
                    form="formCompanyEvenement"
                    @update="update"
                    @update:model-value="onTabModelValueUpdate"
                    @deleted="onDelete"
                    @get-page="getPage"
                    @trier-alphabet="trierAlphabet"
                    @search="search"
                    @update:search-model-value="onUpdateSearchModelValue"
                    @cancel-search="cancelSearch"/>
            </AppCol>
            <AppCol v-if="AddForm" class="col-7">
                <InlistAddForm
                    id="addCompanyEvent"
                    api-method="POST"
                    api-url="/api/company-events"
                    card-title="Créer un nouvel évènement"
                    form="addFormCompanyEvent"
                    :model-value="addFormData"
                    :fields="addFormFields"
                    @submitted="onAddFormSubmitted"
                    @cancel="onAddCancel"/>
            </AppCol>
            <AppCol v-if="UpdateForm" class="col-7">
                <InlistAddForm
                    id="updateCompanyEvent"
                    api-method="PATCH"
                    api-url="/api/company-events"
                    card-title="Modifier un évènement"
                    form="updateFormCompanyEvent"
                    :model-value="updateFormData"
                    :fields="updateFormFields"
                    @submitted="onUpdateFormSubmitted"
                    @cancel="onUpdateCancel"/>
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
