<script setup>
    import {computed, ref} from 'vue'
    import {useEmployeeListClockingStore} from '../../../../../stores/hr/employee/employeeListClocking'
    import {useRoute} from 'vue-router'
    import AppSuspense from '../../../../AppSuspense.vue'
    import InlistAddForm from '../../../../form-cardable/inlist-add-form/InlistAddForm.vue'

    const roleuser = ref('reader')
    const updated = ref(false)
    const AddForm = ref(false)
    const sortable = ref(false)
    const filter = ref(false)
    let trierAlpha = {}
    let filterBy = {}

    const maRoute = useRoute()
    const employeeId = maRoute.params.id_employee
    const updateEmployeeEventItem = ref({})

    const storeEmployeeListClocking = useEmployeeListClockingStore()
    storeEmployeeListClocking.setIdEmployee(employeeId)
    await storeEmployeeListClocking.fetch()
    const itemsTable = ref(storeEmployeeListClocking.itemsEmployeeClocking)

    const addEmployeeClockingItem = ref({})
    addEmployeeClockingItem.value = {
        employee: `/api/employees/${employeeId}`
    }
    const addFormField = [
        {
            label: 'Date et heure du pointage',
            name: 'date',
            type: 'datetime-local'
        }
    ]
    const updateFormField = [
        {
            label: 'Date et heure du pointage',
            name: 'date',
            type: 'datetime-local'
        },
        {
            label: 'Etat',
            name: 'enter',
            type: 'boolean'
        }
    ]
    const tabFields = [
        {
            filter: true,
            label: 'Date de création',
            name: 'creationDate',
            sort: false,
            type: 'datetime-local',
            min: true
        },
        {
            filter: true,
            label: 'Date et heure du pointage',
            name: 'date',
            sort: false,
            type: 'datetime-local',
            min: true
        },
        {
            filter: true,
            label: 'Entrée',
            name: 'enter',
            sort: false,
            type: 'boolean',
            min: true
        }
    ]

    function ajoute(){
        AddForm.value = true
        updated.value = false
    }
    function annule(){
        AddForm.value = false
        updated.value = false
    }
    function formatDateToIso(date) {
        return new Date(date).toISOString().substring(0, 19)
    }
    function update(item) {
        updateEmployeeEventItem.value = item
        if (updateEmployeeEventItem.value.date && updateEmployeeEventItem.value.date !== ' ') updateEmployeeEventItem.value.date = formatDateToIso(updateEmployeeEventItem.value.date)
        if (updateEmployeeEventItem.value.creationDate && updateEmployeeEventItem.value.creationDate !== ' ') updateEmployeeEventItem.value.creationDate = formatDateToIso(updateEmployeeEventItem.value.creationDate)
        updated.value = true
        AddForm.value = false
    }

    async function deleted(id){
        await storeEmployeeListClocking.deleted(id)
        itemsTable.value = [...storeEmployeeListClocking.itemsEmployeeClocking]
    }
    async function getPage(nPage){
        await storeEmployeeListClocking.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeEmployeeListClocking.itemsEmployeeClocking]
    }
    async function trierAlphabet(payload) {
        await storeEmployeeListClocking.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }
    async function search(inputValues) {
        const payload = {
            creationDate: inputValues.creationDate ?? '',
            date: inputValues.date ?? '',
            enter: inputValues.enter ?? ''
        }
        await storeEmployeeListClocking.filterBy(payload)
        itemsTable.value = [...storeEmployeeListClocking.itemsEmployeeClocking]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        await storeEmployeeListClocking.fetch()
        itemsTable.value = storeEmployeeListClocking.itemsEmployeeClocking
    }
    const col1 = computed(() => {
        if (AddForm.value || updated.value) return 5
        return 12
    })
    async function onAddEmployeeClockingSubmit() {
        AddForm.value = false
        await storeEmployeeListClocking.fetch()
        itemsTable.value = storeEmployeeListClocking.itemsEmployeeClocking
    }
    async function onUpdateEmployeeClockingSubmit() {
        updated.value = false
        await storeEmployeeListClocking.fetch()
        itemsTable.value = storeEmployeeListClocking.itemsEmployeeClocking
    }
</script>

<template>
    <div class="gui-bottom">
        <AppRow>
            <AppCol class="d-flex justify-content-between mb-2">
                <span class="ml-10">
                    <AppBtn variant="success" label="Ajout" @click="ajoute">
                        <Fa icon="plus"/>
                        Ajouter un nouveau pointage
                    </AppBtn>
                </span>
            </AppCol>
        </AppRow>
        <AppRow>
            <AppCol :cols="col1">
                <AppCardableTable
                    :current-page="storeEmployeeListClocking.currentPage"
                    :fields="tabFields"
                    :first-page="storeEmployeeListClocking.firstPage"
                    :items="itemsTable"
                    :last-page="storeEmployeeListClocking.lastPage"
                    :min="AddForm"
                    :next-page="storeEmployeeListClocking.nextPage"
                    :pag="storeEmployeeListClocking.pagination"
                    :previous-page="storeEmployeeListClocking.previousPage"
                    :user="roleuser"
                    form="formEmployeeClockingCardableTable"
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
                        <InlistAddForm v-if="AddForm && !updated" id="addEmployeeClocking" api-method="POST" api-url="/api/clockings/add" form="addEmployeeClockingForm" :fields="addFormField" :model-value="addEmployeeClockingItem" @cancel="annule" @submitted="onAddEmployeeClockingSubmit"/>
                        <InlistAddForm v-if="updated && !AddForm" id="updateEmployeeClocking" api-method="PATCH" api-url="" card-title="Modifier le pointage de l'employé" form="updateEmployeeEventForm" :fields="updateFormField" :model-value="updateEmployeeEventItem" @cancel="annule" @submitted="onUpdateEmployeeClockingSubmit"/>
                    </AppSuspense>
                </AppRow>
            </AppCol>
        </AppRow>
    </div>
</template>

<style scoped>
    .ml-10 {
        margin-left: 10px;
    }
    .gui-bottom {
        overflow: hidden;
    }
</style>
