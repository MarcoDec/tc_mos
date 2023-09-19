<script setup>
    import {computed, ref} from 'vue'
    import {useCompanyListSupplyStore} from '../../../../../stores/company/companyListSupply'
    import {useRoute, useRouter} from 'vue-router'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'

    const roleuser = ref('reader')
    const AddForm = ref(false)
    const sortable = ref(false)
    const filter = ref(false)

    const maRoute = useRoute()
    const companyId = maRoute.params.id_company
    const router = useRouter()
    const companySuppliesFetchCriteria = useFetchCriteria('companySuppliesFetchCriteria')
    companySuppliesFetchCriteria.addFilter('company', `/api/companies/${companyId}`)

    const storeCompanyListSupply = useCompanyListSupplyStore()
    storeCompanyListSupply.setIdCompany(companyId)
    await storeCompanyListSupply.fetch(companySuppliesFetchCriteria.getFetchCriteria)
    const itemsTable = ref(storeCompanyListSupply.itemsCompanySupply)

    const tabFields = [
        {
            create: false,
            filter: true,
            label: 'Référence',
            name: 'ref',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Proportion',
            name: 'proportion',
            sort: true,
            type: 'number',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Code Produit',
            name: 'product.code',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Indice',
            name: 'product.index',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Designation',
            name: 'product.name',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Type',
            name: 'product.kind',
            sort: true,
            type: 'text',
            update: true
        }
    ]
    const getId = /.*?\/(\d+)/

    function update(item) {
        console.log(item)
        const itemId = item['product.@id'].match(getId)[1]
        // eslint-disable-next-line camelcase
        const routeData = router.resolve({name: 'product', params: {id_product: itemId}})
        window.open(routeData.href, '_blank')
    }

    async function getPage(nPage){
        companySuppliesFetchCriteria.page = nPage
        await storeCompanyListSupply.fetch(companySuppliesFetchCriteria.getFetchCriteria)
        itemsTable.value = storeCompanyListSupply.itemsCompanySupply
    }
    async function trierAlphabet(payload) {
        await storeCompanyListSupply.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }

    async function search(inputValues) {
        companySuppliesFetchCriteria.resetAllFilter()
        companySuppliesFetchCriteria.addFilter('company', `/api/companies/${companyId}`)
        if (inputValues.ref) companySuppliesFetchCriteria.addFilter('ref', inputValues.ref)
        if (inputValues.proportion) companySuppliesFetchCriteria.addFilter('proportion', inputValues.proportion)
        if (inputValues['product.code']) companySuppliesFetchCriteria.addFilter('product.code', inputValues['product.code'])
        if (inputValues['product.index']) companySuppliesFetchCriteria.addFilter('product.index', inputValues['product.index'])
        if (inputValues['product.name']) companySuppliesFetchCriteria.addFilter('product.name', inputValues['product.name'])
        if (inputValues['product.kind']) companySuppliesFetchCriteria.addFilter('product.kind', inputValues['product.kind'])
        await storeCompanyListSupply.fetch(companySuppliesFetchCriteria.getFetchCriteria)
        itemsTable.value = storeCompanyListSupply.itemsCompanySupply
    }
    async function cancelSearch() {
        companySuppliesFetchCriteria.resetAllFilter()
        companySuppliesFetchCriteria.addFilter('company', `/api/companies/${companyId}`)
        await storeCompanyListSupply.fetch(companySuppliesFetchCriteria.getFetchCriteria)
        itemsTable.value = storeCompanyListSupply.itemsCompanySupply
    }
</script>

<template>
    <div class="gui-bottom">
        <AppRow>
            <AppCol>
                <AppCardableTable
                    :current-page="storeCompanyListSupply.currentPage"
                    :fields="tabFields"
                    :first-page="storeCompanyListSupply.firstPage"
                    :items="itemsTable"
                    :last-page="storeCompanyListSupply.lastPage"
                    :next-page="storeCompanyListSupply.nextPage"
                    :pag="storeCompanyListSupply.pagination"
                    :previous-page="storeCompanyListSupply.previousPage"
                    :user="roleuser"
                    :should-delete="false"
                    form="formCompanySupplyCardableTable"
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
