<script setup>
    import {computed, defineProps, onBeforeMount, ref} from 'vue'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import {useBalanceSheetItemStore} from '../../../../../stores/management/balance-sheets/balanceSheetItems'
    import useUser from '../../../../../stores/security'
    import AppSuspense from '../../../../AppSuspense.vue'
    const props = defineProps({
        title: {required: true, type: String},
        idBalanceSheet: {required: true, type: Number},
        tabFields: {required: true, type: Array},
        tabId: {required: true, type: String},
        paymentCategory: {required: true, type: String}
    })
    console.log('AppBalanceSheetShowTable.vue', props.title)

    const fetchUser = useUser()
    const isWriterOrAdmin = fetchUser.isManagementWriter || fetchUser.isManagementAdmin
    const roleuser = ref(isWriterOrAdmin ? 'writer' : 'reader')
    const fetchBalanceSheetItems = useBalanceSheetItemStore()
    const balanceSheetItemsCriteria = useFetchCriteria(`balanceSheetItems-criteria-${props.tabId}`)
    function addPermanentFilter() {
        balanceSheetItemsCriteria.addFilter('balanceSheet', `/api/balance-sheets/${props.idBalanceSheet}`)
        balanceSheetItemsCriteria.addFilter('paymentCategory', props.paymentCategory)
    }
    addPermanentFilter()
    const itemsTable = computed(() => fetchBalanceSheetItems.items)
    async function refreshTable() {
        await fetchBalanceSheetItems.fetch(balanceSheetItemsCriteria.getFetchCriteria)
    }
    function update(item) {
        const itemId = item['@id'].match(getId)[1]
        console.log(item, itemId)
        // eslint-disable-next-line quote-props
        // router.push({name: 'company', params: {'id_company': itemId}})
    }
    async function deleteTableItem(id) {
        await fetchBalanceSheetItems.remove(id)
        await refreshTable()
    }
    async function getPage(nPage){
        balanceSheetItemsCriteria.gotoPage(nPage)
        await fetchBalanceSheetItems.fetch(balanceSheetItemsCriteria.getFetchCriteria)
    }
    async function trierAlphabet(payload) {
        balanceSheetItemsCriteria.addSort(payload.name, payload.direction)
        await fetchBalanceSheetItems.fetch(balanceSheetItemsCriteria.getFetchCriteria)
    }
    async function search(inputValues) {
        balanceSheetItemsCriteria.resetAllFilter()
        addPermanentFilter()
        if (inputValues.name) balanceSheetItemsCriteria.addFilter('name', inputValues.name)
        await fetchBalanceSheetItems.fetch(balanceSheetItemsCriteria.getFetchCriteria)
    }
    async function cancelSearch() {
        balanceSheetItemsCriteria.resetAllFilter()
        balanceSheetItemsCriteria.addFilter('balanceSheet', `/api/balance-sheets/${props.idBalanceSheet}`)
        await balanceSheetItemsCriteria.fetch(balanceSheetItemsCriteria.getFetchCriteria)
    }
    onBeforeMount(async () => {
        await fetchBalanceSheetItems.fetch(balanceSheetItemsCriteria.getFetchCriteria)
    })
</script>

<template>
    <div>
        <div class="container-fluid tableau">
            <div class="row">
                <div class="col">
                    <AppSuspense>
                        <AppCardableTable
                            :current-page="fetchBalanceSheetItems.currentPage"
                            :fields="tabFields"
                            :first-page="fetchBalanceSheetItems.firstPage"
                            :items="itemsTable"
                            :last-page="fetchBalanceSheetItems.lastPage"
                            :min="false"
                            :next-page="fetchBalanceSheetItems.nextPage"
                            :pag="fetchBalanceSheetItems.pagination"
                            :previous-page="fetchBalanceSheetItems.previousPage"
                            :title="title"
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
    </div>
</template>

<style scoped>
    div.tableau {
        color: red;
    }
</style>
