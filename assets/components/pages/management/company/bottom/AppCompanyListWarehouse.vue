<script setup>
    import {computed, ref} from 'vue'
    import {useCompanyListWarehouseStore} from '../../../../../stores/company/companyListWarehouse'
    import {useRoute, useRouter} from 'vue-router'

    const roleuser = ref('reader')
    // let violations = []
    const AddForm = ref(false)
    // const isPopupVisible = ref(false)
    const sortable = ref(false)
    const filter = ref(false)
    let trierAlpha = {}
    let filterBy = {}

    const maRoute = useRoute()
    const companyId = maRoute.params.id_company
    const router = useRouter()

    const storeCompanyListWarehouse = useCompanyListWarehouseStore()
    storeCompanyListWarehouse.setIdCompany(companyId)
    await storeCompanyListWarehouse.fetch()
    const itemsTable = ref(storeCompanyListWarehouse.itemsCompanyWarehouse)

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
    const getId = /.*?\/(\d+)/

    function update(item) {
        const itemId = item['@id'].match(getId)[1]
        // eslint-disable-next-line camelcase
        const routeData = router.resolve({name: 'warehouse-show', params: {id_warehouse: itemId}})
        window.open(routeData.href, '_blank')
    }
    async function getPage(nPage){
        await storeCompanyListWarehouse.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
        itemsTable.value = [...storeCompanyListWarehouse.itemsCompanyWarehouse]
    }
    async function trierAlphabet(payload) {
        await storeCompanyListWarehouse.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }

    async function search(inputValues) {
        const payload = {
            name: inputValues.name ?? ''
        }

        await storeCompanyListWarehouse.filterBy(payload)
        itemsTable.value = [...storeCompanyListWarehouse.itemsCompanyWarehouse]
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = true
        await storeCompanyListWarehouse.fetch()
        itemsTable.value = storeCompanyListWarehouse.itemsCompanyWarehouse
    }
</script>

<template>
    <div class="gui-bottom">
        <AppRow>
            <AppCol>
                <AppCardableTable
                    :current-page="storeCompanyListWarehouse.currentPage"
                    :fields="tabFields"
                    :first-page="storeCompanyListWarehouse.firstPage"
                    :items="itemsTable"
                    :last-page="storeCompanyListWarehouse.lastPage"
                    :min="AddForm"
                    :next-page="storeCompanyListWarehouse.nextPage"
                    :pag="storeCompanyListWarehouse.pagination"
                    :previous-page="storeCompanyListWarehouse.previousPage"
                    :user="roleuser"
                    :should-see="true"
                    :should-delete="false"
                    form="formCompanyWarehouseCardableTable"
                    @update="update"
                    @get-page="getPage"
                    @trier-alphabet="trierAlphabet"
                    @search="search"
                    @cancel-search="cancelSearch"/>
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
