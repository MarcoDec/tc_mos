<script setup>
    import {computed, onBeforeMount, ref} from 'vue'
    import {useRoute} from 'vue-router'
    import api from '../../../../../api'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import {useBalanceSheetItemStore} from '../../../../../stores/management/balance-sheets/balanceSheetItems'
    import {useBalanceSheetStore} from '../../../../../stores/management/balance-sheets/balanceSheets'
    import {useCompanyStore} from '../../../../../stores/management/companies/companies'
    import useUser from '../../../../../stores/security'
    import AppSuspense from '../../../../AppSuspense.vue'

    console.log('Début AppBalanceSheetShow.vue')
    const route = useRoute()
    const idBalanceSheet = Number(route.params.id)

    const fetchUser = useUser()
    const fetchBalanceSheet = useBalanceSheetStore()
    const fetchBalanceSheetItems = useBalanceSheetItemStore()
    const balanceSheetItemsCriteria = useFetchCriteria('balanceSheetItems-criteria')
    const company = ref({})

    const isWriterOrAdmin = fetchUser.isManagementWriter || fetchUser.isManagementAdmin
    const roleuser = ref(isWriterOrAdmin ? 'writer' : 'reader')

    //region récupération de la balance sheet
    const currentBalanceSheet = ref({})
    balanceSheetItemsCriteria.addFilter('balanceSheet', `/api/balance-sheets/${idBalanceSheet}`)
    onBeforeMount(async () => {
        await fetchBalanceSheet.fetchById(idBalanceSheet)
        currentBalanceSheet.value = fetchBalanceSheet.item
        console.log('currentBalanceSheet', currentBalanceSheet.value)
        await fetchBalanceSheetItems.fetch(balanceSheetItemsCriteria.getFetchCriteria)
        console.log('fetchBalanceSheetItems', fetchBalanceSheetItems.items)
        company.value = await api(fetchBalanceSheet.item.company['@id'], 'GET')
        console.log('company', company.value)
    })
    //endregion
    const tabFields = [
        {label: 'Catégorie', name: 'paymentCategory', trie: true, type: 'text'},
        {label: 'Sous-Catégorie', name: 'subCategory', trie: true, type: 'text'},
        {label: 'Partie Prenante', name: 'stakeholder', trie: true, type: 'text'},
        {label: 'Label', name: 'label', trie: true, type: 'text'},
        {label: 'Date Facture', name: 'billDate', trie: true, type: 'text'},
        {label: 'Date Paiement', name: 'paymentDate', trie: true, type: 'text'},
        {label: 'Ref Paiement', name: 'paymentRef', trie: true, type: 'text'},
        {label: 'Quantité', name: 'quantity', trie: true, type: 'text'},
        {label: 'Prix unitaire', name: 'unitPrice', trie: true, type: 'text'},
        {label: 'tva', name: 'vat', trie: true, type: 'text'},
        {label: 'Montant', name: 'amount', trie: true, type: 'text'},
        {label: 'Méthode paiement', name: 'paymentMethod', trie: true, type: 'text'}
    ]
    const itemsTable = computed(() => fetchBalanceSheet.items)
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
        balanceSheetItemsCriteria.addFilter('balanceSheet', `/api/balance-sheets/${idBalanceSheet}`)
        if (inputValues.name) balanceSheetItemsCriteria.addFilter('name', inputValues.name)
        await fetchBalanceSheetItems.fetch(balanceSheetItemsCriteria.getFetchCriteria)
    }
    async function cancelSearch() {
        balanceSheetItemsCriteria.resetAllFilter()
        balanceSheetItemsCriteria.addFilter('balanceSheet', `/api/balance-sheets/${idBalanceSheet}`)
        await balanceSheetItemsCriteria.fetch(balanceSheetItemsCriteria.getFetchCriteria)
    }
    console.log('Fin AppBalanceSheetShow.vue')
</script>

<template>
    <AppSuspense>
        <div class="title">
            Suivi des dépenses {{ fetchBalanceSheet.item.month }} - {{ fetchBalanceSheet.item.year }} pour {{ company.name }}
        </div>
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
    </AppSuspense>
</template>

<style scoped>
    .title {
        font-size: 2em;
        color: #41b883;
    }
</style>
