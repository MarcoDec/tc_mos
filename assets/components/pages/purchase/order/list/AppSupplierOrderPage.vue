<script setup>
    import {computed, onBeforeMount, onBeforeUpdate, ref} from 'vue'
    import useFetchCriteria from "../../../../../stores/fetch-criteria/fetchCriteria"
    import AppSuspense from "../../../../AppSuspense.vue"

    import {usePurchaseOrderStore} from "../../../../../stores/purchase/order/purchaseOrder"
    import useUser from '../../../../../stores/security'
    import {Modal} from 'bootstrap'
    import {useRouter} from 'vue-router'
    import AppSupplierOrderCreate from "./AppSupplierOrderCreate.vue"

    defineProps({
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })

    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)
    const supplierOrderCreateModal = ref(null)

    const fetchUser = useUser()
    const router = useRouter()
    const storeSupplierOrderList = usePurchaseOrderStore()
    const supplierOrderListCriteria = useFetchCriteria('supplier-order-list-criteria')
    const currentCompany = fetchUser.company
    supplierOrderListCriteria.addFilter('company', currentCompany)

    const isPurchaseWriterOrAdmin = fetchUser.isPurchaseWriter || fetchUser.isPurchaseAdmin
    const canDelete = ref(isPurchaseWriterOrAdmin)
    const userRole = ref(isPurchaseWriterOrAdmin ? 'writer' : 'reader')

    async function refreshTable() {
        await storeSupplierOrderList.fetch(supplierOrderListCriteria.getFetchCriteria)
    }
    async function updateData() {
        await refreshTable()
    }
    onBeforeMount(() => {
        updateData()
    })
    onBeforeUpdate(() => {
        updateData()
    })
    const itemsTable = computed(() => storeSupplierOrderList.purchaseOrders)
    const optionsEtat = [
        {text: 'Brouillon', value: 'draft'},
        {text: 'Caddie', value: 'cart'},
        {text: 'Validée', value: 'agreed'},
        {text: 'Réceptionné partiellement', value: 'partially_received'},
        {text: 'Réceptionné', value: 'received'},
        {text: 'Payé', value: 'paid'}
    ]
    const optionsCloser = [
        {text: 'Actif', value: 'enabled'},
        {text: 'Bloqué', value: 'blocked'},
        {text: 'Clos', value: 'closed'}
    ]
    const fields = computed(() => [
        {label: 'ID', name: 'id', trie: true, type: 'text', width: 50, filter: true},
        // {label: 'Référence', name: 'ref', trie: true, type: 'text', width: 80},
        {
            label: 'Fournisseur',
            name: 'supplier',
            trie: false,
            type: 'multiselect-fetch',
            api: '/api/suppliers',
            filteredProperty: 'name',
            max: 1
        },
        {
            label: 'Site de Destination',
            name: 'deliveryCompany',
            trie: false,
            type: 'multiselect-fetch',
            api: '/api/companies',
            filteredProperty: 'name',
            max: 1
        },
        {
            label: 'Type de commande',
            name: 'orderFamily',
            width: 120,
            type: 'text',
            trie: true
        },
        {
            label: 'Type d\'item',
            name: 'kind',
            width: 120,
            type: 'text'
        },
        {
            label: 'Etat',
            name: 'state',
            options: {
                label: value =>
                    optionsEtat.find(option => option.value === value)?.text ?? null,
                options: optionsEtat
            },
            trie: false,
            type: 'select',
            width: 120
        },
        {
            label: 'Etat Qualité',
            name: 'closer',
            options: {
                label: value =>
                    optionsCloser.find(option => option.value === value)?.text ?? null,
                options: optionsCloser
            },
            trie: false,
            type: 'select',
            width: 80
        }
    ])

    async function deleted(id){
        if (!window.confirm('Voulez-vous vraiment supprimer cette commande fournisseur ?')) return
        await storeSupplierOrderList.remove(id)
        await refreshTable()
    }

    async function getPage(nPage){
        supplierOrderListCriteria.gotoPage(parseFloat(nPage))
        await storeSupplierOrderList.fetch(supplierOrderListCriteria.getFetchCriteria)
    }

    async function search(inputValues) {
        //console.log('search', inputValues)
        supplierOrderListCriteria.resetAllFilter()
        supplierOrderListCriteria.addFilter('company', currentCompany)
        if (inputValues.ref) supplierOrderListCriteria.addFilter('ref', inputValues.ref)
        if (inputValues.id) supplierOrderListCriteria.addFilter('id', inputValues.id)
        if (inputValues.state) supplierOrderListCriteria.addFilter('embState.state[]', inputValues.state)
        if (inputValues.closer) supplierOrderListCriteria.addFilter('embBlocker.state[]', inputValues.closer)
        if (inputValues.supplier) supplierOrderListCriteria.addFilter('supplier', inputValues.supplier)
        if (inputValues.deliveryCompany) supplierOrderListCriteria.addFilter('deliveryCompany', inputValues.deliveryCompany)
        if (inputValues.kind) supplierOrderListCriteria.addFilter('kind', inputValues.kind)
        if (inputValues.orderFamily) supplierOrderListCriteria.addFilter('orderFamily', inputValues.orderFamily)
        await storeSupplierOrderList.fetch(supplierOrderListCriteria.getFetchCriteria)
    }
    async function cancelSearch() {
        supplierOrderListCriteria.resetAllFilter()
        supplierOrderListCriteria.addFilter('company', currentCompany)
        await storeSupplierOrderList.fetch(supplierOrderListCriteria.getFetchCriteria)
    }

    async function trierAlphabet(payload) {
        supplierOrderListCriteria.addSort(payload.name, payload.direction)
        await storeSupplierOrderList.fetch(supplierOrderListCriteria.getFetchCriteria)
    }
    function onCreatedNewSupplierOrder() {
        refreshTable()
        if (supplierOrderCreateModal.value) {
            const modalElement = supplierOrderCreateModal.value.$el
            const bootstrapModal = Modal.getInstance(modalElement)
            bootstrapModal.hide()
        }
    }
    function onSupplierOrderDetailsOpenRequest(supplierOrder) {
        /* eslint-disable camelcase */
        router.push({name: 'supplier-order-show', params: {id: supplierOrder.id}})
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
        <AppSupplierOrderCreate ref="supplierOrderCreateModal" :modal-id="modalId" title="Création nouvelle commande fournisseur" :target="target" @created="onCreatedNewSupplierOrder"/>
        <div class="col">
            <AppSuspense>
                <AppCardableTable
                    :current-page="storeSupplierOrderList.currentPage.toString()"
                    :fields="fields"
                    :first-page="storeSupplierOrderList.firstPage.toString()"
                    :items="itemsTable"
                    :last-page="storeSupplierOrderList.lastPage"
                    :next-page="storeSupplierOrderList.nextPage"
                    :pag="storeSupplierOrderList.pagination"
                    :previous-page="storeSupplierOrderList.previousPage"
                    :user="userRole"
                    :should-delete="canDelete"
                    top-offset="48px"
                    form="formSupplierOrderCardableTable"
                    @deleted="deleted"
                    @get-page="getPage"
                    @trier-alphabet="trierAlphabet"
                    @search="search"
                    @update="onSupplierOrderDetailsOpenRequest"
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
