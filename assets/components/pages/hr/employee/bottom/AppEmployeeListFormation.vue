<script setup>
    import {onBeforeMount, computed, ref} from 'vue'
    import api from '../../../../../api'
    import {useEmployeeListFormationStore} from '../../../../../stores/hr/employee/employeeListFormation'
    import {useRoute} from 'vue-router'
    import AppSuspense from '../../../../AppSuspense.vue'
    import InlistAddForm from '../../../../form-cardable/inlist-add-form/InlistAddForm.vue'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'

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
    const employeeId = maRoute.params.id_employee

    const employeesListFormationFetchCriteria = useFetchCriteria('employeesListFormation')
    employeesListFormationFetchCriteria.addFilter('employee', `/api/employees/${employeeId}`)
    const storeEmployeeListFormation = useEmployeeListFormationStore()
    //storeEmployeeListFormation.setIdEmployee(employeeId)
    await storeEmployeeListFormation.fetch(employeesListFormationFetchCriteria.getFetchCriteria)
    const itemsTable = ref([])
    itemsTable.value = storeEmployeeListFormation.itemsEmployeeFormation
    const formData = ref({
        date: null, dateCloture: null, rappel: null, competence: null, groupeMachine: null, machine: null, niveau: null, commentaire: null, formateurInt: null, formateurExt: null
    })
    //region chargement des listes pour les selects
    function getOptions(dataColl, textProperty, valueProperty = '@id') {
        return {
            label: value => {
                const filteredColl = dataColl.find(item => item[valueProperty] === value)
                if (typeof filteredColl === 'undefined') return '<null>'
                if (typeof filteredColl[textProperty] === 'undefined') return `Property ${textProperty} not found`
                return filteredColl[textProperty]
            },
            options: dataColl.map(item => ({text: item[textProperty], value: item['@id']}))
        }
    }
    const isLoaded = ref(false)
    const addFormField = ref([])
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
                    step: 0.5
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

    function ajoute(){
        AddForm.value = true
        updated.value = false
        // const itemsNull = {
        //     client: null,
        //     reference: null,
        //     quantiteConfirmee: null,
        //     quantiteSouhaitee: null,
        //     quantiteEffetctuee: null,
        //     dateLivraison: null,
        //     dateLivraisonSouhaitee: null
        // }
        // formData.value = itemsNull
    }

    // async function ajoutEmployeeFormation(){
    //     // const form = document.getElementById('addEmployeeFormation')
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
    //         quantiteEffetctuee: formData.value.quantiteEffetctuee,
    //         dateLivraison: formData.value.dateLivraison,
    //         dateLivraisonSouhaitee: formData.value.dateLivraisonSouhaitee
    //     }
    //     violations = await storeEmployeeListFormation.addEmployeeFormation(itemsAddData)

    //     if (violations.length > 0){
    //         isPopupVisible.value = true
    //     } else {
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //         itemsTable.value = [...storeEmployeeListFormation.itemsEmployeeFormation]
    //     }
    // }

    function update(item) {
        updated.value = true
        AddForm.value = true
        const itemsData = {
            date: item.date,
            dateCloture: item.dateCloture,
            rappel: item.rappel,
            competence: item.competence,
            groupeMachine: item.groupeMachine,
            machine: item.machine,
            niveau: item.niveau,
            commentaire: item.commentaire,
            formateurInt: item.formateurInt,
            formateurExt: item.formateurExt
        }
        formData.value = itemsData
    }

    async function deleted(id){
        await storeEmployeeListFormation.deleted(id)
        itemsTable.value = [...storeEmployeeListFormation.itemsEmployeeFormation]
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
        let mac = ''
        if (inputValues.machine) {
            mac = inputValues.machine.trim().split(' ')
            if (mac.length === 2){
                mac = {name: mac[0], surname: mac[1], same: false}
            }
            if (mac.length === 1) {
                mac = {name: mac[0], surname: mac[0], same: true}
            }
        }
        // let gpMachine = inputValues.comptence.trim().split(' ')
        // if (gpMachine.length === 2){
        //     gpMachine = {name: gpMachine[0], surname: gpMachine[1]}
        // }
        // if (gpMachine.length === 1) {
        //     gpMachine = {name: gpMachine[0], surname: gpMachine[0]}
        // }

        let formExt = ''
        if (inputValues.formateurExt){
            formExt = inputValues.formateurExt.trim().split(' ')
            if (formExt.length === 2){
                formExt = {name: formExt[0], surname: formExt[1], same: false}
            }
            if (formExt.length === 1) {
                formExt = {name: formExt[0], surname: formExt[0], same: true}
            }
        }

        let formInt = ''
        if (inputValues.formateurInt){
            formInt = inputValues.formateurInt.trim().split(' ')
            if (formInt.length === 2){
                formInt = {name: formInt[0], surname: formInt[1], same: false}
            }
            if (formInt.length === 1) {
                formInt = {name: formInt[0], surname: formInt[0], same: true}
            }
        }

        const payload = {
            date: inputValues.date ?? '',
            dateCloture: inputValues.dateCloture ?? '',
            rappel: inputValues.rappel ?? '',
            competence: inputValues.competence ?? '',
            groupeMachine: inputValues.groupeMachine ?? '',
            machine: mac,
            niveau: inputValues.niveau ?? '',
            commentaire: inputValues.commentaire ?? '',
            formateurInt: formInt ?? '',
            formateurExt: formExt ?? ''
        }

        await storeEmployeeListFormation.filterBy(payload)
        itemsTable.value = [...storeEmployeeListFormation.itemsEmployeeFormation]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        storeEmployeeListFormation.fetch()
    }
    function cancelAddForm() {
        AddForm.value = false
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
                    v-if="isLoaded"
                    :current-page="storeEmployeeListFormation.currentPage"
                    :fields="tabFields"
                    :first-page="storeEmployeeListFormation.firstPage"
                    :items="itemsTable"
                    :last-page="storeEmployeeListFormation.lastPage"
                    :min="AddForm"
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
            <AppCol v-show="AddForm && !updated" class="col-7">
                <AppSuspense>
                    <InlistAddForm v-if="isLoaded" id="addEmployeeSkill" api-method="POST" api-url="" form="addEmployeeSkillForm" :fields="addFormField" @cancel="cancelAddForm"/>
                </AppSuspense>
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
