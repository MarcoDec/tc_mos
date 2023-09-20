<script setup>
    import {computed, ref} from 'vue'
    import api from '../../../../../api'
    import {useCompanyListSupplierStore} from '../../../../../stores/company/companyListSupplier'
    import {useRoute, useRouter} from 'vue-router'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import {useSuppliersStore} from '../../../../../stores/purchase/supplier/suppliers'
    import AppFormCardable from '../../../../form-cardable/AppFormCardable'

    const roleuser = ref('reader')
    const AddForm = ref(false)
    const sortable = ref(false)
    const filter = ref(false)
    let trierAlpha = {}
    let filterBy = {}
    const router = useRouter()

    //region initialisation des données communes tableau et formulaire d'ajout
    const maRoute = useRoute()
    const companyId = maRoute.params.id_company
    //endregion
    //region gestion données tableau
    const storeCompanyListSupplier = useCompanyListSupplierStore()
    storeCompanyListSupplier.setIdCompany(companyId)
    await storeCompanyListSupplier.fetch()
    const itemsTable = ref(storeCompanyListSupplier.itemsCompanySupplier)
    //endregion

    //region gestion données formulaire d'ajout
    //region chargement de la liste des fournisseurs
    const storeSuppliers = useSuppliersStore()
    const suppliersFetchCriteria = useFetchCriteria('suppliersInList')
    suppliersFetchCriteria.addFilter('pagination', 'false')
    await storeSuppliers.fetch(suppliersFetchCriteria.getFetchCriteria)
    const optionsSuppliers = storeSuppliers.suppliers.map(item => ({
        value: item['@id'],
        text: item.name
    }))
    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Nom',
            name: 'name',
            sort: true,
            type: 'text'
        }
    ]
    //endregion
    //region initalisation formulaire
    const formData = ref({
        supplier: null,
        companyId: `/api/companies${companyId}`
    })
    const fieldsForm = [
        {
            label: 'Fournisseur',
            name: 'supplier',
            options: {
                label: value => optionsSuppliers.filter(item => item.value === value)[0].text,
                options: optionsSuppliers
            },
            type: 'select'
        }
    ]
    //endregion
    //endregion

    //region fonctions tableau
    async function update(item) {
        //region Ouverture de la fiche fournisseur
        //region    1. récupération de l'item supplier-companies
        const supplierCompaniesIri = item['@id']
        const supplierCompany = await api(supplierCompaniesIri, 'GET')
        //endregion
        //region    2. récupération de l'id du supplier
        const supplierId = supplierCompany.supplier.id
        //endregion
        //region    3. ouverture dans un nouvel onglet de la fiche fournisseur
        // eslint-disable-next-line camelcase
        const routeData = router.resolve({name: 'supplier', params: {id_supplier: supplierId}})
        window.open(routeData.href, '_blank')
        //endregion
        //endregion
    }

    async function deleted(id){
        await storeCompanyListSupplier.deleted(id)
        itemsTable.value = [...storeCompanyListSupplier.itemsCompanySupplier]
    }
    async function getPage(nPage){
        await storeCompanyListSupplier.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeCompanyListSupplier.itemsCompanySupplier]
    }
    async function trierAlphabet(payload) {
        await storeCompanyListSupplier.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }

    async function search(inputValues) {
        const payload = {
            name: inputValues.name ?? ''
        }

        await storeCompanyListSupplier.filterBy(payload)
        itemsTable.value = [...storeCompanyListSupplier.itemsCompanySupplier]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        await storeCompanyListSupplier.fetch()
        itemsTable.value = storeCompanyListSupplier.itemsCompanySupplier
    }
    //endregion
    //region fonctions formulaire d'ajout
    function ajoute(){
        AddForm.value = true
        formData.value = {
            supplier: null,
            company: `/api/companies/${companyId}`
        }
    }
    async function ajoutSupplierCompany(){
        await storeCompanyListSupplier.addSupplierCompany(formData.value)
        AddForm.value = false
        await storeCompanyListSupplier.fetch()
        itemsTable.value = storeCompanyListSupplier.itemsCompanySupplier
    }
    function annule(){
        AddForm.value = false
        formData.value = {
            supplier: null,
            company: `/api/companies/${companyId}`
        }
    }
    function addFormDataChanged(item) {
        formData.value = item
    }
    //endregion
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
                    :current-page="storeCompanyListSupplier.currentPage"
                    :fields="tabFields"
                    :first-page="storeCompanyListSupplier.firstPage"
                    :items="itemsTable"
                    :last-page="storeCompanyListSupplier.lastPage"
                    :min="AddForm"
                    :next-page="storeCompanyListSupplier.nextPage"
                    :pag="storeCompanyListSupplier.pagination"
                    :previous-page="storeCompanyListSupplier.previousPage"
                    :user="roleuser"
                    form="formCompanySupplierCardableTable"
                    @update="update"
                    @deleted="deleted"
                    @get-page="getPage"
                    @trier-alphabet="trierAlphabet"
                    @search="search"
                    @cancel-search="cancelSearch"/>
            </AppCol>
            <AppCol v-if="AddForm" class="col-7">
                <AppCard class="bg-blue col" title="">
                    <AppRow>
                        <button id="btnRetour1" class="btn btn-danger btn-icon btn-sm col-1" @click="annule">
                            <Fa icon="angle-double-left"/>
                        </button>
                        <h4 class="col">
                            <Fa icon="plus"/> Ajout
                        </h4>
                    </AppRow>
                    <br/>
                    <AppFormCardable id="addCompanyEvenementQualite" :fields="fieldsForm" :model-value="formData" label-cols @update:model-value="addFormDataChanged"/>
                    <AppCol class="btnright">
                        <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutSupplierCompany">
                            <Fa icon="plus"/> Ajouter
                        </AppBtn>
                    </AppCol>
                </AppCard>
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
