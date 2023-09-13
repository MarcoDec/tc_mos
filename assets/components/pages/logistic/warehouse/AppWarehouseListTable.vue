<script setup>
    import {computed, ref} from 'vue'
    import useFetchCriteria from '../../../../stores/fetch-criteria/fetchCriteria'
    import AppSuspense from '../../../AppSuspense.vue'
    import AppFormCardable from '../../../form-cardable/AppFormCardable'
    import Fa from '../../../Fa'
    import {useWarehouseListStore} from './provisoir/warehouseList'
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
    const isLogisticWriterOrAdmin = fetchUser.isLogisticsWriter || fetchUser.isLogisticsAdmin
    const roleuser = ref(isLogisticWriterOrAdmin ? 'writer' : 'reader')
    //endregion
    //region récupération de la liste des companies
    const fetchCompanies = useCompanyStore()
    await fetchCompanies.fetch()
    const optionsCompanies = fetchCompanies.companies.map(item => ({
        label: item.name, text: item.name, value: item['@id']
    })).filter(item => item.value !== currentCompany)
    //endregion
    //region Chargement de la liste des entrepôts liés à la compagnie de l'utilisateur
    const storeWarehouseList = useWarehouseListStore()
    const warehouseListCriteria = useFetchCriteria('warehouse-list-criteria')
    warehouseListCriteria.addFilter('company', currentCompany)
    async function refreshTable() {
        await storeWarehouseList.fetch(warehouseListCriteria.getFetchCriteria)
    }
    await refreshTable()
    const itemsTable = computed(() => storeWarehouseList.itemsWarehouses)
    //endregion

    //region initialisation des éléments pour le formulaire de création d'un nouvel entrepot
    const AddForm = ref(false)
    let addFormKey = 0
    const formData = ref({
        company: currentCompany, families: null, name: null, destination: null
    })
    const baseAddFormFields = [
        {label: 'Nom *', name: 'name', type: 'text'},
        {
            label: 'Famille ',
            name: 'families',
            options: {
                options: [
                    {
                        text: 'Prison',
                        value: 'prison'
                    },
                    {
                        text: 'Production',
                        value: 'production'
                    },
                    {
                        text: 'Réception',
                        value: 'réception'
                    },
                    {
                        text: 'Magasin pièces finies',
                        value: 'magasin pièces finies'
                    },
                    {
                        text: 'Expédition',
                        value: 'expédition'
                    },
                    {
                        text: 'Magasin matières premières',
                        value: 'magasin matières premières'
                    },
                    {
                        text: 'Camion',
                        value: 'camion'
                    }
                ]
            },
            type: 'multiselect'
        }
    ]
    const destinationField = {
        label: 'Destination',
        name: 'destination',
        options: {
            options: optionsCompanies,
            label: 'test'
        },
        type: 'select'
    }
    const fieldsForm = baseAddFormFields
    let violations = []
    //region Fonctions relatives au formulaire d'ajout
    function showAddForm(){
        AddForm.value = true
    }
    function input(v){
        formData.value = v
        if (v.families && v.families.includes('camion')) {
            //on s'assure que fieldsForm contient la définition du champ destination
            if (typeof fieldsForm.find(item => item.name === 'destination') === 'undefined') {
                fieldsForm.push(destinationField)
                addFormKey++
            }
        } else if (typeof fieldsForm.find(item => item.name === 'destination') !== 'undefined') {
            fieldsForm.pop()
            formData.value.destination = null
            addFormKey++
        }
    }
    async function ajoutWarehouse(){
        violations = await storeWarehouseList.addWarehouse(formData.value)
        if (violations.length > 0){
            isPopupVisible.value = true
        } else {
            AddForm.value = false
            isPopupVisible.value = false
            await refreshTable() //On recharge les données
        }
    }
    function annule(){
        AddForm.value = false
        const itemsNull = {
            families: null
        }
        formData.value = itemsNull
        isPopupVisible.value = false
    }
    //endregion
    //endregion
    //region initialisation des éléments pour le tableau de liste
    const isPopupVisible = ref(false)
    const optionsFamilies = [
        {
            text: 'Prison',
            value: 'prison'
        },
        {
            text: 'Production',
            value: 'production'
        },
        {
            text: 'Réception',
            value: 'réception'
        },
        {
            text: 'Magasin pièces finies',
            value: 'magasin pièces finies'
        },
        {
            text: 'Expédition',
            value: 'expédition'
        },
        {
            text: 'Magasin matières premières',
            value: 'magasin matières premières'
        },
        {
            text: 'Camion',
            value: 'camion'
        }
    ]
    const tabFields = [
        {label: 'Nom', min: true, name: 'name', trie: true, type: 'text'},
        {
            label: 'Famille ',
            name: 'families',
            options: {
                label: itemValue => itemValue,
                options: optionsFamilies
            },
            type: 'select'
        },
        {
            label: 'Destination',
            name: 'destination',
            options: {
                options: optionsCompanies,
                label: itemValue => optionsCompanies.find(item => item.value === itemValue).text
            },
            type: 'select'
        }
    ]
    //endregion

    //region Fonctions relatives à la liste
    function update(item) {
        router.push(`warehouse/${item.id}`)
    }
    async function deleteWarehouse(id) {
        await storeWarehouseList.remove(id)
        await refreshTable()
    }
    async function search(inputValues) {
        warehouseListCriteria.resetAllFilter()
        warehouseListCriteria.addFilter('company', currentCompany)
        if (inputValues.name) warehouseListCriteria.addFilter('name', inputValues.name)
        if (inputValues.families) warehouseListCriteria.addFilter('families[]', inputValues.families)
        if (inputValues.destination) warehouseListCriteria.addFilter('destination', inputValues.destination)
        await storeWarehouseList.fetch(warehouseListCriteria.getFetchCriteria)
    }
    async function cancelSearch() {
        warehouseListCriteria.resetAllFilter()
        warehouseListCriteria.addFilter('company', currentCompany)
        await storeWarehouseList.fetch(warehouseListCriteria.getFetchCriteria)
    }

    async function getPage(nPage){
        warehouseListCriteria.gotoPage(nPage)
        await storeWarehouseList.fetch(warehouseListCriteria.getFetchCriteria)
        //await storeWarehouseList.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        //itemsTable.value = [...storeWarehouseList.itemsWarehouses]
    }
    async function trierAlphabet(payload) {
        warehouseListCriteria.addSort(payload.name, payload.direction)
        await storeWarehouseList.fetch(warehouseListCriteria.getFetchCriteria)
    }
    //endregion
</script>

<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <h1>
                    <Fa :icon="icon"/>
                    {{ title }}
                    <span v-if="isLogisticWriterOrAdmin" class="btn-float-right">
                        <AppBtn variant="success" label="Ajout" @click="showAddForm">
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
                        :current-page="storeWarehouseList.currentPage"
                        :fields="tabFields"
                        :first-page="storeWarehouseList.firstPage"
                        :items="itemsTable"
                        :last-page="storeWarehouseList.lastPage"
                        :min="AddForm"
                        :next-page="storeWarehouseList.nextPage"
                        :pag="storeWarehouseList.pagination"
                        :previous-page="storeWarehouseList.previousPage"
                        :user="roleuser"
                        form="formWarehouseCardableTable"
                        @update="update"
                        @deleted="deleteWarehouse"
                        @get-page="getPage"
                        @trier-alphabet="trierAlphabet"
                        @search="search"
                        @cancel-search="cancelSearch"/>
                </AppSuspense>
            </div>
            <div v-show="AddForm" :key="addFormKey" class="col-7">
                <AppSuspense>
                    <AppCard class="bg-blue col" title="">
                        <div class="row">
                            <button id="btnRetour1" class="btn btn-danger btn-icon btn-sm col-1" @click="annule">
                                <Fa icon="angle-double-left"/>
                            </button>
                            <h4 class="col">
                                <Fa icon="plus"/> Ajout d'un nouvel entrepôt {{ addFormKey }}
                            </h4>
                        </div>
                        <AppSuspense>
                            <AppFormCardable id="addWarehouse" :model-value="formData" :fields="fieldsForm" label-cols @update:model-value="input"/>
                        </AppSuspense>
                        <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                            <div v-for="violation in violations" :key="violation">
                                <li>{{ violation.message }}</li>
                            </div>
                        </div>
                        <div class="btnright row">
                            <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutWarehouse">
                                <Fa icon="plus"/> Enregister
                            </AppBtn>
                        </div>
                    </AppCard>
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
