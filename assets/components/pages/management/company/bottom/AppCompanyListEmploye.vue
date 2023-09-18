<script setup>
    import {computed, ref} from 'vue'
    import {useCompanyListEmployeStore} from '../../../../../stores/company/companyListEmploye'
    import {useRoute, useRouter} from 'vue-router'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import AppRow from '../../../../AppRow'
    import AppCol from '../../../../AppCol'

    const roleuser = ref('reader')
    const sortable = ref(false)
    const filter = ref(false)
    let trierAlpha = {}
    const filterBy = {}

    const router = useRouter()
    //region récupération données d'entrée
    const maRoute = useRoute()
    const companyId = maRoute.params.id_company
    //endregion
    //region récupération initiale de la liste des employées associés à la compagnie
    const storeCompanyListEmploye = useCompanyListEmployeStore()
    const employeeListFetchCriteria = useFetchCriteria('companyEmployeeInListFetchCriteria')
    employeeListFetchCriteria.addFilter('company', `/api/companies/${companyId}`)
    //storeCompanyListEmploye.setIdCompany(companyId)
    await storeCompanyListEmploye.fetch(employeeListFetchCriteria.getFetchCriteria)
    console.log(storeCompanyListEmploye.employees)
    const itemsTable = ref(storeCompanyListEmploye.employees)
    const getId = /.*?\/(\d+)/

    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Login',
            name: 'username',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Initiales',
            name: 'initials',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Prénom',
            name: 'name',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Nom',
            name: 'surname',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Notes',
            name: 'notes',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date d\'arrivée',
            name: 'entryDate',
            sort: true,
            type: 'date',
            update: true
        }
    ]

    function update(item) {
        console.log('update', item)
        //Ouverture de la fiche employée
        const itemId = item['@id'].match(getId)[1]
        const routeData = router.resolve({name: 'employee', params: {'id_employee': itemId}})
        window.open(routeData.href, '_blank')
    }

    async function deleted(id){
        await storeCompanyListEmploye.deleted(id)
        itemsTable.value = [...storeCompanyListEmploye.itemsCompanyEmploye]
    }
    async function getPage(nPage){
        await storeCompanyListEmploye.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeCompanyListEmploye.itemsCompanyEmploye]
    }
    async function trierAlphabet(payload) {
        await storeCompanyListEmploye.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }

    async function search(inputValues) {
        employeeListFetchCriteria.resetAllFilter()
        employeeListFetchCriteria.addFilter('company', `/api/companies/${companyId}`)
        if (inputValues.name) employeeListFetchCriteria.addFilter('name', inputValues.name)
        if (inputValues.surname) employeeListFetchCriteria.addFilter('surname', inputValues.surname)
        if (inputValues.entryDate) employeeListFetchCriteria.addFilter('entryDate', inputValues.entryDate, 'datetime')
        if (inputValues.notes) employeeListFetchCriteria.addFilter('notes', inputValues.notes)
        if (inputValues.username) employeeListFetchCriteria.addFilter('username', inputValues.username)
        if (inputValues.initials) employeeListFetchCriteria.addFilter('initials', inputValues.initials)
        await storeCompanyListEmploye.fetch(employeeListFetchCriteria.getFetchCriteria)
        itemsTable.value = storeCompanyListEmploye.employees
    }
    async function cancelSearch() {
        filter.value = true
        employeeListFetchCriteria.resetAllFilter()
        employeeListFetchCriteria.addFilter('company', `/api/companies/${companyId}`)
        await storeCompanyListEmploye.fetch(employeeListFetchCriteria.getFetchCriteria)
        itemsTable.value = storeCompanyListEmploye.employees
    }
</script>

<template>
    <div class="gui-bottom">
        <AppRow>
            <AppCol class="p-3">
                <AppCardableTable
                    :current-page="storeCompanyListEmploye.currentPage"
                    :fields="tabFields"
                    :first-page="storeCompanyListEmploye.firstPage"
                    :items="itemsTable"
                    :last-page="storeCompanyListEmploye.lastPage"
                    :next-page="storeCompanyListEmploye.nextPage"
                    :pag="storeCompanyListEmploye.pagination"
                    :previous-page="storeCompanyListEmploye.previousPage"
                    :user="roleuser"
                    :should-delete="false"
                    form="formCompanyEmployeCardableTable"
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
    .gui-bottom {
        overflow: hidden;
    }
</style>
