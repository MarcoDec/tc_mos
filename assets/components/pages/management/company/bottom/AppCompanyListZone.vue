<script setup>
    import {computed, ref} from 'vue'
    import {useRoute} from 'vue-router'
    import {useCompanyListZoneStore} from '../../../../../stores/company/companyListZone'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'

    const roleuser = ref('reader')
    const sortable = ref(false)
    const filter = ref(false)
    let trierAlpha = {}

    const maRoute = useRoute()
    const companyId = maRoute.params.id_company

    const storeCompanyListZone = useCompanyListZoneStore()
    const zoneFetchCriteria = useFetchCriteria('zoneFetchCriteria')
    zoneFetchCriteria.addFilter('company', `/api/companies/${companyId}`)
    await storeCompanyListZone.fetch(zoneFetchCriteria.getFetchCriteria)
    const itemsTable = ref(storeCompanyListZone.itemsCompanyZone)

    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Nom',
            name: 'name',
            sort: true,
            type: 'text',
            update: true
        }
    ]

    async function deleted(id){
        await storeCompanyListZone.deleted(id)
        itemsTable.value = [...storeCompanyListZone.itemsCompanyZone]
    }
    async function getPage(nPage){
        await storeCompanyListZone.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeCompanyListZone.itemsCompanyZone]
    }
    async function trierAlphabet(payload) {
        await storeCompanyListZone.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }

    async function search(inputValues) {
        zoneFetchCriteria.resetAllFilter()
        zoneFetchCriteria.addFilter('company', `/api/companies${companyId}`)
        if (inputValues.name) zoneFetchCriteria.addFilter('name', inputValues.name)
        await storeCompanyListZone.fetch(zoneFetchCriteria.getFetchCriteria)
        itemsTable.value = storeCompanyListZone.zones
    }
    async function cancelSearch() {
        zoneFetchCriteria.resetAllFilter()
        zoneFetchCriteria.addFilter('company', `/api/companies${companyId}`)
        await storeCompanyListZone.fetch(zoneFetchCriteria.getFetchCriteria)
        itemsTable.value = storeCompanyListZone.zones
    }
</script>

<template>
    <div class="gui-bottom">
        <AppRow>
            <AppCol>
                <AppCardableTable
                    :current-page="storeCompanyListZone.currentPage"
                    :fields="tabFields"
                    :first-page="storeCompanyListZone.firstPage"
                    :items="itemsTable"
                    :last-page="storeCompanyListZone.lastPage"
                    :next-page="storeCompanyListZone.nextPage"
                    :pag="storeCompanyListZone.pagination"
                    :previous-page="storeCompanyListZone.previousPage"
                    :user="roleuser"
                    :should-delete="false"
                    :should-see="false"
                    form="formCompanyZoneCardableTable"
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
