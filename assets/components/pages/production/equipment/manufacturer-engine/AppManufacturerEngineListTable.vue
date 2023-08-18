<script setup>
    console.log()
    // // import {computed, ref} from 'vue'
    // import {/*computed,*/ ref} from 'vue'
    // import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    // import {useManufacturerEngineStore} from '../../../../../stores/production/engine/manufacturer-engine/manufacturerEngines'
    // defineProps({
    //     title: {required: true, type: String}
    // })
    // const tableCriteria = useFetchCriteria('manufacturerEngines')
    // const roleuser = ref('reader')
    // const AddForm = ref(false)
    // const storeManufacturerEnginers = useManufacturerEngineStore()
    // await storeManufacturerEnginers.fetchAll()
    // console.log(storeManufacturerEnginers)
    // // const formData = new FormData()
    // // let violations = []
    // // const updated = ref(false)
    // // let itemId = ''
    // // const isPopupVisible = ref(false)
    // // const sortable = ref(false)
    // // const filter = ref(false)
    // // let trierAlpha = {}
    // // let filterBy = {}
    // // const itemsTable = computed(() => storeManufacturerEnginers.engines.reduce((acc, curr) => acc.concat(curr), []))
    // // console.log(itemsTable.value, storeManufacturerEnginers.engines)
    // //const itemsTable = computed(() => storeManufacturerEnginers.engines.reduce((acc, curr) => acc.concat(curr), []))
    // //console.log(itemsTable.value, storeManufacturerEnginers.engines)
    // //
    // // const fieldsForm = [
    // //     {label: 'Nom*', name: 'name', type: 'text'}
    // // ]
    // //
    // const tabFields = [
    //     {label: 'Fabriquant', min: true, name: 'manufacturer.name', trie: true, type: 'text'},
    //     {label: 'Référence Produit', min: true, name: 'partNumber', trie: true, type: 'text'},
    //     {label: 'Code', min: true, name: 'code', trie: true, type: 'text'},
    //     {label: 'Nom', min: true, name: 'name', trie: true, type: 'text'}
    // ]
    // // function ajoute(){
    // //     AddForm.value = true
    // //     updated.value = false
    // //     const itemsNull = {
    // //         address: null,
    // //         address2: null,
    // //         city: null,
    // //         country: null,
    // //         email: null,
    // //         name: null,
    // //         phoneNumber: null,
    // //         zipCode: null
    // //     }
    // //     formData.value = itemsNull
    // // }
    // // async function ajoutSociety(){
    // //     const form = document.getElementById('addSociety')
    // //     const formData1 = new FormData(form)
    // //     const itemsAddData = {
    // //         address: {
    // //             address: formData1.get('address'),
    // //             address2: formData1.get('address2'),
    // //             city: formData1.get('city'),
    // //             country: formData1.get('country'),
    // //             email: formData1.get('email'),
    // //             phoneNumber: formData1.get('phoneNumber'),
    // //             zipCode: formData1.get('zipCode')
    // //         },
    // //         name: formData1.get('name')
    // //     }
    // //     await storeManufacturerEnginers.addSociety(itemsAddData)
    // //     AddForm.value = false
    // //     updated.value = false
    // // }
    // // function annule(){
    // //     AddForm.value = false
    // //     updated.value = false
    // //     const itemsNull = {
    // //         address: null,
    // //         address2: null,
    // //         city: null,
    // //         country: null,
    // //         email: null,
    // //         name: null,
    // //         phoneNumber: null,
    // //         zipCode: null
    // //     }
    // //     formData.value = itemsNull
    // //     isPopupVisible.value = false
    // // }
    // // function update(item) {
    // //     updated.value = true
    // //     AddForm.value = true
    // //     itemId = Number(item['@id'].match(/\d+/)[0])
    // //     const itemsData = {
    // //         address: item.address,
    // //         address2: item.address2,
    // //         city: item.city,
    // //         country: item.country,
    // //         email: item.email,
    // //         name: item.name,
    // //         phoneNumber: item.phoneNumber,
    // //         zipCode: item.zipCode
    // //     }
    // //     formData.value = itemsData
    // // }
    // // async function updateSociety(){
    // //     try {
    // //         const form = document.getElementById('updateSociety')
    // //         const formData2 = new FormData(form)
    // //         const itemsUpdateData = {
    // //             address: {
    // //                 address: formData2.get('address'),
    // //                 address2: formData2.get('address2'),
    // //                 city: formData2.get('city'),
    // //                 country: formData2.get('country'),
    // //                 email: formData2.get('email'),
    // //                 phoneNumber: formData2.get('phoneNumber'),
    // //                 zipCode: formData2.get('zipCode')
    // //             },
    // //             name: formData2.get('name')
    // //         }
    // //         const payload = {
    // //             filter,
    // //             filterBy,
    // //             id: itemId,
    // //             itemsUpdateData,
    // //             sortable,
    // //             trierAlpha
    // //         }
    // //         await storeManufacturerEnginers.updateSociety(payload)
    // //         AddForm.value = false
    // //         updated.value = false
    // //         isPopupVisible.value = false
    // //     } catch (error) {
    // //         violations = error
    // //         isPopupVisible.value = true
    // //     }
    // // }
    // // async function deleted(id){
    // //     await storeManufacturerEnginers.delated(id)
    // // }
    // async function getPage(nPage){
    //     tableCriteria.gotoPage(nPage)
    //     const criteria = tableCriteria.getFetchCriteria
    //     await storeManufacturerEnginers.fetchAll(criteria)
    // }
    // // async function trierAlphabet(payload) {
    // //     await storeManufacturerEnginers.sortableItems(payload, filterBy, filter)
    // //     sortable.value = true
    // //     trierAlpha = computed(() => payload)
    // // }
    // async function trier(payload) {
    //     //console.log('trier', payload)
    //     tableCriteria.addSort(payload.name, payload.direction)
    //     const criteria = tableCriteria.getFetchCriteria
    //     // console.log('trier', criteria)
    //     await storeManufacturerEnginers.fetchAll(criteria)
    //     //await storeManufacturerEnginers.sortableItems(payload, filterBy, filter)
    //     //sortable.value = true
    //     //trierAlpha = computed(() => payload)
    // }
    // //region ## fonctions Recherche
    // async function search(inputValues) {
    //     // console.log(inputValues)
    //     //console.log(inputValues)
    //     const result = Object.keys(inputValues).map(key => ({field: key, value: inputValues[key]}))
    //     // console.log(result)
    //     //console.log(result)
    //     result.forEach(filter => {
    //         tableCriteria.addFilter(filter.field, filter.value)
    //     })
    //     const criteria = tableCriteria.getFetchCriteria
    //     // console.log('search', criteria)
    //     //console.log('search', criteria)
    //     await storeManufacturerEnginers.fetchAll(criteria)
    // }
    // async function cancelSearch() {
    //     tableCriteria.resetAllFilter()
    //     const criteria = tableCriteria.getFetchCriteria
    //     // console.log('search', criteria)
    //     //console.log('search', criteria)
    //     await storeManufacturerEnginers.fetchAll(criteria)
    // }
    // //endregion
</script>

<template>
    cc
    <!-- <div class="container">
        <div class="row">
            <h1 class="col">
                <img src="img/production/icons8-usine-48.png"/>
                {{ title }}
            </h1>
            <span class="col">
                <AppBtn variant="success" label="Ajout" class="btn-float-right"> -->
    <!--@click="ajoute"-->
    <!-- <Fa icon="plus"/>
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
                    @get-page="getPage"
                    @search="search"
                    @trier-alphabet="trier"/> -->
    <!--                    @update="update"-->
    <!--                    @deleted="deleted"-->
    <!--                    @trier-alphabet="trierAlphabet"-->
    <!--                    -->
    <!-- </div>
        </div> -->
    <!--        <AppRow>-->
    <!--            <AppCol>-->
    <!--                <AppCardableTable-->
    <!--                    :current-page="storeManufacturerEnginers.currentPage"-->
    <!--                    :fields="tabFields"-->
    <!--                    :first-page="storeManufacturerEnginers.firstPage"-->
    <!--                    :items="itemsTable"-->
    <!--                    :last-page="storeManufacturerEnginers.lastPage"-->
    <!--                    :min="AddForm"-->
    <!--                    :next-page="storeManufacturerEnginers.nextPage"-->
    <!--                    :pag="storeManufacturerEnginers.pagination"-->
    <!--                    :previous-page="storeManufacturerEnginers.previousPage"-->
    <!--                    :user="roleuser"-->
    <!--                    form="formSocietyCardableTable"-->
    <!--                    @update="update"-->
    <!--                    @deleted="deleted"-->
    <!--                    @get-page="getPage"-->
    <!--                    @trier-alphabet="trierAlphabet"-->
    <!--                    @search="search"-->
    <!--                    @cancel-search="cancelSearch"/>-->
    <!--            </AppCol>-->
    <!--            <AppCol v-if="AddForm && !updated" class="col-7">-->
    <!--                <AppCard class="bg-blue col" title="">-->
    <!--                    <AppRow>-->
    <!--                        <button id="btnRetour1" class="btn btn-danger btn-icon btn-sm col-1" @click="annule">-->
    <!--                            <Fa icon="angle-double-left"/>-->
    <!--                        </button>-->
    <!--                        <h4 class="col">-->
    <!--                            <Fa icon="plus"/> Ajout-->
    <!--                        </h4>-->
    <!--                    </AppRow>-->
    <!--                    <br/>-->
    <!--                    <AppFormCardable id="addSociety" :fields="fieldsForm" :model-value="formData" label-cols/>-->
    <!--                    <AppCol class="btnright">-->
    <!--                        <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutSociety">-->
    <!--                            <Fa icon="plus"/> Ajouter-->
    <!--                        </AppBtn>-->
    <!--                    </AppCol>-->
    <!--                </AppCard>-->
    <!--            </AppCol>-->
    <!--            <AppCol v-else-if="AddForm && updated" class="col-7">-->
    <!--                <AppCard class="bg-blue col" title="">-->
    <!--                    <AppRow>-->
    <!--                        <button id="btnRetour2" class="btn btn-danger btn-icon btn-sm col-1" @click="annule">-->
    <!--                            <Fa icon="angle-double-left"/>-->
    <!--                        </button>-->
    <!--                        <h4 class="col">-->
    <!--                            <Fa icon="pencil-alt"/> Modification-->
    <!--                        </h4>-->
    <!--                    </AppRow>-->
    <!--                    <br/>-->
    <!--                    <AppFormCardable id="updateSociety" :fields="fieldsForm" :model-value="formData" :violations="violations"/>-->
    <!--                    <div v-if="isPopupVisible" class="alert alert-danger" role="alert">-->
    <!--                        <div v-for="violation in violations" :key="violation">-->
    <!--                            <li>{{ violation.message }}</li>-->
    <!--                        </div>-->
    <!--                    </div>-->
    <!--                    <AppCol class="btnright">-->
    <!--                        <AppBtn class="btn-float-right" label="retour" variant="success" size="sm" @click="updateSociety">-->
    <!--                            <Fa icon="pencil-alt"/> Modifier-->
    <!--                        </AppBtn>-->
    <!--                    </AppCol>-->
    <!--                </AppCard>-->
    <!--            </AppCol>-->
    <!--        </AppRow>-->
    <!-- </div> -->
</template>

<style scoped>
    .btn-float-right{
        float: right;
    }
</style>
