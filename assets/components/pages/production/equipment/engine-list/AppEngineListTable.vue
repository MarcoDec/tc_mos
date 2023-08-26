<script setup>
    import {computed, ref} from 'vue'
    import AppFormCardable from '../../../../form-cardable/AppFormCardable'
    import AppSuspense from '../../../../AppSuspense.vue'
    import router from '../../../../../router'
    import useEngineGroups from '../../../../../stores/production/engine/groups/engineGroups'
    import {
        useEngineStore
    } from '../../../../../stores/production/engine/engines'
    import {useEngineTypeStore} from '../../../../../stores/production/engine/type/engineTypes'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'
    import {
        useManufacturerEngineStore
    } from '../../../../../stores/production/engine/manufacturer-engine/manufacturerEngines'
    defineProps({
        title: {required: true, type: String}
    })
    import useUser from '../../../../../stores/security'
    import useZonesStore from '../../../../../stores/production/company/zones'
    const roleuser = ref('reader')
    const AddForm = ref(false)
    const formData = ref({})
    let key = 0
    const currentCompany = useUser().company
    //region Initialisation des champs et listes associées
    //region récupération des types de machine
    const fetchEngineTypes = useEngineTypeStore()
    const optionsEngineTypes = fetchEngineTypes.engineTypes
    //endregion
    //region récupération des groupes de machines
    const fetchEngineGroups = useEngineGroups()
    await fetchEngineGroups.fetchAllEngineGroups()
    const optionsEngineGroups = fetchEngineGroups.engineGroups.map(item => ({id: item['@id'], text: `${item.code}-${item.name}`, value: item['@id']}))
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
    //endregion
    //region récupération des zones
    const fetchZones = useZonesStore()
    await fetchZones.fetchAll(currentCompany)
    const optionsZones = fetchZones.zones.map(item => ({id: item['@id'], text: item.name, value: item['@id']}))
    function labelZoneOption(value) {
        return optionsZones.find(item => item.id === value)?.text
            ?? null
    }
    //endregion
    //region récupération des Machines de référence (modèles)
    const fetchManufacturerEngines = useManufacturerEngineStore()
    const tableCriteriaME = useFetchCriteria('ManufacturerEngines')
    tableCriteriaME.addFilter('pagination', 'false')
    await fetchManufacturerEngines.fetchAll(tableCriteriaME.getFetchCriteria)
    const optionsManufacturerEngines = fetchManufacturerEngines.engines.map(item => ({id: item['@id'], text: item.name, value: item['@id']}))
    function labelManufacturerEngine(value) {
        return optionsManufacturerEngines.find(item => item.id === value)?.text
            ?? null
    }
    //endregion
    //region récupération des Machines
    const tableCriteria = useFetchCriteria('Engines')
    tableCriteria.addFilter('zone.company', currentCompany)
    const storeEngines = useEngineStore()
    await storeEngines.fetchAll(tableCriteria.getFetchCriteria)
    //endregion
    //region Définition de la liste des champs pour le formulaire de création
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
                label: labelZoneOption,
                options: optionsZones
            },
            trie: false,
            type: 'select'
        },
        {label: 'Date entrée', min: false, name: 'entryDate', trie: false, type: 'date'},
        {label: 'Nom', min: true, name: 'name', trie: true, type: 'text'},
        {label: 'Numero de série', min: true, name: 'serialNumber', trie: true, type: 'text'},
        {
            label: 'Machine de référence (modèle)',
            min: true,
            name: 'manufacturerEngine',
            options: {
                label: labelManufacturerEngine,
                options: optionsManufacturerEngines
            },
            trie: false,
            type: 'select'
        }
    ])
    //endregion
    //region Définition de la liste des champs pour l'affichage de la liste
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
                label: labelZoneOption,
                options: optionsZones
            },
            trie: false,
            type: 'select'
        },
        {label: 'Code', min: true, name: 'code', trie: true, type: 'text'},
        {label: 'Date entrée', min: false, name: 'entryDate', trie: true, type: 'date'},
        {label: 'Nom', min: true, name: 'name', trie: true, type: 'text'},
        {label: 'Numero de série', min: true, name: 'serialNumber', trie: true, type: 'text'},
        {
            label: 'Machine de référence (modèle)',
            min: true,
            name: 'manufacturerEngine',
            options: {
                label: labelManufacturerEngine,
                options: optionsManufacturerEngines
            },
            trie: true,
            type: 'select'
        }
    ]
    //endregion
    //endregion
    async function refreshList() {
        tableCriteria.addFilter('zone.company', currentCompany)
        const criteria = tableCriteria.getFetchCriteria
        await storeEngines.fetchAll(criteria)
    }
    function showAddForm(){
        // // On vide le formulaire avant de l'afficher
        formData.value = {
            '@type': null,
            brand: null,
            code: null,
            entryDate: null,
            group: null,
            manufacturerEngine: null,
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
            entryDate: formData1.get('entryDate'),
            group: formData1.get('group'),
            manufacturerEngine: formData1.get('manufacturerEngine'),
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
        await refreshList()
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
        if (payload.name === 'manufacturerEngine') tableCriteria.addSort('manufacturerEngine.name', payload.direction)
        else
            tableCriteria.addSort(payload.name, payload.direction)
        await refreshList()
    }
    async function search(inputValues) {
        const result = Object.keys(inputValues).map(cle => ({field: cle, value: inputValues[cle]}))
        result.forEach(filter => {
            if (filter.field === 'entryDate') tableCriteria.addFilter(filter.field, filter.value, 'dateExact')
            else tableCriteria.addFilter(filter.field, filter.value)
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
                <img src="/public/img/production/icons8-usine-48.png" alt="icône Machine type perceuse"/>
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
                </AppSuspense>
            </div>
            <div v-if="AddForm" class="col">
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
                        <AppFormCardable id="add-new-engine" :key="key" :fields="addFormFields" :model-value="formData" label-cols @update:model-value="onAddFormDataChange"/>
                        <div class="col">
                            <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="addNewItem">
                                <Fa icon="plus"/> Ajouter
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
