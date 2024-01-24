<script setup>
    import {computed, ref} from 'vue-demi'
    import useFetchCriteria from '../../../../stores/fetch-criteria/fetchCriteria'
    import AppSupplierCreate from './AppSupplierCreate.vue'
    import AppSuspense from '../../../AppSuspense.vue'
    import {useSuppliersStore} from '../../../../stores/purchase/supplier/suppliers'
    import useUser from '../../../../stores/security'
    import Fa from '../../../Fa'
    import {onBeforeMount, onBeforeUpdate} from "vue";
    defineProps({
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)

    const fetchUser = useUser()
    const currentCompany = fetchUser.company
    const isPurchaseWriterOrAdmin = fetchUser.isPurchaseWriter || fetchUser.isPurchaseAdmin
    const roleuser = ref(isPurchaseWriterOrAdmin ? 'writer' : 'reader')
    const tableKey = ref(0)
    const isLoaded = ref(false)
    const storeSuppliersList = useSuppliersStore()
    const supplierListCriteria = useFetchCriteria('supplier-list-criteria')
    supplierListCriteria.addFilter('company', currentCompany)

    const itemsTable = computed(() => storeSuppliersList.itemsSuppliers)
    async function refreshTable() {
        await storeSuppliersList.fetch(supplierListCriteria.getFetchCriteria).then(() => {
            console.log(itemsTable.value)
            tableKey.value += 1
        })
    }
    onBeforeMount(async () => {
        await refreshTable()
        isLoaded.value = true
    })
    onBeforeUpdate(async () => {
        await refreshTable()
    })

    const optionsEtat = [
        {text: 'agreed', value: 'agreed'},
        {text: 'draft', value: 'draft'},
        {text: 'to_validate', value: 'to_validate'},
        {text: 'warning', value: 'warning'}
    ]

    const fields = computed(() => [
        {
            label: 'Img',
            name: 'filePath',
            trie: false,
            type: 'img',
            width: 100,
            filter: false
        },
        {label: 'Nom', name: 'name', trie: true, type: 'text'},
        {label: 'CP', name: 'zipCode', trie: true, type: 'text'},
        {label: 'Ville', name: 'city', trie: true, type: 'text'},
        {
            label: 'Etat',
            name: 'state',
            options: {
                label: value =>
                    optionsEtat.find(option => option.type === value)?.text ?? null,
                options: optionsEtat
            },
            trie: false,
            type: 'select'
        }
    ])

    async function deleted(id){
        await storeSuppliersList.remove(id)
        await refreshTable()
    }

    async function getPage(nPage){
        supplierListCriteria.gotoPage(parseFloat(nPage))
        await storeSuppliersList.fetch(supplierListCriteria.getFetchCriteria)
    }

    async function search(inputValues) {
        supplierListCriteria.resetAllFilter()
        supplierListCriteria.addFilter('company', currentCompany)
        if (inputValues.name) supplierListCriteria.addFilter('name', inputValues.name)
        if (inputValues.state) supplierListCriteria.addFilter('embState.state[]', inputValues.state)
        await storeSuppliersList.fetch(supplierListCriteria.getFetchCriteria)
    }
    async function cancelSearch() {
        supplierListCriteria.resetAllFilter()
        supplierListCriteria.addFilter('company', currentCompany)
        await storeSuppliersList.fetch(supplierListCriteria.getFetchCriteria)
    }

    async function trierAlphabet(payload) {
        supplierListCriteria.addSort(payload.name, payload.direction)
        await storeSuppliersList.fetch(supplierListCriteria.getFetchCriteria)
    }
</script>

<template>
    <div class="row">
        <div class="col">
            <h1>
                <Fa :icon="icon"/>
                {{ title }}
                <span v-if="isPurchaseWriterOrAdmin" class="btn-float-right">
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
        <AppSupplierCreate :modal-id="modalId" :title="title" :target="target"/>
        <div class="col">
            <AppSuspense>
                <AppCardableTable
                    v-if="isLoaded"
                    :current-page="storeSuppliersList.currentPage"
                    :fields="fields"
                    :first-page="storeSuppliersList.firstPage"
                    :items="itemsTable"
                    :last-page="storeSuppliersList.lastPage"
                    :next-page="storeSuppliersList.nextPage"
                    :pag="storeSuppliersList.pagination"
                    :previous-page="storeSuppliersList.previousPage"
                    :user="roleuser"
                    form="formSupplierCardableTable"
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
