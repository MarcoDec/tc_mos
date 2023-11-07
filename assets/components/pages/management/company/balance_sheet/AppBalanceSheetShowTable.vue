<script setup>
    import {computed, defineProps, onBeforeMount, ref} from 'vue'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import {useBalanceSheetItemStore} from '../../../../../stores/management/balance-sheets/balanceSheetItems'
    import useUser from '../../../../../stores/security'
    import AppSuspense from '../../../../AppSuspense.vue'
    import Fa from '../../../../Fa'
    import AppFormCardable from '../../../../form-cardable/AppFormCardable'
    const props = defineProps({
        addForm: {required: true, type: Boolean},
        title: {required: true, type: String},
        idBalanceSheet: {required: true, type: Number},
        tabFields: {required: true, type: Array},
        tabId: {required: true, type: String},
        paymentCategory: {required: true, type: String}
    })

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
    const AddForm = ref(false)
    let addFormKey = 0
    const isPopupVisible = ref(false)
    const formData = ref({})
    const fieldsForm = ref({})
    let violations = []
    onBeforeMount(() => {
        fieldsForm.value = props.tabFields
    })
    function input(v){
        formData.value = v
        if (v.families && v.families.includes('camion')) {
            //on s'assure que fieldsForm contient la définition du champ destination
            if (typeof fieldsForm.value.find(item => item.name === 'destination') === 'undefined') {
                fieldsForm.value.push(destinationField)
                addFormKey++
            }
        } else if (typeof fieldsForm.value.find(item => item.name === 'destination') !== 'undefined') {
            fieldsForm.value.pop()
            formData.value.destination = null
            addFormKey++
        }
    }

    async function ajoutItem(){
        formData.value.file = [formData.value.file]
        violations = await fetchBalanceSheetItems.add(formData.value)
        if (violations.length > 0){
            isPopupVisible.value = true
        } else {
            AddForm.value = false
            isPopupVisible.value = false
            await refreshTable() //On recharge les données
        }
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
        <div v-if="addForm" class="row">
            <div class="col">
                <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="AddForm = !AddForm">
                    <Fa icon="plus"/> Ajouter
                </AppBtn>
            </div>
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
                        :min="AddForm"
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
                            <AppFormCardable id="addItem" :model-value="formData" :fields="fieldsForm" label-cols @update:model-value="input"/>
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
