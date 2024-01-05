<script setup>
    import AppProductCreate from './AppProductCreate.vue'
    import {computed, ref} from 'vue'
    import useUser from '../../../../stores/security'
    import useFetchCriteria from '../../../../stores/fetch-criteria/fetchCriteria'
    import {useProductStore} from '../../../../stores/project/product/products'
    import useOptions from '../../../../stores/option/options'
    import FontAwesomeIcon from '@fortawesome/vue-fontawesome/src/components/FontAwesomeIcon'

    defineProps({
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)

    const fetchUser = useUser()
    const currentCompany = fetchUser.company
    const isProjectWriterOrAdmin = fetchUser.isProjectWriter || fetchUser.isProjectAdmin
    const roleuser = ref(isProjectWriterOrAdmin ? 'writer' : 'reader')

    const storeProductsList = useProductStore()
    await storeProductsList.fetch()

    const productListCriteria = useFetchCriteria('product-list-criteria')
    productListCriteria.addFilter('company', currentCompany)

    async function refreshTable() {
        await storeProductsList.fetch(productListCriteria.getFetchCriteria)
    }
    await refreshTable()

    const itemsTable = computed(() => storeProductsList.productItem)

    const optionsEtat = [
        {text: 'agreed', value: 'agreed'},
        {text: 'draft', value: 'draft'},
        {text: 'to_validate', value: 'to_validate'},
        {text: 'warning', value: 'warning'}
    ]
    const stateBlockerOptions = [
        {text: 'blocked', value: 'blocked'},
        {text: 'disabled', value: 'disabled'},
        {text: 'enabled', value: 'enabled'}
    ]
    const typeProductOptions = [
        {text: 'Prototype', value: 'Prototype'},
        {text: 'EI', value: 'EI'},
        {text: 'Série', value: 'Série'},
        {text: 'Piéce de rechange', value: 'Piéce de rechange'}
    ]

    const fecthOptionsProductFamilies = useOptions('product-families')
    await fecthOptionsProductFamilies.fetchOp()
    const optionsProductFamilies = computed(() =>
        fecthOptionsProductFamilies.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))

    const fields = computed(() => [
        {label: 'Référence', name: 'code', trie: true, type: 'text'},
        {label: 'Indice', name: 'index', trie: true, type: 'text'},
        {label: 'Désignation', name: 'name', trie: true, type: 'text'},
        // {
        //     label: 'Compagnies',
        //     name: 'companies',
        //     type: 'multiselect-fetch',
        //     api: '/api/companies',
        //     filteredProperty: 'name',
        //     max: 3
        // },
        {
            label: 'Famille',
            name: 'family',
            options: {
                label: value =>
                    optionsProductFamilies.value.find(option => option.value === value)?.text ?? null,
                options: optionsProductFamilies.value
            },
            type: 'select'
        },
        {
            label: 'Type de Produit',
            name: 'kind',
            options: {
                label: value =>
                    typeProductOptions.find(option => option.value === value)?.text ?? null,
                options: typeProductOptions
            },
            type: 'select'
        },
        // {label: 'Date d\'expiration', name: 'endOfLife', trie: false, type: 'date'},
        {
            label: 'Etat de maturité',
            name: 'state',
            options: {
                label: value =>
                    optionsEtat.find(option => option.type === value)?.text ?? null,
                options: optionsEtat
            },
            trie: false,
            type: 'select'
        },
        {
            label: 'Etat de de blocage',
            name: 'stateBlocker',
            options: {
                label: value =>
                    stateBlockerOptions.find(option => option.type === value)?.text ?? null,
                options: stateBlockerOptions
            },
            trie: false,
            type: 'select'
        }
    ])
    async function deleted(id){
        await storeProductsList.remove(id)
        await refreshTable()
    }
    async function getPage(nPage){
        productListCriteria.gotoPage(parseFloat(nPage))
        await storeProductsList.fetch(productListCriteria.getFetchCriteria)
    }
    async function search(inputValues) {
        productListCriteria.resetAllFilter()
        productListCriteria.addFilter('company', currentCompany)
        if (inputValues.code) productListCriteria.addFilter('code', inputValues.code)
        if (inputValues.index) productListCriteria.addFilter('index', inputValues.index)
        if (inputValues.name) productListCriteria.addFilter('name', inputValues.name)
        if (inputValues.family) productListCriteria.addFilter('family', inputValues.family)
        if (inputValues.kind) productListCriteria.addFilter('kind', inputValues.kind)
        // if (inputValues.endOfLife) productListCriteria.addFilter('endOfLife', inputValues.endOfLife)
        if (inputValues.stateBlocker) productListCriteria.addFilter('embBlocker.state[]', inputValues.stateBlocker)
        if (inputValues.state) productListCriteria.addFilter('embState.state[]', inputValues.state)
        await storeProductsList.fetch(productListCriteria.getFetchCriteria)
    }
    async function cancelSearch() {
        productListCriteria.resetAllFilter()
        productListCriteria.addFilter('company', currentCompany)
        await storeProductsList.fetch(productListCriteria.getFetchCriteria)
    }
    async function trierAlphabet(payload) {
        productListCriteria.addSort(payload.name, payload.direction)
        await storeProductsList.fetch(productListCriteria.getFetchCriteria)
    }
</script>

<template>
    <div class="row">
        <div class="col">
            <h1>
                <font-awesome-icon icon="fa-brands fa-product-hunt" />
                {{ title }}
                <span v-if="isProjectWriterOrAdmin" class="btn-float-right">
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
    <div class="row">
        <AppSuspense>
            <AppProductCreate :modal-id="modalId" title="Création d'un nouveau Produit" :options-product-families="optionsProductFamilies" :target="target"/>
        </AppSuspense>
        <div class="col">
            <AppSuspense>
                <AppCardableTable
                    :current-page="storeProductsList.currentPage"
                    :fields="fields"
                    :first-page="storeProductsList.firstPage"
                    :items="itemsTable"
                    :last-page="storeProductsList.lastPage"
                    :next-page="storeProductsList.nextPage"
                    :pag="storeProductsList.pagination"
                    :previous-page="storeProductsList.previousPage"
                    :user="roleuser"
                    form="formProductCardableTable"
                    @deleted="deleted"
                    @get-page="getPage"
                    @trier-alphabet="trierAlphabet"
                    @search="search"
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
