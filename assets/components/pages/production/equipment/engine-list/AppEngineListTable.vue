<script setup>
    console.log()
    // import ref from 'vue'
    // // import {computed, ref} from 'vue'
    // import AppFormCardable from '../../../../form-cardable/AppFormCardable'
    // import AppTabFichiers from '../../../../tab/AppTabFichiers.vue'
    // import {
    //     useEngineStore
    // } from '../../../../../stores/production/engine/engines'
    // import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    // import useOptions from '../../../../../stores/option/options'

    // defineProps({
    //     title: {required: true, type: String}
    // })

    // const fetchManufacturerOptions = useOptions('manufacturers')
    // fetchManufacturerOptions.fetchable = true
    // await fetchManufacturerOptions.fetch()
    // // const optionsManufacturer = computed(() =>
    // //     fetchManufacturerOptions.options.map(op => {
    // //         const text = op.text
    // //         const value = op['@id']
    // //         return {text, value}
    // //     }))
    // // const key = ref(0)
    // const tableCriteria = useFetchCriteria('manufacturerEngines')
    // const roleuser = ref('reader')
    // const AddForm = ref(false)
    // const updated = ref(false)
    // const storeEngines = useEngineStore()
    // await storeEngines.fetchAll()
    // const formData = new FormData()
    // // const violations = ref([])
    // // const itemId = ref(null)
    // // const isPopupVisible = ref(false)
    // const tabFields = [
    //     // {
    //     //     label: 'Fabriquant',
    //     //     min: true,
    //     //     name: 'manufacturer',
    //     //     options: {
    //     //         label: value =>
    //     //             optionsManufacturer.value.find(option => option.type === value)?.text
    //     //             ?? null,
    //     //         options: optionsManufacturer.value
    //     //     },
    //     //     sortName: 'manufacturer',
    //     //     trie: false,
    //     //     type: 'select'
    //     // },
    //     {label: 'Type', min: true, name: '@type', trie: true, type: 'text'},
    //     {label: 'Code', min: false, name: 'code', trie: true, type: 'text'},
    //     {label: 'Nom', min: true, name: 'name', trie: true, type: 'text'}
    // ]
    // //const addFormfields = tabFields
    // async function refreshList() {
    //     const criteria = tableCriteria.getFetchCriteria
    //     await storeEngines.fetchAll(criteria)
    // }
    // function showAddForm(){
    //     // On vide le formulaire avant de l'afficher
    //     formData.value = {
    //         code: null,
    //         manufacturer: null,
    //         name: null,
    //         partNumber: null
    //     }
    //     AddForm.value = true
    //     updated.value = false
    // }
    // // async function addNewItem(){
    // //     const form = document.getElementById('add-new-engine')
    // //     const formData1 = new FormData(form)
    // //     const itemsAddData = {
    // //         code: formData1.get('code'),
    // //         manufacturer: formData1.get('manufacturer'),
    // //         name: formData1.get('name'),
    // //         partNumber: formData1.get('partNumber')
    // //     }
    // //     await storeEngines.create(itemsAddData)
    // //     AddForm.value = false
    // //     updated.value = false
    // // }
    // // function hideForm(){
    // //     AddForm.value = false
    // //     updated.value = false
    // //     // isPopupVisible.value = false
    // // }
    // async function showUpdateForm(item) {
    //     // itemId.value = Number(item['@id'].match(/\d+/)[0])
    //     // await storeEngines.fetchOne(itemId.value)
    //     // const engine = storeEngines.engine
    //     // formData.value = {
    //     //     code: engine.code,
    //     //     manufacturer: engine.manufacturer ? engine.manufacturer['@id'] : null,
    //     //     name: engine.name,
    //     //     partNumber: engine.partNumber
    //     // }
    //     // key.value++
    //     // updated.value = true
    //     // AddForm.value = true
    //     console.log(item)
    // }
    // // async function updateItem(){
    // //     try {
    // //         const form = document.getElementById('update-engine')
    // //         const formData2 = new FormData(form)
    // //         const itemsUpdateData = {
    // //             code: formData2.get('code'),
    // //             manufacturer: formData2.get('manufacturer'),
    // //             name: formData2.get('name'),
    // //             partNumber: formData2.get('partNumber')
    // //         }
    // //         await storeEngines.update(itemsUpdateData)
    // //         AddForm.value = false
    // //         updated.value = false
    // //         isPopupVisible.value = false
    // //         await refreshList()
    // //     } catch (error) {
    // //         violations.value = error
    // //         isPopupVisible.value = true
    // //     }
    // // }
    // async function deleted(id){
    //     //await storeEngines.remove(id)
    //     //await refreshList()
    //     console.log(id)
    // }

    // //region ## fonctions Recherche, Tri et pagination
    // async function getPage(nPage){
    //     tableCriteria.gotoPage(nPage)
    //     await refreshList()
    // }
    // async function trier(payload) {
    //     tableCriteria.addSort(payload.name, payload.direction)
    //     await refreshList()
    // }
    // async function search(inputValues) {
    //     const result = Object.keys(inputValues).map(cle => ({field: cle, value: inputValues[cle]}))
    //     result.forEach(filter => {
    //         tableCriteria.addFilter(filter.field, filter.value)
    //     })
    //     await refreshList()
    // }
    // async function cancelSearch() {
    //     tableCriteria.resetAllFilter()
    //     await refreshList()
    // }
    // //endregion
</script>

<template>
    coucouc
    <!-- <div class="container">
        <div class="row">
            <h1 class="col">
                <img src="img/production/icons8-usine-48.png"/>
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
                <AppCardableTable
                    :current-page="storeEngines.currentPage"
                    :fields="tabFields"
                    :first-page="storeEngines.view['hydra:first']"
                    :items="storeEngines.engines"
                    :last-page="storeEngines.view['hydra:last']"
                    :min="AddForm"
                    :next-page="storeEngines.view['hydra:next']"
                    :pag="storeEngines.pagination"
                    :previous-page="storeEngines.view['hydra:previous']"
                    :user="roleuser"
                    form="formSocietyCardableTable"
                    @cancel-search="cancelSearch"
                    @deleted="deleted"
                    @get-page="getPage"
                    @search="search"
                    @trier-alphabet="trier"
                    @update="showUpdateForm"/>
        </div> -->
    <!--            <div v-if="AddForm && !updated" class="col">-->
    <!--                <AppCard class="bg-blue col" title="">-->
    <!--                    <div class="row">-->
    <!--                        <button id="btnRetour1" class="btn btn-danger btn-icon btn-sm col-1" @click="hideForm">-->
    <!--                            <Fa icon="angle-double-left"/>-->
    <!--                        </button>-->
    <!--                        <h4 class="col">-->
    <!--                            <Fa icon="plus"/> Ajout-->
    <!--                        </h4>-->
    <!--                    </div>-->
    <!--                    <br/>-->
    <!--                    <AppFormCardable id="add-new-engine" :fields="addFormfields" :model-value="formData" label-cols/>-->
    <!--                    &lt;!&ndash; On ne permet pas l'ajout de pièce jointe lors du formulaire de création afin de simplifier le traitement &ndash;&gt;-->
    <!--                    <div class="col">-->
    <!--                        <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="addNewItem">-->
    <!--                            &lt;!&ndash; &ndash;&gt;-->
    <!--                            <Fa icon="plus"/> Ajouter-->
    <!--                        </AppBtn>-->
    <!--                    </div>-->
    <!--                </AppCard>-->
    <!--            </div>-->
    <!--            <div v-else-if="AddForm && updated" class="col">-->
    <!--                <AppCard class="bg-blue col" title="">-->
    <!--                    <div class="col">-->
    <!--                        <button id="btnRetour2" class="btn btn-danger btn-icon btn-sm col-1" @click="hideForm">-->
    <!--                            <Fa icon="angle-double-left"/>-->
    <!--                        </button>-->
    <!--                        <h4 class="col">-->
    <!--                            <Fa icon="pencil-alt"/> Modification-->
    <!--                        </h4>-->
    <!--                    </div>-->
    <!--                    <br/>-->
    <!--                    <AppFormCardable id="update-engine" :key="key" :fields="addFormfields" :model-value="formData.value" :violations="violations"/>-->
    <!--                    <Suspense>-->
    <!--                        <AppTabFichiers-->
    <!--                            :key="`attachment_${key}`"-->
    <!--                            attachment-element-label="engine"-->
    <!--                            :element-api-url="`/api/manufacturer-engines/${storeEngines.engine.id}`"-->
    <!--                            :element-attachment-store="fetchManufacturerAttachmentStore"-->
    <!--                            :element-id="storeEngines.engine.id"-->
    <!--                            element-parameter-name="ENGINE_ATTACHMENT_CATEGORIES"-->
    <!--                            :element-store="useManufacturerEngineStore"/>-->
    <!--                    </Suspense>-->
    <!--                    <div v-if="isPopupVisible" class="alert alert-danger" role="alert">-->
    <!--                        <div v-for="violation in violations" :key="violation">-->
    <!--                            <li>{{ violation.message }}</li>-->
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                    <div class="col">-->
    <!--                        <AppBtn class="btn-float-right" label="retour" variant="success" size="sm" @click="updateItem">-->
    <!--                            <Fa icon="pencil-alt"/> Modifier-->
    <!--                        </AppBtn>-->
    <!--                    </div>-->
    <!--                </AppCard>-->
    <!--            </div>-->
    <!-- </div>
    </div> -->
</template>

<style scoped>
    .btn-float-right{
        float: right;
    }
</style>
