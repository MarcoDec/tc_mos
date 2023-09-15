<script setup>
    import {computed, ref} from 'vue'
    import useFetchCriteria from '../../../../stores/fetch-criteria/fetchCriteria'
    import AppSuspense from '../../../AppSuspense.vue'
    import Fa from '../../../Fa'
    import {useRouter} from 'vue-router'
    import useUser from '../../../../stores/security'
    import {useCompanyStore} from '../../../../stores/management/companies/companies'

    defineProps({
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
    const router = useRouter()
    //region récupération des données utilisateur
    const fetchUser = useUser()
    const currentCompany = fetchUser.company
    const isWriterOrAdmin = fetchUser.isManagementWriter || fetchUser.isManagementAdmin
    const roleuser = ref(isWriterOrAdmin ? 'writer' : 'reader')
    //endregion
    //region récupération de la liste des companies
    const fetchCompanies = useCompanyStore()
    const companyListCriteria = useFetchCriteria('company-list-criteria')
    await fetchCompanies.fetch(companyListCriteria.getFetchCriteria)
    const optionsCompanies = fetchCompanies.companies.map(item => ({
        label: item.name, text: item.name, value: item['@id']
    })).filter(item => item.value !== currentCompany)
    async function refreshTable() {
        await fetchCompanies.fetch(companyListCriteria.getFetchCriteria)
    }
    const itemsTable = computed(() => fetchCompanies.companies)
    //endregion
    //region initialisation des éléments pour le tableau de liste
    const tabFields = [
        {
            label: 'Nom', name: 'name', trie: true, type: 'text'
        },
        {
            label: 'Societe', name: 'society', trie: true, type: 'text'
        },
        {
            label: 'Devise', name: 'currency', trie: true, type: 'text'
        },
        {
            label: 'Horaires de travail', name: 'workTimeTable', trie: true, type: 'text'
        }
    ]
    //endregion

    //region Fonctions relatives à la liste
    function update(item) {
        const getId = /.*?\/(\d+)/
        const itemId = item['@id'].match(getId)[1]
        console.log(item, itemId)
        // eslint-disable-next-line quote-props
        router.push({name: 'company', params: {'id_company': itemId}})
    }
    async function deleteTableItem(id) {
        await fetchCompanies.remove(id)
        await refreshTable()
    }
    async function search(inputValues) {
        companyListCriteria.resetAllFilter()
        if (inputValues.name) companyListCriteria.addFilter('name', inputValues.name)
        if (inputValues.society) companyListCriteria.addFilter('society', inputValues.society)
        if (inputValues.currency) companyListCriteria.addFilter('currency', inputValues.currency)
        if (inputValues.workTimeTable) companyListCriteria.addFilter('workTimeTable', inputValues.workTimeTable)
        await fetchCompanies.fetch(companyListCriteria.getFetchCriteria)
    }
    async function cancelSearch() {
        companyListCriteria.resetAllFilter()
        await fetchCompanies.fetch(companyListCriteria.getFetchCriteria)
    }

    async function getPage(nPage){
        companyListCriteria.gotoPage(nPage)
        await fetchCompanies.fetch(companyListCriteria.getFetchCriteria)
    }
    async function trierAlphabet(payload) {
        companyListCriteria.addSort(payload.name, payload.direction)
        await fetchCompanies.fetch(companyListCriteria.getFetchCriteria)
    }
    //endregion
    function openNewCompanyForm() {
        console.log('Ouverture du formulaire d\'ajout d\'une nouvelle compagnie')
    }
</script>

<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <h1>
                    <Fa :icon="icon"/>
                    {{ title }}
                    <span v-if="isWriterOrAdmin" class="btn-float-right">
                        <AppBtn variant="success" label="Ajout" @click="openNewCompanyForm">
                            <Fa icon="plus"/>
                            Ajouter
                        </AppBtn>
                    </span>
                </h1>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <AppSuspense>
                    <AppCardableTable
                        :current-page="fetchCompanies.currentPage"
                        :fields="tabFields"
                        :first-page="fetchCompanies.firstPage"
                        :items="itemsTable"
                        :last-page="fetchCompanies.lastPage"
                        :min="false"
                        :next-page="fetchCompanies.nextPage"
                        :pag="fetchCompanies.pagination"
                        :previous-page="fetchCompanies.previousPage"
                        :user="roleuser"
                        form="formCompanyCardableTable"
                        @update="update"
                        @deleted="deleteTableItem"
                        @get-page="getPage"
                        @trier-alphabet="trierAlphabet"
                        @search="search"
                        @cancel-search="cancelSearch"/>
                </AppSuspense>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .btn-float-right{
        float: right;
    }
</style>
