<script setup>
    import {computed, ref} from 'vue'
    import generateSupplier from '../../../../../stores/purchase/supplier/supplier'
    import {useIncotermStore} from '../../../../../stores/logistic/incoterm/incoterm'
    import useOptions from '../../../../../stores/option/options'
    import {useSocietyStore} from '../../../../../stores/management/societies/societies'
    import {useSuppliersStore} from '../../../../../stores/purchase/supplier/suppliers'

    //Définition des évènements
    const emit = defineEmits([
        'update',
        'update:modelValue'
    ])
    const fetchIncotermStore = useIncotermStore()
    const fetchSocietyStore = useSocietyStore()
    const fetchSuppliersStore = useSuppliersStore()
    const fetchUnitOptions = useOptions('units')
    await fetchUnitOptions.fetchOp()

    const localData = ref({})
    function initLocalData(){
        // Récupération des données supplier
        localData.value = fetchSuppliersStore.supplier
        // Récupération des données de la societé associée
        localData.value.ar = fetchSocietyStore.society.ar
        localData.value.copper = fetchSocietyStore.society.copper
        localData.value.incoterms = fetchSocietyStore.society.incoterms
        localData.value.orderMin = fetchSocietyStore.society.orderMin
        localData.value.managedCopper = fetchSocietyStore.society.copper.managed
        localData.value.vatMessageValue = fetchSocietyStore.society.vatMessage
        localData.value.incotermsValue = fetchSocietyStore.society.incoterms ? fetchSocietyStore.society.incoterms['@id'] : null
    }
    initLocalData()
    const optionsIncoterm = computed(() =>
        fetchIncotermStore.incoterms.map(incoterm => {
            const text = incoterm.name
            const value = incoterm['@id']
            return {text, value}
        }))
    const Achatfields = [
        {
            label: 'Minimum de commande',
            measure: {code: {label: 'Unité', name: 'unit', options: {options: [{text: 'EUR', value: 'EUR'}]}, sortName: 'unit.code', type: 'select'}, value: 'valeur'},
            name: 'orderMin',
            type: 'measure'
        },
        {
            label: 'Incoterm',
            name: 'incotermsValue',
            options: {
                label: value =>
                    optionsIncoterm.value.find(option => option.type === value)?.text
                    ?? null,
                options: optionsIncoterm.value
            },
            type: 'select'
        },
        {label: 'Gestion du cuivre', name: 'managedCopper', type: 'boolean'},
        {label: 'Niveau de confiance', name: 'confidenceCriteria', type: 'rating'},
        {label: 'Commande ouverte', name: 'openOrdersEnabled', type: 'boolean'},
        {label: 'Accusé de réception', name: 'ar', type: 'boolean'}
    ]

    async function updateModelValue(value) {
        localData.value = value
        emit('update:modelValue', value)
    }
    async function update() {
        const societyId = fetchSocietyStore.society.id
        const dataSociety = {
            ar: localData.value.ar,
            copper: {
                managed: localData.value.managedCopper
            },
            incoterms: localData.value.incotermsValue,
            orderMin: {
                code: 'EUR',
                value: Number(localData.value.orderMin.value)
            }
        }
        const data = {
            confidenceCriteria: localData.value.confidenceCriteria,
            openOrdersEnabled: localData.value.openOrdersEnabled
        }
        await fetchSocietyStore.update(dataSociety, societyId)

        await fetchSocietyStore.fetchById(societyId)
        const item = generateSupplier(localData.value)
        await item.updateLog(data)
        await fetchSocietyStore.fetch()
    }
</script>

<template>
    <AppCardShow
        id="addAchatLogistique"
        :fields="Achatfields"
        :component-attribute="localData"
        @update="update"
        @update:model-value="updateModelValue"/>
</template>
