<script setup>
    import {onBeforeMount, computed, ref} from 'vue'
    import api from '../../../../../api'
    import {useEmployeeListFormationStore} from '../../../../../stores/hr/employee/employeeListFormation'
    import {useRoute} from 'vue-router'
    import AppSuspense from '../../../../AppSuspense.vue'
    import InlistAddForm from '../../../../form-cardable/inlist-add-form/InlistAddForm.vue'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import {getOptions} from '../../../../../utils'

    const roleuser = ref('reader')
    // let violations = []
    const updated = ref(false)
    const AddForm = ref(false)
    // const isPopupVisible = ref(false)
    const sortable = ref(false)
    const filter = ref(false)
    let trierAlpha = {}

    const maRoute = useRoute()
    const employeeId = maRoute.params.id_employee

    const employeesListFormationFetchCriteria = useFetchCriteria('employeesListFormation')
    function resetFilter() {
        employeesListFormationFetchCriteria.resetAllFilter()
        employeesListFormationFetchCriteria.addFilter('employee', `/api/employees/${employeeId}`)
    }
    resetFilter()
    const storeEmployeeListFormation = useEmployeeListFormationStore()
    //storeEmployeeListFormation.setIdEmployee(employeeId)
    await storeEmployeeListFormation.fetch(employeesListFormationFetchCriteria.getFetchCriteria)
    const itemsTable = ref([])
    itemsTable.value = storeEmployeeListFormation.itemsEmployeeFormation
    const updateSkillItem = ref({})
    //region chargement des listes pour les selects
    const isLoaded = ref(false)
    const addFormField = ref([])
    const updateFormField = ref([])
    const tabFields = ref([])
    const engineGroupsOptions = ref({})
    const skillsOptions = ref({})
    const manufacturerEnginesOptions = ref({})
    const employeesOptions = ref({})
    const outTrainersOptions = ref([])
    const productsOptions = ref([])
    onBeforeMount(() => {
        console.log('Début chargement', new Date())
        const promiseEngineGroups = new Promise((resolve, reject) => {
            try {
                const response = api('/api/engine-groups?pagination=false', 'GET')
                response.then(result => {
                    engineGroupsOptions.value = getOptions(result['hydra:member'], 'name')
                    resolve('promiseEngineGroups ok')
                })
            } catch (e) {
                console.log('error', e)
                reject(e)
            }
        })
        const promiseSkillTypes = new Promise((resolve, reject) => {
            try {
                const response = api('/api/skill-types?pagination=false', 'GET')
                response.then(result => {
                    skillsOptions.value = getOptions(result['hydra:member'], 'name')
                    resolve('promiseSkillTypes ok')
                })
            } catch (e) {
                console.log('error', e)
                reject(e)
            }
        })
        const promiseManufacturerEngines = new Promise((resolve, reject) => {
            try {
                const response = api('/api/manufacturer-engines?pagination=false', 'GET')
                response.then(result => {
                    manufacturerEnginesOptions.value = getOptions(result['hydra:member'], 'code')
                    resolve('promiseManufacturerEngines ok')
                })
            } catch (e) {
                console.log('error', e)
                reject(e)
            }
        })
        const promiseEmployees = new Promise((resolve, reject) => {
            try {
                const response = api('/api/employees?pagination=false', 'GET')
                response.then(result => {
                    employeesOptions.value = getOptions(result['hydra:member'], 'username')
                    resolve('promiseEmployees ok')
                })
            } catch (e) {
                console.log('error', e)
                reject(e)
            }
        })
        const promiseOutTrainers = new Promise((resolve, reject) => {
            try {
                const response = api('/api/out-trainers?pagination=false', 'GET')
                response.then(result => {
                    outTrainersOptions.value = getOptions(result['hydra:member'], 'name')
                    resolve('promiseOutTrainers ok')
                })
            } catch (e) {
                console.log('error', e)
                reject(e)
            }
        })
        const promiseProducts = new Promise((resolve, reject) => {
            try {
                const response = api('/api/products?pagination=false', 'GET')
                response.then(result => {
                    productsOptions.value = getOptions(result['hydra:member'], 'code')
                    resolve('promiseProducts ok')
                })
            } catch (e) {
                console.log('error', e)
                reject(e)
            }
        })
        const promises = [promiseEngineGroups, promiseSkillTypes, promiseManufacturerEngines, promiseEmployees, promiseOutTrainers, promiseProducts]
        // eslint-disable-next-line array-callback-return
        Promise.allSettled(promises).then(results => {
            console.log('Chargement terminé', new Date(), results)
            addFormField.value = [
                {
                    label: 'Date',
                    name: 'startedDate',
                    type: 'date'
                },
                {
                    label: 'Date clôture',
                    name: 'endedDate',
                    type: 'date'
                },
                {
                    label: 'Date de rappel',
                    name: 'remindedDate',
                    type: 'date'
                },
                {
                    label: 'Compétence',
                    name: 'type',
                    options: skillsOptions.value,
                    type: 'select'
                },
                {
                    label: 'Niveau',
                    name: 'level',
                    type: 'number',
                    step: 1
                },
                {
                    label: 'Groupe de machine',
                    name: 'family',
                    type: 'select',
                    options: engineGroupsOptions.value
                },
                {
                    label: 'Machine',
                    name: 'engine',
                    type: 'select',
                    options: manufacturerEnginesOptions.value
                },
                {
                    label: 'Produit',
                    name: 'product',
                    type: 'select',
                    options: productsOptions.value
                },
                {
                    label: 'Formateur Interne',
                    name: 'inTrainer',
                    type: 'select',
                    options: employeesOptions.value
                },
                {
                    label: 'Formateur Externe',
                    name: 'outTrainer',
                    type: 'select',
                    options: outTrainersOptions.value
                }
            ]
            updateFormField.value = addFormField.value
            tabFields.value = [
                {
                    label: 'Date',
                    name: 'startedDate',
                    type: 'date',
                    min: true
                },
                {
                    label: 'Date clôture',
                    name: 'endedDate',
                    type: 'date',
                    min: true
                },
                {
                    label: 'Date de rappel',
                    name: 'remindedDate',
                    type: 'date',
                    min: false
                },
                {
                    label: 'Compétence',
                    name: 'type',
                    type: 'select',
                    options: skillsOptions.value,
                    min: true
                },
                {
                    label: 'Niveau',
                    name: 'level',
                    type: 'number',
                    min: true
                },
                {
                    label: 'Groupe de machine',
                    name: 'family',
                    type: 'select',
                    options: engineGroupsOptions.value,
                    min: false
                },
                {
                    label: 'Machine',
                    name: 'engine',
                    type: 'select',
                    options: manufacturerEnginesOptions.value,
                    min: false
                },
                {
                    label: 'Produit',
                    name: 'product',
                    type: 'select',
                    options: productsOptions.value,
                    min: false
                },
                {
                    label: 'Formateur Interne',
                    name: 'inTrainer',
                    type: 'select',
                    options: employeesOptions.value,
                    min: false
                },
                {
                    label: 'Formateur Externe',
                    name: 'outTrainer',
                    type: 'select',
                    options: outTrainersOptions.value,
                    min: false
                }
            ]
            isLoaded.value = true
        })
    })
    //endregion

    const addSkillItem = ref({})
    addSkillItem.value = {
        employee: `/api/employees/${employeeId}`
    }
    function ajoute(){
        AddForm.value = true
        updated.value = false
    }

    function update(item) {
        updateSkillItem.value = item
        updated.value = true
        AddForm.value = false
    }

    async function deleted(id){
        await storeEmployeeListFormation.deleted(id)
        await storeEmployeeListFormation.fetch(employeesListFormationFetchCriteria.getFetchCriteria)
        itemsTable.value = storeEmployeeListFormation.itemsEmployeeFormation
    }
    async function getPage(nPage){
        await storeEmployeeListFormation.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeEmployeeListFormation.itemsEmployeeFormation]
    }
    async function trierAlphabet(payload) {
        await storeEmployeeListFormation.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }
    async function search(inputValues) {
        console.log('search', inputValues)
        resetFilter()
        if (inputValues.startedDate) employeesListFormationFetchCriteria.addFilter('startedDate', inputValues.startedDate, 'date')
        if (inputValues.endedDate) employeesListFormationFetchCriteria.addFilter('endedDate', inputValues.endedDate, 'date')
        if (inputValues.remindedDate) employeesListFormationFetchCriteria.addFilter('remindedDate', inputValues.remindedDate, 'date')
        if (inputValues.type) employeesListFormationFetchCriteria.addFilter('type', inputValues.type)
        if (inputValues.level) employeesListFormationFetchCriteria.addFilter('level', inputValues.level)
        if (inputValues.family) employeesListFormationFetchCriteria.addFilter('family', inputValues.family)
        if (inputValues.engine) employeesListFormationFetchCriteria.addFilter('engine', inputValues.engine)
        if (inputValues.product) employeesListFormationFetchCriteria.addFilter('product', inputValues.product)
        if (inputValues.inTrainer) employeesListFormationFetchCriteria.addFilter('inTrainer', inputValues.inTrainer)
        if (inputValues.outTrainer) employeesListFormationFetchCriteria.addFilter('outTrainer', inputValues.outTrainer)
        await storeEmployeeListFormation.fetch(employeesListFormationFetchCriteria.getFetchCriteria)
        itemsTable.value = storeEmployeeListFormation.itemsEmployeeFormation
    }
    async function cancelSearch() {
        resetFilter()
        await storeEmployeeListFormation.fetch(employeesListFormationFetchCriteria.getFetchCriteria)
        itemsTable.value = storeEmployeeListFormation.itemsEmployeeFormation
    }
    function cancelAddForm() {
        AddForm.value = false
    }
    function cancelUpdateForm() {
        updated.value = false
    }
    async function onAddSkillSubmit() {
        AddForm.value = false
        await storeEmployeeListFormation.fetch(employeesListFormationFetchCriteria.getFetchCriteria)
        itemsTable.value = storeEmployeeListFormation.itemsEmployeeFormation
    }
    async function onUpdateSkillSubmit() {
        updated.value = false
        await storeEmployeeListFormation.fetch(employeesListFormationFetchCriteria.getFetchCriteria)
        itemsTable.value = storeEmployeeListFormation.itemsEmployeeFormation
    }
    const col1 = computed(() => {
        if (AddForm.value || updated.value) return 5
        return 12
    })
</script>

<template>
    <div class="gui-bottom">
        <AppRow>
            <AppCol class="d-flex justify-content-between mb-2">
                <span class="ml-10">
                    <AppBtn variant="success" label="Ajout" @click="ajoute">
                        <Fa icon="plus"/>
                        Ajouter une nouvelle formation
                    </AppBtn>
                </span>
            </AppCol>
        </AppRow>
        <AppRow>
            <AppCol :cols="col1">
                <AppCardableTable
                    v-if="isLoaded"
                    :current-page="storeEmployeeListFormation.currentPage"
                    :fields="tabFields"
                    :first-page="storeEmployeeListFormation.firstPage"
                    :items="itemsTable"
                    :last-page="storeEmployeeListFormation.lastPage"
                    :min="AddForm || updated"
                    :next-page="storeEmployeeListFormation.nextPage"
                    :pag="storeEmployeeListFormation.pagination"
                    :previous-page="storeEmployeeListFormation.previousPage"
                    :user="roleuser"
                    form="formEmployeeFormationCardableTable"
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
                        <InlistAddForm v-if="isLoaded && AddForm && !updated" id="addEmployeeSkill" api-method="POST" api-url="/api/skills" form="addEmployeeSkillForm" :fields="addFormField" :model-value="addSkillItem" @cancel="cancelAddForm" @submitted="onAddSkillSubmit"/>
                        <InlistAddForm v-if="isLoaded && updated && !AddForm" id="updateEmployeeSkill" api-method="PATCH" api-url="" card-title="Modifier la compétence" form="updateEmployeeSkillForm" :fields="updateFormField" :model-value="updateSkillItem" @cancel="cancelUpdateForm" @submitted="onUpdateSkillSubmit"/>
                    </AppSuspense>
                </AppRow>
            </AppCol>
        </AppRow>
    </div>
</template>

<style scoped>
    .btn-float-right{
        float: right;
    }
    .ml-10 {
        margin-left: 10px;
    }
    .gui-bottom {
        overflow: hidden;
    }
</style>
