<script setup>
    import {computed, defineProps, onBeforeMount, ref} from 'vue'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import useBalanceSheetItemStore from '../../../../../stores/management/balance-sheets/balanceSheetItems'
    import useUser from '../../../../../stores/security'
    import AppSuspense from '../../../../AppSuspense.vue'
    import Fa from '../../../../Fa'
    import {useCookies} from '@vueuse/integrations/useCookies'
    import AppBalanceSheetForm from './AppBalanceSheetForm.vue'

    const props = defineProps({
        addForm: {required: true, type: Boolean},
        balanceSheetCurrency: {required: true, type: String},
        title: {required: true, type: String},
        idBalanceSheet: {required: true, type: Number},
        formFields: {required: true, type: Array},
        tabFields: {required: true, type: Array},
        tabId: {required: true, type: String},
        paymentCategory: {required: true, type: String},
        defaultFormValues: {required: true, type: Object}
    })
    console.log('props', props)
    //region Récupération des données des sessions
    const fetchUser = useUser()
    const token = useCookies(['token']).get('token')
    const isWriterOrAdmin = fetchUser.isManagementWriter || fetchUser.isManagementAdmin
    const roleuser = ref(isWriterOrAdmin ? 'writer' : 'reader')
    //endregion
    //region Définition des variables liées au chargement des données
    const fetchBalanceSheetItems = useBalanceSheetItemStore(props.paymentCategory)
    const balanceSheetItemsCriteria = useFetchCriteria(`balanceSheetItems-criteria-${props.tabId}`)
    function addPermanentFilter() {
        balanceSheetItemsCriteria.addFilter('balanceSheet', `/api/balance-sheets/${props.idBalanceSheet}`)
        balanceSheetItemsCriteria.addFilter('paymentCategory', props.paymentCategory)
    }
    addPermanentFilter()
    const tableKey = ref(0)
    const itemsTable = ref([])
    async function refreshTable() {
        await fetchBalanceSheetItems.fetch(balanceSheetItemsCriteria.getFetchCriteria)
        itemsTable.value = fetchBalanceSheetItems.items
        tableKey.value++
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
        await refreshTable()
    }
    async function search(inputValues) {
        console.log(inputValues)
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
    function openUpdateForm() {
        UpdateForm.value = true
        AddForm.value = false
        addViolations.value = []
        updateViolations.value = []
    }
    //endregion
    //region Définition des variables et fonctions de formulaires
    const addFormData = ref({})
    const AddForm = ref(false)
    const addFieldsForm = ref(props.tabFields)
    const addViolations = ref([])
    const addFormName = computed(() => `addForm${props.tabId}`)

    const updateFormData = ref({})
    const UpdateForm = ref(false)
    const updateFieldsForm = ref({})
    const updateViolations = ref([])
    const updateFormName = computed(() => `updateForm${props.tabId}`)

    const formKey = ref(0)
    function resetFormData(){
        addFormData.value = {}
        addFormData.value = props.defaultFormValues
        addViolations.value = []
        updateViolations.value = []
        updateFormData.value = {}
        updateFormData.value = props.defaultFormValues
        //region On initialise les valeurs des champs de formulaire de type measure avec la valeur de la devise de la balanceSheet parente
        props.formFields.forEach(field => {
            if (field.type === 'measure') {
                addFormData.value[field.name].code = props.balanceSheetCurrency
            }
        })
        //endregion
    }
    resetFormData()
    function cancel() {
        AddForm.value = false
        UpdateForm.value = false
        resetFormData()
    }
    function addNewItem(){
        const addFormElement = document.getElementById(`form_${addFormName.value}`)
        console.log('addNewItem', addFormElement)
        const formDataAddItem = new FormData(addFormElement)
        formDataAddItem.append('balanceSheet', `/api/balance-sheets/${props.idBalanceSheet}`)
        formDataAddItem.append('paymentCategory', props.paymentCategory)
        // props.formFields.forEach(field => {
        //     if (field.type === 'measure' && typeof formDataAddItem.get(`${field.name}-code`) === 'string') {
        //         formDataAddItem.append(field.name, {
        //             value: formDataAddItem.get(`${field.name}-value`),
        //             code: formDataAddItem.get(`${field.name}-code`)
        //         })
        //     }
        // })
        //console.log('ajoutItem', formDataAddItem.get('paymentDate'))
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
    function updateItem(data){
        console.log('updateItem', data)
        //const updateForm = document.getElementById(updateFormName)
        //const formDataUpdateItem = new FormData(updateForm)
        // formDataUpdateItem.append('balanceSheet', `/api/balance-sheets/${props.idBalanceSheet}`)
        // formDataUpdateItem.append('paymentCategory', props.paymentCategory)
        //console.log('ajoutItem', formDataAddItem.get('paymentDate'))
        // fetch(`/api/balance-sheet-items/${}`, {
        //     headers: {
        //         Authorization: `Bearer ${token}`
        //     },
        //     method: 'POST',
        //     body: formDataUpdateItem
        // }).then(() => {
        //     resetFormData()
        //     refreshTable()
        // })
    }
    //endregion

    //endregion
    //region Chargement des données onBeforeMount()
    onBeforeMount(async () => {
        addFieldsForm.value = props.formFields
        updateFieldsForm.value = props.formFields
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
                            <AppBtn v-if="addForm" class="btn-float-right" label="Ajout" variant="success" size="sm" @click="AddForm = !AddForm">
                                <Fa icon="plus"/> Ajouter
                            </AppBtn>
                        </template>
                    </AppCardableTable>
                </AppSuspense>
            </div>
            <div v-show="AddForm || UpdateForm" :key="formKey" class="col-6">
                <AppBalanceSheetForm
                    v-if="AddForm"
                    :id="addFormName"
                    icon="square-plus"
                    title="Ajouter un nouvel élément"
                    :fields="addFieldsForm"
                    :form-data="addFormData"
                    :violations="addViolations"
                    @cancel="cancel"
                    @save="addNewItem"/>
                <AppBalanceSheetForm
                    v-if="UpdateForm"
                    :id="updateFormName"
                    icon="pencil"
                    title="Modifier l'élément"
                    :fields="updateFieldsForm"
                    form-data="updateFormData"
                    :violations="updateViolations"
                    @cancel="cancel"
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
