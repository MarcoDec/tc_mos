<script setup>
    import {computed, ref} from 'vue-demi'
    import useFetchCriteria from '../../../../stores/fetch-criteria/fetchCriteria'
    import AppCustomerCreate from './AppCustomerCreate.vue'
    import AppSuspense from '../../../AppSuspense.vue'
    import {useCustomersStore} from '../../../../stores/customer/customers'
    import useUser from '../../../../stores/security'

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

    const storeCustomersList = useCustomersStore()
    await storeCustomersList.fetch()

    const customerListCriteria = useFetchCriteria('customer-list-criteria')
    customerListCriteria.addFilter('company', currentCompany)
    async function refreshTable() {
        await storeCustomersList.fetch(customerListCriteria.getFetchCriteria)
    }
    await refreshTable()

    const itemsTable = computed(() => storeCustomersList.itemsCustomers)
    const optionsEtat = [
        {text: 'agreed', value: 'agreed'},
        {text: 'draft', value: 'draft'},
        {text: 'to_validate', value: 'to_validate'},
        {text: 'warning', value: 'warning'}
    ]

    const fields = computed(() => [
        {label: 'Nom', name: 'name', trie: true, type: 'text'},
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
        await storeCustomersList.remove(id)
        await refreshTable()
    }

    async function getPage(nPage){
        customerListCriteria.gotoPage(parseFloat(nPage))
        await storeCustomersList.fetch(customerListCriteria.getFetchCriteria)
    }

    async function search(inputValues) {
        customerListCriteria.resetAllFilter()
        customerListCriteria.addFilter('company', currentCompany)
        if (inputValues.name) customerListCriteria.addFilter('name', inputValues.name)
        if (inputValues.state) customerListCriteria.addFilter('embState.state[]', inputValues.state)
        await storeCustomersList.fetch(customerListCriteria.getFetchCriteria)
    }
    async function cancelSearch() {
        customerListCriteria.resetAllFilter()
        customerListCriteria.addFilter('company', currentCompany)
        await storeCustomersList.fetch(customerListCriteria.getFetchCriteria)
    }

    async function trierAlphabet(payload) {
        customerListCriteria.addSort(payload.name, payload.direction)
        await storeCustomersList.fetch(customerListCriteria.getFetchCriteria)
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
        <AppCustomerCreate :modal-id="modalId" title="Création nouveau client" :target="target"/>
        <div class="col">
            <AppSuspense>
                <AppCardableTable
                    :current-page="storeCustomersList.currentPage"
                    :fields="fields"
                    :first-page="storeCustomersList.firstPage"
                    :items="itemsTable"
                    :last-page="storeCustomersList.lastPage"
                    :next-page="storeCustomersList.nextPage"
                    :pag="storeCustomersList.pagination"
                    :previous-page="storeCustomersList.previousPage"
                    :user="roleuser"
                    form="formCustomerCardableTable"
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
