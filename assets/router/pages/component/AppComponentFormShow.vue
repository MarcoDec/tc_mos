<script setup>
    import {computed, onUnmounted} from 'vue'
    import AppCardShow from '../../../components/AppCardShow.vue'
    import AppTab from '../../../components/tab/AppTab.vue'
    import AppTabs from '../../../components/tab/AppTabs.vue'
    import generateComponentAttribute from '../../../stores/component/componentAttribute'
    import {useColorsStore} from '../../../stores/colors/colors'
    import {useComponentAttachmentStore} from '../../../stores/component/componentAttachment'
    import {useComponentListStore} from '../../../stores/component/components'
    import {useComponentShowStore} from '../../../stores/component/componentAttributesList'
    import useOptions from '../../../stores/option/options'

    const fecthOptions = useOptions('units')
    const fecthColors = useColorsStore()
    await fecthOptions.fetch()
    await fecthColors.fetch()

    const optionsAtt = computed(() => fecthOptions.options.map(op => {
        const text = op.text
        const value = op.value
        const optionList = {text, value}
        return optionList
    }))
    const optionsColors = computed(() => fecthColors.colors.map(op => {
        const text = op.name
        const value = op.id
        const optionList = {text, value}
        return optionList
    }))

    const useFetchComponentStore = useComponentListStore()
    const useComponentStore = useComponentShowStore()
    const fetchComponentAttachment = useComponentAttachmentStore()
    await useComponentStore.fetch()
    fetchComponentAttachment.fetch()
    useFetchComponentStore.fetch()

    const Attributfields = [
        {label: 'Couleur', name: 'getColor', options: {
             label: value => optionsColors.value.find(option => option.type === value)?.text ?? null,
             options: optionsColors.value
         },
         type: 'select'},
        {label: 'T° maxi (°C)', name: 'temperatureMaxi', type: 'number'},
        {label: 'Nombre des brins', name: 'NombreBrins', type: 'number'},
        {label: 'Voltage (V)', name: 'Voltage', type: 'text'},
        {label: 'Dia ext maxi (mm)', name: 'DiaMaxi', type: 'number'},
        {label: 'Norme d\'appellation', name: 'Norme', type: 'text'},
        {
            label: 'Nombre des conducteurs',
            name: 'NombreConducteurs',
            type: 'number'
        },
        {label: 'Section (mm²)', name: 'MatièreBrins', type: 'text'},
        {label: 'T° mini (°C)', name: 'temperatureMini', type: 'number'},
        {label: 'Matière d\'isolant', name: 'MatièreIsolant', type: 'text'},
        {label: 'needJoint', name: 'needJoint', type: 'boolean'}
    ]
    const Fichiersfields = [
        {label: 'Fichier', name: 'file', type: 'file'}

    ]
    const Qualitéfields = [
        {label: 'rohs ', name: 'rohs', type: 'boolean'},
        {label: 'rohsAttachment', name: 'rohsAttachment', type: 'text'},
        {label: 'reach', name: 'reach', type: 'boolean'},
        {label: 'reachAttachment', name: 'reachAttachment', type: 'text'},
        {label: 'Qualité', name: 'Qualité', type: 'number'}
    ]
    const Achatfields = [
        {label: 'Fabricant', name: 'Fabricant', type: 'text'},
        {label: 'Référence du Fabricant', name: 'RéférenceFabricant', type: 'text'}
    ]
    const Logistiquefields = [
        {label: 'Code douanier', name: 'code', type: 'text'},
        {label: 'Poids', name: 'weight', type: 'measure'},
        {
            label: 'Unité',
            name: 'unit',
            options: {
                label: value => optionsAtt.value.find(option => option.type === value)?.text ?? null,
                options: optionsAtt.value
            },
            type: 'select'
        },
        {label: 'Volume Prévisionnel', name: 'forecastVolume', type: 'number'},
        {label: 'Stock Minimum', name: 'minStock', type: 'number'},
        {label: 'gestionStock', name: 'managedStock', type: 'boolean'}
    ]

    const Spécificationfields = [
        {label: 'Prix', name: 'Prix', type: 'text'},
        {label: 'Poids Cuivre', name: 'weight', type: 'measure'},
        {label: 'Info Cmde', name: 'orderInfo', type: 'text'}
    ]

    async function update(value){
        const form = document.getElementById('addAttribut')
        const formData = new FormData(form)
        const data = {
            color: `/api/colors/${formData.get('getColor')}`,
            measure: {
                code: 'U',
                value: 1
            },
            value: 'string'
        }
        const item = generateComponentAttribute(value)

        await item.updateAttributes(data)

        await useComponentStore.fetch()
    }
    function updateLogistique(value){
        console.log('update logistique value==', value)
        const componentId = Number(value['@id'].match(/\d+/)[0])
        const form = document.getElementById('addLogistique')
        const formData = new FormData(form)
        const data = {

            family: '/api/component-families/4',
            forecastVolume: {
                code: 'U',
                value: formData.get('forecastVolume')
            },
            managedStock: formData.get('managedStock'),

            minStock: {
                code: 'U',
                value: formData.get('minStock')
            },
            unit: formData.get('unit'),
            weight: {
                code: formData.get('weight-code'),
                value: formData.get('weight-value')
            }
        }
        console.log('data==', data)

        //useFetchComponentStore.update(data, componentId)
    }
    function updateSpecification(value){
        console.log('updateSpecification value==', value)
        const componentId = Number(value['@id'].match(/\d+/)[0])
        const form = document.getElementById('addSpécification')
        const formData = new FormData(form)

        const data = {
            orderInfo: formData.get('orderInfo'),
            weight: {
                code: formData.get('weight-code'),
                value: formData.get('weight-value')
            }
        }
        console.log('data**', data)

        //useFetchComponentStore.update(data, componentId)
    }
    function updateFichiers(value){
        console.log('updateFichiers value==', value)
        const componentId = Number(value['@id'].match(/\d+/)[0])
        const form = document.getElementById('addFichiers')
        const formData = new FormData(form)
        console.log('formData**', formData.get('file'))

        const data = {
            category: 'doc',
            component: `/api/components/${componentId}`,
            file: formData.get('file')
        }
        console.log('data Fichiers**', data)

        fetchComponentAttachment.ajout(data)
    }

    onUnmounted(() => {
        fecthOptions.dispose()
    })
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab
            id="gui-start-main"
            active
            title="Généralités"
            icon="pencil"
            tabs="gui-start">
            <AppCardShow id="addGeneralites"/>
        </AppTab>
        <AppTab
            id="gui-start-attribut"
            title="Attribut"
            icon="sitemap"
            tabs="gui-start">
            <AppCardShow v-for="item in useComponentStore.componentAttribute" id="addAttribut" :key="item.id" :fields="Attributfields" :component-attribute="item" @update="update(item)"/>
        </AppTab>
        <AppTab
            id="gui-start-files"
            title="Fichiers"
            icon="laptop"
            tabs="gui-start">
            <AppCardShow id="addFichiers" :fields="Fichiersfields" @update="updateFichiers(useFetchComponentStore.component)"/>
        </AppTab>
        <AppTab
            id="gui-start-quality"
            title="Qualité"
            icon="certificate"
            tabs="gui-start">
            <AppCardShow id="addQualite" :fields="Qualitéfields"/>
        </AppTab>
        <AppTab
            id="gui-start-achat"
            title="Achat"
            icon="bag-shopping"
            tabs="gui-start">
            <AppCardShow id="addAchat" :fields="Achatfields"/>
        </AppTab>
        <AppTab
            id="gui-start-logistics"
            title="Logistique"
            icon="pallet"
            tabs="gui-start">
            <AppCardShow id="addLogistique" :fields="Logistiquefields" :component-attribute="useFetchComponentStore.component" @update="updateLogistique(useFetchComponentStore.component)"/>
        </AppTab>
        <AppTab
            id="gui-start-spécifications"
            title="Spécification"
            icon="file-contract"
            tabs="gui-start">
            <AppCardShow id="addSpécification" :fields="Spécificationfields" :component-attribute="useFetchComponentStore.component" @update="updateSpecification(useFetchComponentStore.component)"/>
        </AppTab>
    </AppTabs>
</template>
