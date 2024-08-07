<script setup>
    import {onMounted, ref} from 'vue'
    import useEngineGroups from '../../../../../../../stores/production/engine/groups/engineGroups'
    import useFetchCriteria from '../../../../../../../stores/fetch-criteria/fetchCriteria'
    import {
        useManufacturerEngineStore
    } from '../../../../../../../stores/production/engine/manufacturer-engine/manufacturerEngines'
    import useZonesStore from '../../../../../../../stores/production/company/zones'
    import {useGenEngineStore} from '../../../../../../../stores/production/engine/generic/engines'

    const enginesStr = 'infras'
    const engineStr = 'infra'
    const baseUrl = '/api/infras'

    //region récupération des stores déjà chargés
    const fetchEngineStore = useGenEngineStore(enginesStr, baseUrl)()
    const fetchZones = useZonesStore()
    // await fetchZones.fetchAll(currentCompany) //fetch inutile car déjà fait dans le composant parent
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
    //region récupération des Machines de référence (modèles)
    const fetchManufacturerEngines = useManufacturerEngineStore()
    const tableCriteriaME = useFetchCriteria('ManufacturerEngines')
    tableCriteriaME.addFilter('pagination', 'false')
    await fetchManufacturerEngines.fetchAll(tableCriteriaME.getFetchCriteria)
    const optionsManufacturerEngines = fetchManufacturerEngines.engines.map(item => ({id: item['@id'], text: item.name === null ? '' : item.name, value: item['@id']}))
    function labelManufacturerEngine(value) {
        return optionsManufacturerEngines.find(item => item.id === value)?.text
            ?? null
    }
    //endregion

    //region creation options pour les zones
    const optionsZones = fetchZones.zones.map(item => ({id: item['@id'], text: item.name, value: item['@id']}))
    function labelZoneOption(value) {
        return optionsZones.find(item => item.id === value)?.text
            ?? null
    }
    //endregion

    //region initialisation localData
    const localData = ref({})
    const key = ref(0)
    onMounted(() => {
        localData.value = {
            brand: fetchEngineStore.engine ? fetchEngineStore.engine.brand : null,
            //code: fetchEngineStore.engine.code,
            entryDate: fetchEngineStore.engine ? fetchEngineStore.engine.entryDate.substring(0, 10) : null,
            group: fetchEngineStore.engine ? fetchEngineStore.engine.group : null,
            manufacturerEngine: fetchEngineStore.engine.manufacturerEngine ? fetchEngineStore.engine.manufacturerEngine['@id'] : null,
            maxOperator: fetchEngineStore.engine ? fetchEngineStore.engine.maxOperator : null,
            name: fetchEngineStore.engine ? fetchEngineStore.engine.name : null,
            notes: fetchEngineStore.engine ? fetchEngineStore.engine.notes : null,
            serialNumber: fetchEngineStore.engine ? fetchEngineStore.engine.serialNumber : null,
            zone: fetchEngineStore.engine.zone ? fetchEngineStore.engine.zone['@id'] : null
        }
        key.value++
    })
    //endregion
    //region définition des champs du formulaire
    const generalFields = [
        {
            label: 'Groupe',
            name: 'group',
            options: getGroups(engineStr),
            type: 'select'
        },
        {label: 'Marque', name: 'brand', type: 'text'},
        //{label: 'Code', name: 'code', type: 'text'},
        {label: 'Date d\'entrée', name: 'entryDate', type: 'date'},
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Numéro de Série', name: 'serialNumber', type: 'text'},
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
        {
            label: 'Machine de référence (modèle)',
            name: 'manufacturerEngine',
            options: {
                label: labelManufacturerEngine,
                options: optionsManufacturerEngines
            },
            type: 'select'
        },
        {label: 'Nombre d\'opérateur maximal', name: 'maxOperator', type: 'text'},
        {label: 'Notes', name: 'notes', type: 'textarea'}
    ]
    //endregion
    function updateField(data) {
        localData.value = {
            brand: data.brand,
            //code: data.code, //autogénéré
            entryDate: data.entryDate,
            group: data.group,
            manufacturerEngine: data.manufacturerEngine,
            maxOperator: Number(data.maxOperator),
            name: data.name,
            notes: data.notes,
            serialNumber: data.serialNumber,
            zone: data.zone
        }
    }
    async function updateGeneral() {
        await fetchEngineStore.update(localData.value)
    }
</script>

<template>
    <AppCardShow
        id="addGeneralites"
        :key="key"
        :fields="generalFields"
        :component-attribute="localData"
        @update:model-value="updateField"
        @update="updateGeneral"/>
</template>
