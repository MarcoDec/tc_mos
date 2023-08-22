<script setup>
    import {computed, ref} from 'vue'
    import AppFormCardable from '../../../../form-cardable/AppFormCardable'
    import router from '../../../../../router'
    import useEngineGroups from '../../../../../stores/production/engine/groups/engineGroups'
    import {
        useEngineStore
    } from '../../../../../stores/production/engine/engines'
    import {useEngineTypeStore} from '../../../../../stores/production/engine/type/engineTypes'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    defineProps({
        title: {required: true, type: String}
    })
    import useUser from '../../../../../stores/security'
    import useZonesStore from '../../../../../stores/production/company/zones'

    const currentCompany = useUser().company
    const fetchEngineTypes = useEngineTypeStore()
    const optionsEngineTypes = fetchEngineTypes.engineTypes
    const fetchEngineGroups = useEngineGroups()
    await fetchEngineGroups.fetchAllEngineGroups()
    const optionsEngineGroups = fetchEngineGroups.engineGroups.map(item => ({id: item['@id'], text: `${item.code}-${item.name}`, value: item['@id']}))
    const fetchZones = useZonesStore()
    await fetchZones.fetchAll(currentCompany)
    const optionsZones = fetchZones.zones.map(item => ({id: item['@id'], text: item.name, value: item['@id']}))
    const tableCriteria = useFetchCriteria('Engines')
    tableCriteria.addFilter('zone.company', currentCompany)
    const roleuser = ref('reader')
    const AddForm = ref(false)
    // const updated = ref(false)
    const storeEngines = useEngineStore()
    //currentCompany
    await storeEngines.fetchAll(tableCriteria.getFetchCriteria)
    const formData = ref({})
    // const violations = ref([])
    // const itemId = ref(null)
    // const isPopupVisible = ref(false)
    let key = 0
    function filterEngineGroups(type) {
        return {
            label: value => optionsEngineGroups.find(item => item.value === value)?.text
                ?? null,
            options: optionsEngineGroups.filter(item => item.value.includes(type))
        }
    }
    function getGroups(typeValue) {
        if (typeof typeValue !== 'undefined' && typeValue !== null) return filterEngineGroups(typeValue)
        return {
            label: value => optionsEngineGroups.find(item => item.value === value)?.text
                ?? null,
            options: optionsEngineGroups
        }
    }
    const addFormFields = computed(() => [
        {
            label: 'Type',
            min: true,
            name: '@type',
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
        {label: 'Marque', min: false, name: 'brand', trie: true, type: 'text'},
        {
            label: 'Groupe',
            min: false,
            name: 'group',
            options: getGroups(formData.value['@type']),
            searchDisabled: true, //désactivation de la fonction filtre car Group est une classe abstraite...
            trie: false,
            type: 'select'
        },
        {
            label: 'Zone',
            min: true,
            name: 'zone',
            options: {
                label: value => optionsZones.find(item => item.value === value['@id'])?.text
                    ?? null,
                options: optionsZones
            },
            trie: false,
            type: 'select'
        },
        {label: 'Nom', min: true, name: 'name', trie: true, type: 'text'},
        {label: 'Numero de série', min: true, name: 'serialNumber', trie: true, type: 'text'}
    ])

    const tabFields = [
        {
            label: 'Type',
            min: true,
            name: '@type',
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
        {label: 'Marque', min: false, name: 'brand', trie: true, type: 'text'},
        {
            label: 'Groupe',
            min: false,
            name: 'group',
            options: {
                label: value => optionsEngineGroups.find(item => item.value === value)?.text
                    ?? null,
                options: optionsEngineGroups
            },
            searchDisabled: true, //désactivation de la fonction filtre car Group est une classe abstraite...
            trie: false,
            type: 'select'
        },
        {
            label: 'Zone',
            min: true,
            name: 'zone',
            options: {
                label: value => optionsZones.find(item => item.value === value['@id'])?.text
                    ?? null,
                options: optionsZones
            },
            trie: false,
            type: 'select'
        },
        {label: 'Code', min: true, name: 'code', trie: true, type: 'text'},
        {label: 'Nom', min: true, name: 'name', trie: true, type: 'text'},
        {label: 'Numero de série', min: true, name: 'serialNumber', trie: true, type: 'text'}
    ]
    async function refreshList() {
        tableCriteria.addFilter('zone.company', currentCompany)
        const criteria = tableCriteria.getFetchCriteria
        await storeEngines.fetchAll(criteria)
    }
    function showAddForm(){
        // // On vide le formulaire avant de l'afficher
        formData.value = {
            '@type': null,
            brand: 'coucou',
            code: null,
            group: null,
            name: null,
            serialNumber: null,
            zone: null
        }
        AddForm.value = true
    }
    async function addNewItem(){
        const form = document.getElementById('add-new-engine')
        const formData1 = new FormData(form)
        const itemsAddData = {
            //'@type': formData1.get('@type'),
            brand: formData1.get('brand'),
            code: formData1.get('code'),
            group: formData1.get('group'),
            name: formData1.get('name'),
            serialNumber: formData1.get('serialNumber'),
            zone: formData1.get('zone')
        }
        switch (formData1.get('@type')) {
            case 'tool':
                await storeEngines.createTool(itemsAddData)
                break
            case 'workstation':
                await storeEngines.createWorkstation(itemsAddData)
                break
            case 'counter-part':
                await storeEngines.createCounterPart(itemsAddData)
                break
        }
        AddForm.value = false
    }
    function hideForm(){
        AddForm.value = false
    }
    async function showUpdateForm(item) {
        const idEngine = Number(item.id)
        switch (item['@type']) {
            case 'Tool':
                // eslint-disable-next-line camelcase
                await router.push({name: 'toolShow', params: {id_engine: idEngine}})
                break
            case 'Workstation':
                // eslint-disable-next-line camelcase
                await router.push({name: 'workstationShow', params: {id_engine: idEngine}})
                break
            case 'CounterPart':
                // eslint-disable-next-line camelcase
                await router.push({name: 'counterPartShow', params: {id_engine: idEngine}})
                break
        }
    }
    async function deleted(id){
        const elementToRemove = storeEngines.engines.find(item => item.id === id)
        const userResponse = confirm(`Are you sure you want to remove "${elementToRemove.name}" (SN => ${elementToRemove.serialNumber}) ?`)
        if (userResponse) {
            await storeEngines.remove(id)
            await refreshList()
        }
    }

    //region ## fonctions Recherche, Tri et pagination
    async function getPage(nPage){
        tableCriteria.gotoPage(nPage)
        await refreshList()
    }
    async function trier(payload) {
        tableCriteria.addSort(payload.name, payload.direction)
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
    function onAddFormDataChange(data) {
        if (formData.value['@type'] !== data['@type']) {
            formData.value = data
            key++
        }
        formData.value = data
    }
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
                    form="formEnginesCardableTable"
                    @cancel-search="cancelSearch"
                    @deleted="deleted"
                    @get-page="getPage"
                    @search="search"
                    @trier-alphabet="trier"
                    @update="showUpdateForm"/>
            </div>
            <div v-if="AddForm" class="col">
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
                    <AppFormCardable id="add-new-engine" :key="key" :fields="addFormFields" :model-value="formData" label-cols @update:model-value="onAddFormDataChange"/>
                    <div class="col">
                        <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="addNewItem">
                            <Fa icon="plus"/> Ajouter
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
