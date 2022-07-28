<script setup>
    const options = [
        {text: 'aaaaa', value: 'aaaaa'},
        {text: 'bbbb', value: 'bbbb'}
    ]
    const Attributfields = [
        {label: 'Couleur', name: 'couleur', type: 'text'},
        {label: 'T° maxi (°C)', name: 'temperatureMaxi', type: 'number'},
        {label: 'Nombre des brins', name: 'NombreBrins', type: 'number'},
        {label: 'Voltage (V)', name: 'Voltage', type: 'text'},
        {label: 'Dia ext maxi (mm)', name: 'DiaMaxi', type: 'number'},
        {label: 'Norme d\'appellation', name: 'NormeAppellation', type: 'text'},
        {label: 'Nombre des conducteurs', name: 'NombreConducteurs', type: 'number'},
        {label: 'Section (mm²)', name: 'MatièreBrins', type: 'text'},
        {label: 'T° mini (°C)', name: 'temperatureMini', type: 'number'},
        {label: 'Matière d\'isolant', name: 'MatièreIsolant', type: 'text'},
        {label: 'needJoint', name: 'needJoint', type: 'boolean'}
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
        {label: 'Code douanier', name: 'CodeDouanier', type: 'text'},
        {label: 'Poids', name: 'Poids', type: 'text'},
        {label: 'Unité', name: 'unité', options: {label: value => options.find(option => option.type === value)?.text ?? null, options}, type: 'select'},
        {label: 'Volume Prévisionnel', name: 'VolumePrévisionnel', type: 'number'},
        {label: 'Stock Minimum', name: 'StockMinimum', type: 'number'},
        {label: 'gestionStock', name: 'gestionStock', type: 'boolean'}
    ]
    const Spécificationfields = [
        {label: 'Prix', name: 'Prix', type: 'text'},
        {label: 'Poids Cuivre', name: 'PoidsCuivre', type: 'text'},
        {label: 'Info Cmde', name: 'InfoCmde', type: 'text'}
    ]
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab id="gui-start-main" active title="Généralités" icon="pencil">
            <AppCardShow id="addGeneralites"/>
        </AppTab>
        <AppTab id="gui-start-attribut" title="Attribut" icon="sitemap">
            <AppCardShow id="addAttribut" :fields="Attributfields"/>
        </AppTab>
        <AppTab id="gui-start-files" title="Fichiers" icon="laptop">
            <AppCardShow id="addFichiers"/>
        </AppTab>
        <AppTab id="gui-start-quality" title="Qualité" icon="certificate">
            <AppCardShow id="addQualite" :fields="Qualitéfields"/>
        </AppTab>
        <AppTab id="gui-start-achat" title="Achat" icon="bag-shopping">
            <AppCardShow id="addAchat" :fields="Achatfields"/>
        </AppTab>
        <AppTab id="gui-start-logistics" title="Logistique" icon="pallet">
            <AppCardShow id="addLogistique" :fields="Logistiquefields"/>
        </AppTab>
        <AppTab id="gui-start-spécifications" title="Spécification" icon="file-contract">
            <AppCardShow id="addSpécification" :fields="Spécificationfields"/>
        </AppTab>
    </AppTabs>
</template>
