<script setup>
    import {ref} from 'vue'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import {
        useManufacturerEngineStore
    } from '../../../../../stores/production/engine/manufacturer-engine/manufacturerEngines'

    defineProps({
        title: {required: true, type: String}
    })
    const tableCriteria = useFetchCriteria('manufacturerEngines')
    const roleuser = ref('reader')
    const AddForm = ref(false)
    const updated = ref(false)
    const storeManufacturerEnginers = useManufacturerEngineStore()
    await storeManufacturerEnginers.fetchAll()
    console.log(storeManufacturerEnginers)
    const formData = new FormData()
    // let violations = []
    // let itemId = ''
    // const isPopupVisible = ref(false)
    //
    //
    const tabFields = [
        {label: 'Fabriquant', min: true, name: 'manufacturer.name', trie: true, type: 'text'},
        {label: 'Référence Produit', min: true, name: 'partNumber', trie: true, type: 'text'},
        {label: 'Code', min: true, name: 'code', trie: true, type: 'text'},
        {label: 'Nom', min: true, name: 'name', trie: true, type: 'text'}
    ]
    const fieldsForm = tabFields
    function showAddForm(){
        AddForm.value = true
        updated.value = false
        formData.value = {
            address: null,
            address2: null,
            city: null,
            country: null,
            email: null,
            name: null,
            phoneNumber: null,
            zipCode: null
        }
    }
    // async function ajoutSociety(){
    //     const form = document.getElementById('addSociety')
    //     const formData1 = new FormData(form)
    //     const itemsAddData = {
    //         address: {
    //             address: formData1.get('address'),
    //             address2: formData1.get('address2'),
    //             city: formData1.get('city'),
    //             country: formData1.get('country'),
    //             email: formData1.get('email'),
    //             phoneNumber: formData1.get('phoneNumber'),
    //             zipCode: formData1.get('zipCode')
    //         },
    //         name: formData1.get('name')
    //     }
    //     await storeManufacturerEnginers.addSociety(itemsAddData)
    //     AddForm.value = false
    //     updated.value = false
    // }
    function hideForm(){
        AddForm.value = false
        updated.value = false
        // const itemsNull = {
        //     address: null,
        //     address2: null,
        //     city: null,
        //     country: null,
        //     email: null,
        //     name: null,
        //     phoneNumber: null,
        //     zipCode: null
        // }
        // formData.value = itemsNull
        // isPopupVisible.value = false
    }
    function update(item) {
        console.log('update', item)
        updated.value = true
        AddForm.value = true
        // itemId = Number(item['@id'].match(/\d+/)[0])
        // const itemsData = {
        //     address: item.address,
        //     address2: item.address2,
        //     city: item.city,
        //     country: item.country,
        //     email: item.email,
        //     name: item.name,
        //     phoneNumber: item.phoneNumber,
        //     zipCode: item.zipCode
        // }
        // formData.value = itemsData
    }
    // async function updateSociety(){
    //     try {
    //         const form = document.getElementById('updateSociety')
    //         const formData2 = new FormData(form)
    //         const itemsUpdateData = {
    //             address: {
    //                 address: formData2.get('address'),
    //                 address2: formData2.get('address2'),
    //                 city: formData2.get('city'),
    //                 country: formData2.get('country'),
    //                 email: formData2.get('email'),
    //                 phoneNumber: formData2.get('phoneNumber'),
    //                 zipCode: formData2.get('zipCode')
    //             },
    //             name: formData2.get('name')
    //         }
    //         const payload = {
    //             filter,
    //             filterBy,
    //             id: itemId,
    //             itemsUpdateData,
    //             sortable,
    //             trierAlpha
    //         }
    //         await storeManufacturerEnginers.updateSociety(payload)
    //         AddForm.value = false
    //         updated.value = false
    //         isPopupVisible.value = false
    //     } catch (error) {
    //         violations = error
    //         isPopupVisible.value = true
    //     }
    // }
    async function deleted(id){
        await storeManufacturerEnginers.remove(id)
        const criteria = tableCriteria.getFetchCriteria
        await storeManufacturerEnginers.fetchAll(criteria)
    }

    //region ## fonctions Recherche, Tri et pagination
    async function getPage(nPage){
        tableCriteria.gotoPage(nPage)
        const criteria = tableCriteria.getFetchCriteria
        await storeManufacturerEnginers.fetchAll(criteria)
    }
    async function trier(payload) {
        tableCriteria.addSort(payload.name, payload.direction)
        const criteria = tableCriteria.getFetchCriteria
        await storeManufacturerEnginers.fetchAll(criteria)
    }
    async function search(inputValues) {
        const result = Object.keys(inputValues).map(key => ({field: key, value: inputValues[key]}))
        result.forEach(filter => {
            tableCriteria.addFilter(filter.field, filter.value)
        })
        const criteria = tableCriteria.getFetchCriteria
        await storeManufacturerEnginers.fetchAll(criteria)
    }
    async function cancelSearch() {
        tableCriteria.resetAllFilter()
        const criteria = tableCriteria.getFetchCriteria
        await storeManufacturerEnginers.fetchAll(criteria)
    }
    //endregion
</script>

<template>
    <div class="container">
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
                    @update="update"/>
            </div>
            <div v-if="AddForm && !updated" class="col">
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
                    <AppFormCardable id="addSociety" :fields="fieldsForm" :model-value="formData" label-cols/>
                    <!-- On ne permet pas l'ajout de pièce jointe lors du formulaire de création afin de simplifier le traitement -->
                    <div class="col">
                        <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm">
                            <!-- @click="ajoutSociety"-->
                            <Fa icon="plus"/> Ajouter
                        </AppBtn>
                    </div>
                </AppCard>
            </div>
            <div v-else-if="AddForm && updated" class="col">
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
                    <!--<AppFormCardable id="updateSociety"/>-->
                    <!-- :fields="fieldsForm" :model-value="formData"-->
                    <!-- :violations="violations"-->
                    <p>Ici le formulaire de modification</p>
                    <div class="alert alert-danger" role="alert">
                        <!-- v-if="isPopupVisible" -->
                        <!--                        <div v-for="violation in violations" :key="violation">-->
                        <!--                            <li>{{ violation.message }}</li>-->
                        <!--                        </div>-->
                    </div>
                    <div class="col">
                        <AppBtn class="btn-float-right" label="retour" variant="success" size="sm">
                            <!-- @click="updateSociety"-->
                            <Fa icon="pencil-alt"/> Modifier
                        </AppBtn>
                    </div>
                </AppCard>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .btn-float-right{
        float: right;
    }
</style>
