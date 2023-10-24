<script setup>
    import {computed, ref} from 'vue-demi'
    import useFetchCriteria from '../../../../stores/fetch-criteria/fetchCriteria'
    import AppSupplierCreate from './AppSupplierCreate.vue'
    import AppSuspense from '../../../AppSuspense.vue'
    // import useCountries from '../../../stores/countries/countries'
    import {useSuppliersStore} from '../../../../stores/purchase/supplier/suppliers'
    import useUser from '../../../../stores/security'
    import Fa from '../../../Fa'
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
    // let violations = []
    // let success = []
    // const isPopupVisible = ref(false)
    // const isCreatedPopupVisible = ref(false)

    const storeSuppliersList = useSuppliersStore()
    await storeSuppliersList.fetch()

    const supplierListCriteria = useFetchCriteria('supplier-list-criteria')
    supplierListCriteria.addFilter('company', currentCompany)
    async function refreshTable() {
        await storeSuppliersList.fetch(supplierListCriteria.getFetchCriteria)
    }
    await refreshTable()

    const itemsTable = computed(() => storeSuppliersList.itemsSuppliers)
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

    // const generalForm = {}
    // const qualityForm = {}
    // const comptabilityForm = {}
    // const cuivreForm = {}

    // async function generalFormInput(generalData) {
    //     generalForm.value = generalData
    // }
    // async function qualityFormInput(qualityData) {
    //     qualityForm.value = qualityData
    // }
    // async function comptabilityFormInput(comptabilityData) {
    //     comptabilityForm.value = comptabilityData
    // }
    // async function cuivreFormInput(cuivreData) {
    //     cuivreForm.value = cuivreData
    // }
    // async function supplierFormCreate(){
    //     try {
    //         const supplier = {
    //             address: {
    //                 address: generalForm?.value?.address || '',
    //                 address2: generalForm?.value?.address2 || '',
    //                 city: generalForm?.value?.city || '',
    //                 country: 'FR',
    //                 // "country":  generalForm?.value?.value?.country || '',
    //                 email: generalForm?.value?.email || '',
    //                 phoneNumber: generalForm?.value?.phoneNumber || '',
    //                 zipCode: generalForm?.value?.zipCode || ''
    //             },
    //             administeredBy: generalForm?.value?.administeredBy || '',
    //             confidenceCriteria: qualityForm?.value?.confidenceCriteria || 0,
    //             copper: {
    //                 index: {
    //                     code: cuivreForm?.value?.copperType || '',
    //                     value: cuivreForm?.value?.copperIndex || 0
    //                 },
    //                 last: cuivreForm?.value?.last || null,
    //                 managed: cuivreForm?.value?.managed || false,
    //                 next: cuivreForm?.value?.next || null,
    //                 type: cuivreForm?.value?.type || ''
    //             },
    //             currency: comptabilityForm?.value?.currency || '',
    //             managedProduction: qualityForm?.value?.managedProduction || false,
    //             managedQuality: qualityForm?.value?.managedQuality || false,
    //             name: generalForm?.value?.name || '',
    //             openOrdersEnabled: comptabilityForm?.value?.value?.openOrdersEnabled || false,
    //             society: generalForm?.value?.society || ''
    //         }
    //         console.log('supplier', supplier)
    //         await storeSuppliersList.addSupplier(supplier)
    //         isPopupVisible.value = false
    //         isCreatedPopupVisible.value = true
    //         success = 'Fournisseur crée'
    //     } catch (error) {
    //         violations = error
    //         isPopupVisible.value = true
    //         isCreatedPopupVisible.value = false
    //         console.log('violations', violations)
    //     }
    // }
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

        <!-- <AppModal :id="modalId" class="four" :title="title">
            <AppSupplierCreate :success="success" :is-created-popup-visible="isCreatedPopupVisible" :is-popup-visible="isPopupVisible" :violations="violations" @general-data="generalFormInput" @quality-data="qualityFormInput" @comptability-data="comptabilityFormInput" @cuivre-data="cuivreFormInput"/>
            <template #buttons>
                <AppBtn
                    variant="success"
                    label="Créer"
                    data-bs-toggle="modal"
                    :data-bs-target="target"
                    @click="supplierFormCreate">
                    Créer
                </AppBtn>
            </template>
        </AppModal> -->
        <div class="col">
            <AppSuspense>
                <AppCardableTable
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
