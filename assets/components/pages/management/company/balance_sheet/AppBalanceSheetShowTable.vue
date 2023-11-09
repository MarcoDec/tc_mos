<script setup>
    import {computed, defineProps, onBeforeMount, ref} from 'vue'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import useBalanceSheetItemStore from '../../../../../stores/management/balance-sheets/balanceSheetItems'
    import useUser from '../../../../../stores/security'
    import AppSuspense from '../../../../AppSuspense.vue'
    import Fa from '../../../../Fa'
    import AppFormCardable from '../../../../form-cardable/AppFormCardable'
    import {useCookies} from '@vueuse/integrations/useCookies'
    const token = useCookies(['token']).get('token')
    const props = defineProps({
        addForm: {required: true, type: Boolean},
        title: {required: true, type: String},
        idBalanceSheet: {required: true, type: Number},
        tabFields: {required: true, type: Array},
        tabId: {required: true, type: String},
        paymentCategory: {required: true, type: String},
        defaultFormValues: {required: true, type: Object}
    })

    const fetchUser = useUser()
    const isWriterOrAdmin = fetchUser.isManagementWriter || fetchUser.isManagementAdmin
    const roleuser = ref(isWriterOrAdmin ? 'writer' : 'reader')
    const fetchBalanceSheetItems = useBalanceSheetItemStore(props.paymentCategory)
    const balanceSheetItemsCriteria = useFetchCriteria(`balanceSheetItems-criteria-${props.tabId}`)
    const formData = ref(props.defaultFormValues)
    function addPermanentFilter() {
        balanceSheetItemsCriteria.addFilter('balanceSheet', `/api/balance-sheets/${props.idBalanceSheet}`)
        balanceSheetItemsCriteria.addFilter('paymentCategory', props.paymentCategory)
    }
    addPermanentFilter()
    function addPermanentFields(){
        formData.value.balanceSheet = `/api/balance-sheets/${props.idBalanceSheet}`
        formData.value.paymentCategory = props.paymentCategory
    }
    const itemsTable = computed(() => fetchBalanceSheetItems.items)
    async function refreshTable() {
        console.log('refreshTable')
        await fetchBalanceSheetItems.fetch(balanceSheetItemsCriteria.getFetchCriteria)
    }
    const getId = /.*?\/(\d+)/

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
        if (inputValues.paymentRef) balanceSheetItemsCriteria.addFilter('paymentRef', inputValues.paymentRef)
        if (inputValues.stakeholder) balanceSheetItemsCriteria.addFilter('stakeholder', inputValues.stakeholder)
        if (inputValues.label) balanceSheetItemsCriteria.addFilter('label', inputValues.label)
        if (inputValues.paymentMethod) balanceSheetItemsCriteria.addFilter('paymentMethod', inputValues.paymentMethod)
        if (inputValues.subCategory) balanceSheetItemsCriteria.addFilter('subCategory', inputValues.subCategory)
        if (inputValues.billDate) balanceSheetItemsCriteria.addFilter('billDate', inputValues.billDate)
        if (inputValues.paymentDate) balanceSheetItemsCriteria.addFilter('paymentDate', inputValues.paymentDate)
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
    const AddForm = ref(false)
    let addFormKey = 0
    const isPopupVisible = ref(false)
    const fieldsForm = ref({})
    let violations = []
    onBeforeMount(() => {
        fieldsForm.value = props.tabFields
    })
    function input(v){
        console.log('input', v)
    }
    const addFormName = `addItemForm_${props.tabId}`

    function ajoutItem(){
        const addForm = document.getElementById(addFormName)
        const formDataAddItem = new FormData(addForm)
        console.log('ajoutItem', formDataAddItem.get('paymentDate'))
        fetch('/api/balance-sheet-items', {
            headers: {
                Authorization: `Bearer ${token}`
            },
            method: 'POST',
            body: formDataAddItem
        }).then(response => {
            console.log('response', response)
        })
        // addPermanentFields()
        // if (typeof formData.value.file !== 'undefined' && formData.value.file !== null) formData.value.file = [formData.value.file]
        // else formData.value.file = null
        // violations = await fetchBalanceSheetItems.add({data: formData.value})
        // if (typeof violations !== 'undefined' && violations.length > 0){
        //     isPopupVisible.value = true
        // } else {
        //     AddForm.value = false
        //     isPopupVisible.value = false
        //     await refreshTable() //On recharge les données
        // }
    }
    function annule(){
        AddForm.value = false
        const itemsNull = {
            families: null
        }
        formData.value = itemsNull
        isPopupVisible.value = false
    }
</script>

<template>
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
                        :min="AddForm"
                        :next-page="fetchBalanceSheetItems.nextPage"
                        :pag="fetchBalanceSheetItems.pagination"
                        :previous-page="fetchBalanceSheetItems.previousPage"
                        :title="title"
                        :user="roleuser"
                        :form="`updateItemForm${tabId}`"
                        @update="update"
                        @deleted="deleteTableItem"
                        @get-page="getPage"
                        @trier-alphabet="trierAlphabet"
                        @search="search"
                        @cancel-search="cancelSearch">
                        <template #title>
                            {{ title }}
                            <AppBtn v-if="addForm" class="btn-float-right" label="Ajout" variant="success" size="sm" @click="AddForm = !AddForm">
                                <Fa icon="plus"/> Ajouter
                            </AppBtn>
                        </template>
                    </AppCardableTable>
                </AppSuspense>
            </div>
            <div v-show="AddForm" :key="addFormKey" class="col-6">
                <AppSuspense>
                    <AppCard class="bg-blue col" title="">
                        <div class="row">
                            <button id="btnRetour1" class="btn btn-danger btn-icon btn-sm col-1" @click="annule">
                                <Fa icon="angle-double-left"/>
                            </button>
                            <h4 class="col">
                                <Fa icon="square-plus"/> Ajouter un nouvel élément
                            </h4>
                        </div>
                        <AppSuspense>
                            <AppFormCardable :id="addFormName" :model-value="formData" :fields="fieldsForm" label-cols @update:model-value="input"/>
                        </AppSuspense>
                        <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                            <div v-for="violation in violations" :key="violation">
                                <li>{{ violation.message }}</li>
                            </div>
                        </div>
                        <div class="btnright row">
                            <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutItem">
                                <Fa icon="plus"/> Enregister
                            </AppBtn>
                        </div>
                    </AppCard>
                </AppSuspense>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .tableau {
        min-height: 300px;
    }
</style>
