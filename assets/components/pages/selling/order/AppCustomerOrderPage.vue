<script setup>
    import {computed, onBeforeMount, onBeforeUpdate, ref} from 'vue'
    import useFetchCriteria from '../../../../stores/fetch-criteria/fetchCriteria'
    import AppSuspense from '../../../AppSuspense.vue'

    import {useCustomerOrderStore} from '../../../../stores/customer/customerOrder'
    import useUser from '../../../../stores/security'
    import {Modal} from 'bootstrap'
    import {useRouter} from 'vue-router'
    import AppCustomerOrderCreate from './AppCustomerOrderCreate.vue'

    defineProps({
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })

    const modalId = computed(() => 'target')
    const target = computed(() => `#${modalId.value}`)
    const customerOrderCreateModal = ref(null)

    const fetchUser = useUser()
    const router = useRouter()
    const storeCustomerOrderList = useCustomerOrderStore()
    const customerOrderListCriteria = useFetchCriteria('customer-order-list-criteria')
    const currentCompany = fetchUser.company
    customerOrderListCriteria.addFilter('company', currentCompany)

    const isSellingWriterOrAdmin = fetchUser.isSellingWriter || fetchUser.isSellingAdmin
    const userRole = ref(isSellingWriterOrAdmin ? 'writer' : 'reader')

    async function refreshTable() {
        await storeCustomerOrderList.fetch(customerOrderListCriteria.getFetchCriteria)
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
    const itemsTable = computed(() => storeCustomerOrderList.customerOrders)
    const optionsEtat = [
        {text: 'Brouillon', value: 'draft'},
        {text: 'En attente de validation', value: 'to_validate'},
        {text: 'Approuvée', value: 'agreed'},
        {text: 'Livrée partiellement', value: 'partially_delivered'},
        {text: 'Livrée', value: 'delivered'},
        {text: 'Soldé', value: 'paid'}
    ]
    const optionsCloser = [
        {text: 'Actif', value: 'enabled'},
        {text: 'Bloqué', value: 'blocked'},
        {text: 'Clos', value: 'closed'}
    ]
    const fields = computed(() => [
        {label: 'ID', name: 'id', trie: true, type: 'text', width: 50, filter: true},
        {label: 'Référence', name: 'ref', trie: true, type: 'text', width: 80},
        {
            label: 'Client',
            name: 'customer',
            trie: false,
            type: 'multiselect-fetch',
            api: '/api/customers',
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
            label: 'Etat',
            name: 'state',
            options: {
                label: value =>
                    optionsEtat.find(option => option.value === value)?.text ?? null,
                options: optionsEtat
            },
            trie: false,
            type: 'select',
            width: 120,
            sourceName: 'embState.state'
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
            width: 80,
            sourceName: 'embBlocker.state'
        }
    ])

    async function deleted(id){
        await storeCustomerOrderList.remove(id)
        await refreshTable()
    }

    async function getPage(nPage){
        customerOrderListCriteria.gotoPage(parseFloat(nPage))
        await storeCustomerOrderList.fetch(customerOrderListCriteria.getFetchCriteria)
    }

    async function search(inputValues) {
        //console.log('search', inputValues)
        customerOrderListCriteria.resetAllFilter()
        customerOrderListCriteria.addFilter('company', currentCompany)
        if (inputValues.ref) customerOrderListCriteria.addFilter('ref', inputValues.ref)
        if (inputValues.id) customerOrderListCriteria.addFilter('id', inputValues.id)
        if (inputValues.state) customerOrderListCriteria.addFilter('embState.state[]', inputValues.state)
        if (inputValues.closer) customerOrderListCriteria.addFilter('embBlocker.state[]', inputValues.closer)
        if (inputValues.customer) customerOrderListCriteria.addFilter('customer', inputValues.customer)
        if (inputValues.orderFamily) customerOrderListCriteria.addFilter('orderFamily', inputValues.orderFamily)
        await storeCustomerOrderList.fetch(customerOrderListCriteria.getFetchCriteria)
    }
    async function cancelSearch() {
        customerOrderListCriteria.resetAllFilter()
        customerOrderListCriteria.addFilter('company', currentCompany)
        await storeCustomerOrderList.fetch(customerOrderListCriteria.getFetchCriteria)
    }

    async function trierAlphabet(payload) {
        customerOrderListCriteria.addSort(payload.name, payload.direction)
        await storeCustomerOrderList.fetch(customerOrderListCriteria.getFetchCriteria)
    }
    function onCreatedNewCustomerOrder() {
        refreshTable()
        if (customerOrderCreateModal.value) {
            const modalElement = customerOrderCreateModal.value.$el
            const bootstrapModal = Modal.getInstance(modalElement)
            bootstrapModal.hide()
        }
    }
    function onCustomerOrderDetailsOpenRequest(customerOrder) {
        //console.log('onCustomerDetailsOpenRequest', customerOrder)
        /* eslint-disable camelcase */
        router.push({name: 'customer-order-show', params: {id: customerOrder.id}})
    }
</script>

<template>
    <div class="row">
        <div class="col">
            <h1>
                <Fa :icon="icon"/>
                {{ title }}
                <span v-if="isSellingWriterOrAdmin" class="btn-float-right">
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
        <AppCustomerOrderCreate ref="customerOrderCreateModal" :modal-id="modalId" title="Création nouvelle commande client" :target="target" @created="onCreatedNewCustomerOrder"/>
        <div class="col">
            <AppSuspense>
                <AppCardableTable
                    :current-page="storeCustomerOrderList.currentPage.toString()"
                    :current-filter-and-sort-iri="`/api/selling-orders${customerOrderListCriteria.getFetchCriteriaWithoutPage}`"
                    :can-export-table="fetchUser.isSellingAmin || fetchUser.isSellingWriter"
                    :fields="fields"
                    :first-page="storeCustomerOrderList.firstPage.toString()"
                    :items="itemsTable"
                    :last-page="storeCustomerOrderList.lastPage"
                    :next-page="storeCustomerOrderList.nextPage"
                    :pag="storeCustomerOrderList.pagination"
                    :previous-page="storeCustomerOrderList.previousPage"
                    :user="userRole"
                    top-offset="48px"
                    form="formCustomerOrderCardableTable"
                    @deleted="deleted"
                    @get-page="getPage"
                    @trier-alphabet="trierAlphabet"
                    @search="search"
                    @update="onCustomerOrderDetailsOpenRequest"
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
