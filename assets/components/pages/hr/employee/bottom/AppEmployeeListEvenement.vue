<script setup>
    import {computed, ref} from 'vue'
    import api from '../../../../../api'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import {useEmployeeListEvenementStore} from '../../../../../stores/hr/employee/employeeListEvenement'
    import {useRoute} from 'vue-router'
    import {getOptions} from '../../../../../utils'
    import AppSuspense from '../../../../AppSuspense.vue'
    import InlistAddForm from '../../../../form-cardable/inlist-add-form/InlistAddForm.vue'

    const roleuser = ref('reader')
    const updated = ref(false)
    const AddForm = ref(false)
    const addFormField = ref([])
    const updateFormField = ref([])
    const sortable = ref(false)
    const filter = ref(false)
    let trierAlpha = {}

    const maRoute = useRoute()
    const employeeId = maRoute.params.id_employee
    const updateEmployeeEventItem = ref({})

    const eventTypes = (await api('/api/event-types', 'GET'))['hydra:member']
    const eventTypesOptions = getOptions(eventTypes, 'name')
    const storeEmployeeListEvenement = useEmployeeListEvenementStore()
    const employeeEventFetchCriteria = useFetchCriteria('employeeEvents')
    const addEmployeeEventItem = ref({})
    addEmployeeEventItem.value = {
        employee: `/api/employees/${employeeId}`
    }
    function resetFilters() {
        employeeEventFetchCriteria.resetAllFilter()
        employeeEventFetchCriteria.addFilter('employee', `/api/employees/${employeeId}`)
    }
    resetFilters()
    await storeEmployeeListEvenement.fetch(employeeEventFetchCriteria.getFetchCriteria)
    const itemsTable = ref([])
    itemsTable.value = storeEmployeeListEvenement.itemsEmployeeEvenement

    addFormField.value = [
        {
            filter: true,
            label: 'Date',
            name: 'date',
            sort: false,
            type: 'date',
            min: true
        },
        {
            filter: true,
            label: 'Type Evènement',
            name: 'type',
            sort: false,
            type: 'select',
            options: eventTypesOptions,
            min: true
        },
        {
            filter: true,
            label: 'Description',
            name: 'name',
            sort: false,
            type: 'text',
            min: true
        }
    ]
    updateFormField.value = addFormField.value

    const tabFields = [
        {
            filter: true,
            label: 'Date',
            name: 'date',
            sort: false,
            type: 'date',
            min: true
        },
        {
            filter: true,
            label: 'Type Evènement',
            name: 'type',
            sort: false,
            type: 'select',
            options: eventTypesOptions,
            min: true
        },
        {
            filter: true,
            label: 'Description',
            name: 'name',
            sort: false,
            type: 'text',
            min: true
        }
    ]

    function ajoute(){
        AddForm.value = true
        updated.value = false
    }

    function update(item) {
        updateEmployeeEventItem.value = item
        updated.value = true
        AddForm.value = false
    }

    async function deleted(id){
        await storeEmployeeListEvenement.deleted(id)
        await storeEmployeeListEvenement.fetch(employeeEventFetchCriteria.getFetchCriteria)
        itemsTable.value = storeEmployeeListEvenement.itemsEmployeeEvenement
    }
    async function getPage(nPage){
        await storeEmployeeListEvenement.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeEmployeeListEvenement.itemsEmployeeEvenement]
    }
    async function trierAlphabet(payload) {
        await storeEmployeeListEvenement.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }
    async function search(inputValues) {
        resetFilters()
        if (inputValues.date) employeeEventFetchCriteria.addFilter('date', inputValues.date, 'date')
        if (inputValues.type) employeeEventFetchCriteria.addFilter('type', inputValues.type)
        if (inputValues.name) employeeEventFetchCriteria.addFilter('name', inputValues.name)
        await storeEmployeeListEvenement.fetch(employeeEventFetchCriteria.getFetchCriteria)
        itemsTable.value = storeEmployeeListEvenement.itemsEmployeeEvenement
    }
    async function cancelSearch() {
        resetFilters()
        await storeEmployeeListEvenement.fetch(employeeEventFetchCriteria.getFetchCriteria)
        itemsTable.value = storeEmployeeListEvenement.itemsEmployeeEvenement
    }
    const col1 = computed(() => {
        if (AddForm.value || updated.value) return 5
        return 12
    })
    function cancelAddForm() {
        AddForm.value = false
    }
    async function onAddEmployeeEventSubmit() {
        AddForm.value = false
        await storeEmployeeListEvenement.fetch(employeeEventFetchCriteria.getFetchCriteria)
        itemsTable.value = storeEmployeeListEvenement.itemsEmployeeEvenement
    }
    async function onUpdateEmployeeEventSubmit() {
        updated.value = false
        await storeEmployeeListEvenement.fetch(employeeEventFetchCriteria.getFetchCriteria)
        itemsTable.value = storeEmployeeListEvenement.itemsEmployeeEvenement
    }
    function cancelUpdateForm() {
        updated.value = false
    }
</script>

<template>
    <div class="gui-bottom">
        <AppRow>
            <AppCol class="d-flex justify-content-between mb-2">
                <span style="margin-left: 10px;">
                    <AppBtn variant="success" label="Ajout" @click="ajoute">
                        <Fa icon="plus"/>
                        Ajouter une nouvel évènement employé
                    </AppBtn>
                </span>
            </AppCol>
        </AppRow>
        <AppRow>
            <AppCol :cols="col1">
                <AppCardableTable
                    :current-page="storeEmployeeListEvenement.currentPage"
                    :fields="tabFields"
                    :first-page="storeEmployeeListEvenement.firstPage"
                    :items="itemsTable"
                    :last-page="storeEmployeeListEvenement.lastPage"
                    :min="AddForm"
                    :next-page="storeEmployeeListEvenement.nextPage"
                    :pag="storeEmployeeListEvenement.pagination"
                    :previous-page="storeEmployeeListEvenement.previousPage"
                    :user="roleuser"
                    form="formEmployeeEvenementCardableTable"
                    @update="update"
                    @deleted="deleted"
                    @get-page="getPage"
                    @trier-alphabet="trierAlphabet"
                    @search="search"
                    @cancel-search="cancelSearch"/>
            </AppCol>
            <AppCol :cols="12 - col1">
                <AppRow>
                    <AppSuspense>
                        <InlistAddForm v-if="AddForm && !updated" id="addEmployeeEvent" api-method="POST" api-url="/api/employee-events" form="addEmployeeEventForm" :fields="addFormField" :model-value="addEmployeeEventItem" @cancel="cancelAddForm" @submitted="onAddEmployeeEventSubmit"/>
                        <InlistAddForm v-if="updated && !AddForm" id="updateEmployeeEvent" api-method="PATCH" api-url="" card-title="Modifier l'évènement employé" form="updateEmployeeEventForm" :fields="updateFormField" :model-value="updateEmployeeEventItem" @cancel="cancelUpdateForm" @submitted="onUpdateEmployeeEventSubmit"/>
                    </AppSuspense>
                </AppRow>
            </AppCol>
        </AppRow>
    </div>
</template>

<style scoped>
    .gui-bottom {
        overflow: hidden;
    }
</style>
