<script setup>
    import {computed, ref} from 'vue-demi'
    import AppEmployeeCreate from './AppEmployeeCreate.vue'
    import useFetchCriteria from '../../../../stores/fetch-criteria/fetchCriteria'
    import {useEmployeesStore} from '../../../../stores/employee/employees'
    import useUser from '../../../../stores/security'
    import {useRouter} from 'vue-router'
    import {onMounted, onUnmounted} from 'vue'
    import {Modal} from 'bootstrap'

    defineProps({
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })

    const router = useRouter()
    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)
    const tableKey = ref(0)
    const createModalRef = ref(null)
    const creationSuccess = ref(false)

    const fetchUser = useUser()
    const currentCompany = fetchUser.company
    const isHrWriterOrAdmin = fetchUser.isHrWriter || fetchUser.isHrAdmin
    const roleuser = ref(isHrWriterOrAdmin ? 'writer' : 'reader')

    const storeEmployeesList = useEmployeesStore()
    await storeEmployeesList.fetch()

    const employeeListCriteria = useFetchCriteria('employee-list-criteria')
    employeeListCriteria.addFilter('company', currentCompany)

    async function refreshTable() {
        await storeEmployeesList.fetch(employeeListCriteria.getFetchCriteria)
    }
    await refreshTable()

    const itemsTable = computed(() => storeEmployeesList.itemsEmployees)

    const optionsEtat = [
        {text: 'agreed', value: 'agreed'},
        {text: 'warning', value: 'warning'}
    ]

    const fields = computed(() => [
        {
            label: 'Img',
            name: 'filePath',
            trie: false,
            type: 'img',
            width: 100,
            filter: false
        },
        {label: 'N° Badge', name: 'timeCard', trie: true, type: 'text', width: 80},
        {label: 'Nom', name: 'surname', trie: true, type: 'text'},
        {label: 'prenom', name: 'name', trie: true, type: 'text'},
        {label: 'Initiales', name: 'initials', trie: true, type: 'text', width: 80},
        {label: 'Identifiant', name: 'username', trie: true, type: 'text', width: 80},
        {label: 'Compte utilisateur', name: 'userEnabled', trie: false, type: 'boolean', width: 80},
        {
            label: 'Etat',
            name: 'state',
            options: {
                label: value =>
                    optionsEtat.find(option => option.type === value)?.text ?? null,
                options: optionsEtat
            },
            trie: false,
            type: 'select',
            width: 80
        }
    ])

    async function deleted(id){
        await storeEmployeesList.remove(id)
        await refreshTable()
    }
    async function getPage(nPage){
        employeeListCriteria.gotoPage(parseFloat(nPage))
        await storeEmployeesList.fetch(employeeListCriteria.getFetchCriteria)
    }

    async function search(inputValues) {
        employeeListCriteria.resetAllFilter()
        // console.log('search', inputValues)
        employeeListCriteria.addFilter('company', currentCompany)
        if (inputValues.timeCard) employeeListCriteria.addFilter('timeCard', inputValues.timeCard)
        if (inputValues.surname) employeeListCriteria.addFilter('surname', inputValues.surname)
        if (inputValues.name) employeeListCriteria.addFilter('name', inputValues.name)
        if (inputValues.initials) employeeListCriteria.addFilter('initials', inputValues.initials)
        if (inputValues.username) employeeListCriteria.addFilter('username', inputValues.username)
        if (typeof inputValues.userEnabled !== 'undefined') employeeListCriteria.addFilter('userEnabled', inputValues.userEnabled)
        if (inputValues.state) employeeListCriteria.addFilter('embState.state[]', inputValues.state)
        await storeEmployeesList.fetch(employeeListCriteria.getFetchCriteria)
    }
    async function cancelSearch() {
        employeeListCriteria.resetAllFilter()
        employeeListCriteria.resetSort()
        employeeListCriteria.addFilter('company', currentCompany)
        await storeEmployeesList.fetch(employeeListCriteria.getFetchCriteria)
    }

    async function trierAlphabet(payload) {
        employeeListCriteria.addSort(payload.name, payload.direction)
        await storeEmployeesList.fetch(employeeListCriteria.getFetchCriteria)
    }
    async function onEmployeeCreated() {
        await refreshTable()
        if (createModalRef.value) {
            const modalElement = createModalRef.value.$el
            const bootstrapModal = Modal.getInstance(modalElement)
            bootstrapModal.hide()
            creationSuccess.value = true
            setTimeout(() => {
                creationSuccess.value = false
            }, 3000)
        }
    }
    function onEmployeeShow(item) {
        console.log('onEmployeeShow', item)
        /* eslint-disable camelcase */
        router.push({name: 'employee', params: {id_employee: item.id}})
    }

    onMounted(() => {
        storeEmployeesList.fetch(employeeListCriteria.getFetchCriteria).then(() => {
            tableKey.value += 1
        })
    })
    onUnmounted(() => {
        storeEmployeesList.reset()
        employeeListCriteria.reset()
    })
</script>

<template>
    <div class="row">
        <div class="col">
            <h1>
                <Fa :icon="icon"/>
                {{ title }}
                <span v-if="isHrWriterOrAdmin" class="btn-float-right">
                    <AppBtn
                        variant="success"
                        label="Créer"
                        data-bs-toggle="modal"
                        :data-bs-target="target">
                        <Fa icon="plus"/>
                        Créer
                    </AppBtn>
                </span>
            </h1>
        </div>
    </div>
    <div v-if="creationSuccess" class="row d-flex">
        <div class="bg-success text-white text-center">
            Employé bien créé
        </div>
    </div>
    <div class="row">
        <AppEmployeeCreate
            ref="createModalRef"
            :current-company="currentCompany"
            :modal-id="modalId" title="Ajouter un nouvel employé"
            :target="target" @created="onEmployeeCreated"/>
        <div class="col">
            <AppSuspense>
                <AppCardableTable
                    :key="tableKey"
                    :current-page="storeEmployeesList.currentPage"
                    :fields="fields"
                    :first-page="storeEmployeesList.firstPage"
                    :items="itemsTable"
                    :last-page="storeEmployeesList.lastPage"
                    :next-page="storeEmployeesList.nextPage"
                    :pag="storeEmployeesList.pagination"
                    :previous-page="storeEmployeesList.previousPage"
                    :user="roleuser"
                    form="formEmployeeCardableTable"
                    @deleted="deleted"
                    @get-page="getPage"
                    @trier-alphabet="trierAlphabet"
                    @search="search"
                    @update="onEmployeeShow"
                    @cancel-search="cancelSearch"/>
            </AppSuspense>
        </div>
    </div>
</template>

<style scoped>
    .btn-float-right{
        float: right;
    }
</style>
