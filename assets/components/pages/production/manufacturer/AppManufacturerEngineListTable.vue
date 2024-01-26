<script setup>
    import {computed, ref} from 'vue'
    import AppFormCardable from '../../../form-cardable/AppFormCardable'
    import AppSuspense from '../../../AppSuspense.vue'
    import AppTabFichiers from '../../../tab/AppTabFichiers.vue'
    import {useEngineTypeStore} from '../../../../stores/production/engine/type/engineTypes'
    import useFetchCriteria from '../../../../stores/fetch-criteria/fetchCriteria'
    import {
        useManufacturerEngineAttachmentStore
    } from '../../../../stores/production/engine/manufacturer-engine/manufacturerEngineAttachements'
    import {
        useManufacturerEngineStore
    } from '../../../../stores/production/engine/manufacturer-engine/manufacturerEngines'
    import useOptions from '../../../../stores/option/options'

    defineProps({
        title: {required: true, type: String}
    })

    const key = ref(0)
    //region récupération des types de machine
    const fetchEngineTypes = useEngineTypeStore()
    const optionsEngineTypes = fetchEngineTypes.engineTypes
    //endregion
    const fetchManufacturerOptions = useOptions('manufacturers')
    fetchManufacturerOptions.fetchable = true
    await fetchManufacturerOptions.fetch()
    const fetchManufacturerAttachmentStore = useManufacturerEngineAttachmentStore()
    const optionsManufacturer = computed(() =>
        fetchManufacturerOptions.options.map(op => {
            const text = op.text
            const value = op['@id']
            return {text, value}
        }))
    const tableCriteria = useFetchCriteria('manufacturerEngines')
    const roleuser = ref('reader')
    const AddForm = ref(false)
    const updated = ref(false)
    const storeManufacturerEnginers = useManufacturerEngineStore()
    await storeManufacturerEnginers.fetchAll()
    const formData = new FormData()
    const violations = ref([])
    const itemId = ref(null)
    const isPopupVisible = ref(false)
    const manufacturerOptions = {
        label: value =>
            optionsManufacturer.value.find(option => option.value === value)?.text
            ?? null,
        options: optionsManufacturer.value
    }
    const tabFields = [
        {label: 'Code', min: true, name: 'code', searchDisabled: true, trie: false, type: 'text'},
        {
            label: 'Type',
            min: true,
            name: 'type',
            options: {
                label: value =>
                    optionsEngineTypes.find(option => option.value === value)?.text
                    ?? null,
                options: optionsEngineTypes
            },
            searchDisabled: true,
            trie: false,
            type: 'select'
        },
        {
            label: 'Fabriquant',
            min: true,
            name: 'manufacturer',
            options: manufacturerOptions,
            sortName: 'manufacturer',
            trie: true,
            type: 'select'
        },
        {label: 'Référence Produit', min: true, name: 'partNumber', trie: true, type: 'text'},
        {label: 'Nom', min: false, name: 'name', trie: true, type: 'text'}
    ]
    const addFormfields = [
        {
            label: 'Type',
            min: true,
            name: 'type',
            options: {
                label: value =>
                    optionsEngineTypes.find(option => option.value === value)?.text
                    ?? null,
                options: optionsEngineTypes
            },
            searchDisabled: true,
            trie: false,
            type: 'select'
        },
        {
            label: 'Fabriquant',
            min: true,
            name: 'manufacturer',
            options: manufacturerOptions,
            sortName: 'manufacturer',
            trie: false,
            type: 'select'
        },
        {label: 'Référence Produit', min: true, name: 'partNumber', trie: true, type: 'text'},
        {label: 'Nom', min: true, name: 'name', trie: true, type: 'text'}
    ]
    async function refreshList() {
        const criteria = tableCriteria.getFetchCriteria
        await storeManufacturerEnginers.fetchAll(criteria)
    }
    function showAddForm(){
        // On vide le formulaire avant de l'afficher
        formData.value = {
            code: null,
            manufacturer: null,
            name: null,
            partNumber: null,
            type: null
        }
        AddForm.value = true
        updated.value = false
    }
    async function addNewItem(){
        const form = document.getElementById('add-new-engine')
        const formData1 = new FormData(form)
        const itemsAddData = {
            code: formData1.get('code'),
            manufacturer: formData1.get('manufacturer'),
            name: formData1.get('name'),
            partNumber: formData1.get('partNumber'),
            type: formData1.get('type')
        }
        await storeManufacturerEnginers.create(itemsAddData)
        AddForm.value = false
        updated.value = false
    }
    function hideForm(){
        AddForm.value = false
        updated.value = false
        // isPopupVisible.value = false
    }
    async function showUpdateForm(item) {
        itemId.value = Number(item['@id'].match(/\d+/)[0])
        await storeManufacturerEnginers.fetchOne(itemId.value)
        const engine = storeManufacturerEnginers.engine
        formData.value = {
            code: engine.code,
            manufacturer: engine.manufacturer ? engine.manufacturer['@id'] : null,
            name: engine.name,
            partNumber: engine.partNumber,
            type: engine.type
        }
        key.value++
        updated.value = true
        AddForm.value = true
    }
    async function updateItem(){
        try {
            const form = document.getElementById('update-engine')
            const formData2 = new FormData(form)
            const itemsUpdateData = {
                code: formData2.get('code'),
                manufacturer: formData2.get('manufacturer'),
                name: formData2.get('name'),
                partNumber: formData2.get('partNumber'),
                type: formData2.get('type')
            }
            await storeManufacturerEnginers.update(itemsUpdateData)
            AddForm.value = false
            updated.value = false
            isPopupVisible.value = false
            await refreshList()
        } catch (error) {
            violations.value = error
            isPopupVisible.value = true
        }
    }
    async function deleted(id){
        await storeManufacturerEnginers.remove(id)
        await refreshList()
    }

    //region ## fonctions Recherche, Tri et pagination
    async function getPage(nPage){
        tableCriteria.gotoPage(nPage)
        await refreshList()
    }
    async function trier(payload) {
        if (payload.name === 'manufacturer') tableCriteria.addSort('manufacturer.name', payload.direction)
        else tableCriteria.addSort(payload.name, payload.direction)
        await refreshList()
    }
    async function search(inputValues) {
        const result = Object.keys(inputValues).map(cle => ({field: cle, value: inputValues[cle]}))
        result.forEach(filter => {
            tableCriteria.addFilter(filter.field, filter.value)
        })
        await refreshList()
    }
    async function cancelSearch() {
        tableCriteria.resetAllFilter()
        await refreshList()
    }
    //endregion
</script>

<template>
    <div class="container">
        <div class="row">
            <h1 class="col">
                <img src="/img/production/icons8-usine-48.png"/>
                {{ title }}
            </h1>
            <span class="col">
                <AppBtn variant="success" label="Ajout" class="btn-float-right" @click="showAddForm">
                    <Fa icon="plus"/>
                    Ajouter
                </AppBtn>
            </span>
        </div>
        <div class="row">
            <div class="col">
                <AppSuspense>
                    <AppCardableTable
                        :current-page="storeManufacturerEnginers.currentPage"
                        :fields="tabFields"
                        :first-page="storeManufacturerEnginers.view['hydra:first']"
                        :items="storeManufacturerEnginers.engines"
                        :last-page="storeManufacturerEnginers.view['hydra:last']"
                        :min="AddForm"
                        :next-page="storeManufacturerEnginers.view['hydra:next']"
                        :pag="storeManufacturerEnginers.pagination"
                        :previous-page="storeManufacturerEnginers.view['hydra:previous']"
                        :user="roleuser"
                        form="formSocietyCardableTable"
                        @cancel-search="cancelSearch"
                        @deleted="deleted"
                        @get-page="getPage"
                        @search="search"
                        @trier-alphabet="trier"
                        @update="showUpdateForm"/>
                </AppSuspense>
            </div>
            <div v-if="AddForm && !updated" class="col">
                <AppSuspense>
                    <AppCard class="bg-blue col" title="">
                        <div class="row">
                            <button id="btnRetour1" class="btn btn-danger btn-icon btn-sm col-1" @click="hideForm">
                                <Fa icon="angle-double-left"/>
                            </button>
                            <h4 class="col">
                                <Fa icon="plus"/> Ajout
                            </h4>
                        </div>
                        <br/>
                        <AppFormCardable id="add-new-engine" :fields="addFormfields" :model-value="formData" label-cols/>
                        <!-- On ne permet pas l'ajout de pièce jointe lors du formulaire de création afin de simplifier le traitement -->
                        <div class="col">
                            <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="addNewItem">
                                <!-- -->
                                <Fa icon="plus"/> Ajouter
                            </AppBtn>
                        </div>
                    </AppCard>
                </AppSuspense>
            </div>
            <div v-else-if="AddForm && updated" class="col">
                <AppSuspense>
                    <AppCard class="bg-blue col" title="">
                        <div class="col">
                            <button id="btnRetour2" class="btn btn-danger btn-icon btn-sm col-1" @click="hideForm">
                                <Fa icon="angle-double-left"/>
                            </button>
                            <h4 class="col">
                                <Fa icon="pencil-alt"/> Modification
                            </h4>
                        </div>
                        <br/>
                        <AppFormCardable id="update-engine" :key="key" :fields="addFormfields" :model-value="formData.value" :violations="violations"/>
                        <AppSuspense>
                            <AppTabFichiers
                                :key="`attachment_${key}`"
                                attachment-element-label="engine"
                                :element-api-url="`/api/manufacturer-engines/${storeManufacturerEnginers.engine.id}`"
                                :element-attachment-store="fetchManufacturerAttachmentStore"
                                :element-id="storeManufacturerEnginers.engine.id"
                                element-parameter-name="ENGINE_ATTACHMENT_CATEGORIES"
                                :element-store="useManufacturerEngineStore"/>
                        </AppSuspense>
                        <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                            <div v-for="violation in violations" :key="violation">
                                <li>{{ violation.message }}</li>
                            </div>
                        </div>
                        <div class="col">
                            <AppBtn class="btn-float-right" label="retour" variant="success" size="sm" @click="updateItem">
                                <Fa icon="pencil-alt"/> Modifier
                            </AppBtn>
                        </div>
                    </AppCard>
                </AppSuspense>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .btn-float-right{
        float: right;
    }
</style>
