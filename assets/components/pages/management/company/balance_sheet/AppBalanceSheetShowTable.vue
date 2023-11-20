<script setup>
    import {computed, onBeforeMount, ref, toRefs} from 'vue'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import useBalanceSheetItemStore from '../../../../../stores/management/balance-sheets/balanceSheetItems'
    import useUser from '../../../../../stores/security'
    import AppSuspense from '../../../../AppSuspense.vue'
    import Fa from '../../../../Fa'
    import {useCookies} from '@vueuse/integrations/useCookies'
    import AppBalanceSheetForm from './AppBalanceSheetForm.vue'

    const {addForm, balanceSheetCurrency, title, idBalanceSheet, formFields, tabFields, tabId, paymentCategory, defaultFormValues} = toRefs(defineProps({
        addForm: {required: true, type: Boolean},
        balanceSheetCurrency: {required: true, type: String},
        title: {required: true, type: String},
        idBalanceSheet: {required: true, type: Number},
        formFields: {required: true, type: Array},
        tabFields: {required: true, type: Array},
        tabId: {required: true, type: String},
        paymentCategory: {required: true, type: String},
        defaultFormValues: {required: true, type: Object}
    }))
    //region Récupération des données des sessions
    const fetchUser = useUser()
    const token = useCookies(['token']).get('token')
    const isWriterOrAdmin = fetchUser.isManagementWriter || fetchUser.isManagementAdmin
    const roleuser = ref(isWriterOrAdmin ? 'writer' : 'reader')
    //endregion
    //region Définition des variables liées au chargement des données
    const fetchBalanceSheetItems = useBalanceSheetItemStore(paymentCategory.value)
    const balanceSheetItemsCriteria = useFetchCriteria(`balanceSheetItems-criteria-${tabId.value}`)
    function addPermanentFilter() {
        balanceSheetItemsCriteria.addFilter('balanceSheet', `/api/balance-sheets/${idBalanceSheet.value}`)
        balanceSheetItemsCriteria.addFilter('paymentCategory', paymentCategory.value)
    }
    addPermanentFilter()
    const tableKey = ref(0)
    const itemsTable = ref([])
    async function refreshTable(updateKey = true) {
        await fetchBalanceSheetItems.fetch(balanceSheetItemsCriteria.getFetchCriteria)
        itemsTable.value = fetchBalanceSheetItems.items
        if (updateKey) tableKey.value++
    }
    //endregion
    //const getId = /.*?\/(\d+)/
    //region Définition des variables et fonctions du tableau
    async function deleteTableItem(id) {
        await fetchBalanceSheetItems.remove(id)
        await refreshTable()
    }
    async function getPage(nPage){
        balanceSheetItemsCriteria.gotoPage(nPage)
        await refreshTable()
    }
    async function trierAlphabet(payload) {
        balanceSheetItemsCriteria.addSort(payload.name, payload.direction)
        await refreshTable(false) // On ne change pas la clé du tableau pour ne pas perdre le tri
    }
    async function search(inputValues) {
        balanceSheetItemsCriteria.resetAllFilter()
        addPermanentFilter()
        if (inputValues.paymentRef) balanceSheetItemsCriteria.addFilter('paymentRef', inputValues.paymentRef)
        if (inputValues.stakeholder) balanceSheetItemsCriteria.addFilter('stakeholder', inputValues.stakeholder)
        if (inputValues.label) balanceSheetItemsCriteria.addFilter('label', inputValues.label)
        if (inputValues.paymentMethod) balanceSheetItemsCriteria.addFilter('paymentMethod', inputValues.paymentMethod)
        if (inputValues.subCategory) balanceSheetItemsCriteria.addFilter('subCategory', inputValues.subCategory)
        if (inputValues.billDate) {
            balanceSheetItemsCriteria.addFilter('billDate[after]', inputValues.billDate)
            balanceSheetItemsCriteria.addFilter('billDate[before]', inputValues.billDate)
        }
        if (inputValues.paymentDate) {
            balanceSheetItemsCriteria.addFilter('paymentDate[after]', inputValues.paymentDate)
            balanceSheetItemsCriteria.addFilter('paymentDate[before]', inputValues.paymentDate)
        }
        if (inputValues.amount) {
            if (inputValues.amount.value) balanceSheetItemsCriteria.addFilter('amount.value', inputValues.amount.value)
        }
        await refreshTable()
    }
    async function cancelSearch() {
        balanceSheetItemsCriteria.resetAllFilter()
        addPermanentFilter()
        await refreshTable()
    }
    function openUpdateForm(data) {
        if (!UpdateForm.value) {
            updateFormData.value = data
            if (data.paymentDate) updateFormData.value.paymentDate = data.paymentDate.split('T')[0]
            if (data.billDate) updateFormData.value.billDate = data.billDate.split('T')[0]
            updateFormName.value = `updateForm${tabId.value}`
            formKey.value++
        }
        UpdateForm.value = !UpdateForm.value
        AddForm.value = false
        addViolations.value = []
        updateViolations.value = []
    }
    //endregion
    //region Définition des variables et fonctions de formulaires
    const addFormData = ref({})
    const AddForm = ref(false)
    const addFieldsForm = ref(tabFields.value)
    const addViolations = ref([])
    const addFormName = computed(() => `addForm${tabId.value}`)

    const updateFormData = ref({})
    const UpdateForm = ref(false)
    const updateFieldsForm = ref({})
    const updateViolations = ref([])
    const updateFormName = computed(() => `updateForm${tabId.value}`)

    const formKey = ref(0)
    function resetFormData(){
        addFormData.value = {}
        addFormData.value = defaultFormValues.value
        addViolations.value = []
        updateViolations.value = []
        updateFormData.value = {}
        updateFormData.value = defaultFormValues.value
        //region On initialise les valeurs des champs de formulaire de type measure avec la valeur de la devise de la balanceSheet parente
        formFields.value.forEach(field => {
            if (field.type === 'measure') {
                addFormData.value[field.name].code = balanceSheetCurrency.value
            }
        })
        //endregion
    }
    resetFormData()
    function showAddForm() {
        if (!AddForm.value) {
            addFormName.value = `addForm${tabId.value}`
            resetFormData()
            formKey.value++
        }
        AddForm.value = !AddForm.value
        UpdateForm.value = false
    }
    function showUpdateForm() {
        if (!UpdateForm.value) {
            updateFormName.value = `updateForm${tabId.value}`
            resetFormData()
            formKey.value++
        }
        UpdateForm.value = !UpdateForm.value
        AddForm.value = false
    }
    function addNewItem(){
        const addFormElement = document.getElementById(`form_${addFormName.value}`)
        const formDataAddItem = new FormData(addFormElement)
        formDataAddItem.append('balanceSheet', `/api/balance-sheets/${idBalanceSheet.value}`)
        formDataAddItem.append('paymentCategory', paymentCategory.value)
        fetch('/api/balance-sheet-items', {
            headers: {
                Authorization: `Bearer ${token}`
            },
            method: 'POST',
            body: formDataAddItem
        }).then(() => {
            resetFormData()
            refreshTable()
        })
    }
    function updateItem(){
        const updateFormElement = document.getElementById(`form_${updateFormName.value}`)
        const formDataUpdateItem = new FormData(updateFormElement)
        formDataUpdateItem.append('balanceSheet', `/api/balance-sheets/${idBalanceSheet.value}`)
        formDataUpdateItem.append('paymentCategory', paymentCategory.value)
        fetch(`/api/balance-sheet-items/${updateFormData.value.id}`, {
            headers: {
                Authorization: `Bearer ${token}`
            },
            method: 'POST',
            body: formDataUpdateItem
        }).then(() => {
            resetFormData()
            refreshTable()
        })
    }
    //endregion

    //endregion
    //region Chargement des données onBeforeMount()
    onBeforeMount(async () => {
        addFieldsForm.value = formFields.value
        updateFieldsForm.value = formFields.value
        await fetchBalanceSheetItems.fetch(balanceSheetItemsCriteria.getFetchCriteria)
        itemsTable.value = fetchBalanceSheetItems.items
    })
    //endregion
</script>

<template>
    <div class="container-fluid tableau">
        <div class="row">
            <div class="col">
                <AppSuspense>
                    <AppCardableTable
                        :key="tableKey"
                        :current-page="fetchBalanceSheetItems.currentPage"
                        :fields="tabFields"
                        :first-page="fetchBalanceSheetItems.firstPage"
                        :items="itemsTable"
                        :last-page="fetchBalanceSheetItems.lastPage"
                        :min="AddForm || UpdateForm"
                        :next-page="fetchBalanceSheetItems.nextPage"
                        :pag="fetchBalanceSheetItems.pagination"
                        :previous-page="fetchBalanceSheetItems.previousPage"
                        :title="title"
                        :user="roleuser"
                        :form="`updateItemForm${tabId}`"
                        @update="openUpdateForm"
                        @deleted="deleteTableItem"
                        @get-page="getPage"
                        @trier-alphabet="trierAlphabet"
                        @search="search"
                        @cancel-search="cancelSearch">
                        <template #title>
                            {{ title }}
                            <AppBtn v-if="addForm" class="btn-float-right" label="Ajout" variant="success" size="sm" @click="showAddForm">
                                <Fa icon="plus"/> Ajouter
                            </AppBtn>
                        </template>
                    </AppCardableTable>
                </AppSuspense>
            </div>
            <div v-if="AddForm || UpdateForm" class="col-6">
                <AppBalanceSheetForm
                    v-show="AddForm"
                    :id="addFormName"
                    :key="`${addFormName}-${formKey}`"
                    icon="square-plus"
                    title="Ajouter un nouvel élément"
                    :fields="addFieldsForm"
                    :form-data="addFormData"
                    :violations="addViolations"
                    @cancel="showAddForm"
                    @save="addNewItem"/>
                <AppBalanceSheetForm
                    v-show="UpdateForm"
                    :id="updateFormName"
                    :key="`${updateFormName}-${formKey}`"
                    icon="pencil"
                    title="Modifier l'élément"
                    :fields="updateFieldsForm"
                    :form-data="updateFormData"
                    :violations="updateViolations"
                    @cancel="showUpdateForm"
                    @save="updateItem"/>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .tableau {
        min-height: 300px;
    }
    .btn:hover {
        border: white 1px solid;
    }
</style>
