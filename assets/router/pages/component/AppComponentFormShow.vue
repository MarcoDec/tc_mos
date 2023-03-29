<script setup>
    import AppCardShow from '../../../components/AppCardShow.vue'
    import AppTab from '../../../components/tab/AppTab.vue'
    import AppTabs from '../../../components/tab/AppTabs.vue'
    import {useComponentListStore} from '../../../stores/component/components'
    import {useComponentShowStore} from '../../../stores/component/componentAttributesList'
    import {useComponentAttachmentStore} from '../../../stores/component/componentAttachment'
    import {computed} from 'vue'
   
    const useFetchComponentStore = useComponentListStore()
    const useComponentStore = useComponentShowStore()
    const fetchComponentAttachment = useComponentAttachmentStore()
    useComponentStore.fetch()
    fetchComponentAttachment.fetch()
    useFetchComponentStore.fetch()
    // const weight = computed(()=> useFetchComponentStore.getWeight)
    // console.log('ddd', weight);
    const options = [
        {text: 'aaaaa', value: 'aaaaa'},
        {text: 'bbbb', value: 'bbbb'}
    ]
    const Attributfields = [
        {label: 'Couleur', name: 'color', type: 'text'},
        {label: 'T° maxi (°C)', name: 'temperatureMaxi', type: 'number'},
        {label: 'Nombre des brins', name: 'NombreBrins', type: 'number'},
        {label: 'Voltage (V)', name: 'Voltage', type: 'text'},
        {label: 'Dia ext maxi (mm)', name: 'DiaMaxi', type: 'number'},
        {label: 'Norme d\'appellation', name: 'NormeAppellation', type: 'text'},
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
                label: value =>
                    options.find(option => option.type === value)?.text ?? null,
                options
            },
            type: 'select'
        },
        {label: 'Volume Prévisionnel', name: 'forecastVolume', type: 'number'},
        {label: 'Stock Minimum', name: 'minStock', type: 'number'},
        {label: 'gestionStock', name: 'managedStock', type: 'boolean'}
    ]

    const Spécificationfields = [
        {label: 'Prix', name: 'Prix', type: 'text'},
        {label: 'Poids Cuivre', name: 'PoidsCuivre', type: 'text'},
        {label: 'Info Cmde', name: 'InfoCmde', type: 'text'}
    ]

   

    function update(value){
        console.log('je suis ici to update');
        console.log('value==', value.id);
        const form = document.getElementById('addAttribut')
        const formData= new FormData(form)
        const data = {
        color: formData.get('color'),
        measure: {
            code: 'U',
            value: 1
        },
        value: 'string'
    }
    useComponentStore.update(data, value.id)

 }
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
            <AppCardShow v-for="item in useComponentStore.componentAttribute" id="addAttribut" :key="item" :fields="Attributfields"  :component-attribute="item" @update="update(item)"/>
        </AppTab>
        <AppTab
            id="gui-start-files"
            title="Fichiers"
            icon="laptop"
            tabs="gui-start">
            <AppCardShow id="addFichiers" :fields="Fichiersfields"/>
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
            <AppCardShow id="addLogistique" :fields="Logistiquefields" :component-attribute="useFetchComponentStore.component"/>
        </AppTab>
        <AppTab
            id="gui-start-spécifications"
            title="Spécification"
            icon="file-contract"
            tabs="gui-start">
            <AppCardShow id="addSpécification" :fields="Spécificationfields"/>
        </AppTab>
    </AppTabs>
</template>
