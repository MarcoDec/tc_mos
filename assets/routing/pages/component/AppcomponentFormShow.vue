<script lang="ts" setup>
    import type {FormField} from '../../../types/bootstrap-5'

    const Attributfields: FormField[] = [
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
    const Qualitéfields: FormField[] = [
        {label: 'rohs ', name: 'rohs', type: 'boolean'},
        {label: 'rohsAttachment', name: 'rohsAttachment', type: 'text'},
        {label: 'reach', name: 'reach', type: 'boolean'},
        {label: 'reachAttachment', name: 'reachAttachment', type: 'text'},
        {label: 'Qualité', name: 'Qualité', type: 'number'}
    ]
    const Achatfields: FormField[] = [
        {label: 'Fabricant', name: 'Fabricant', type: 'text'},
        {label: 'Référence du Fabricant', name: 'RéférenceFabricant', type: 'text'}
    ]
    const Logistiquefields: FormField[] = [
        {label: 'Code douanier', name: 'CodeDouanier', type: 'text'},
        {label: 'Poids', name: 'Poids', type: 'text'},
        {label: 'Unité', name: 'unité', type: 'select'},
        {label: 'Volume Prévisionnel', name: 'VolumePrévisionnel', type: 'number'},
        {label: 'Stock Minimum', name: 'StockMinimum', type: 'number'},
        {label: 'gestionStock', name: 'gestionStock', type: 'boolean'}
    ]
    const Spécificationfields: FormField[] = [
        {label: 'Prix', name: 'Prix', type: 'text'},
        {label: 'Poids Cuivre', name: 'PoidsCuivre', type: 'text'},
        {label: 'Info Cmde', name: 'InfoCmde', type: 'text'}
    ]
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab id="gui-start-main" active title="Généralités">
            <AppCardShow/>
        </AppTab>
        <AppTab id="gui-start-attribut" title="Attribut">
            <AppCardShow :fields="Attributfields"/>
        </AppTab>
        <AppTab id="gui-start-files" title="Fichiers">
            <AppCardShow/>
        </AppTab>
        <AppTab id="gui-start-quality" title="Qualité">
            <AppCardShow :fields="Qualitéfields"/>
        </AppTab>
        <AppTab id="gui-start-achat" title="Achat">
            <AppCardShow :fields="Achatfields"/>
        </AppTab>
        <AppTab id="gui-start-logistics" title="Logistique">
            <AppCardShow :fields="Logistiquefields"/>
        </AppTab>
        <AppTab id="gui-start-spécifications" title="Spécification">
            <AppCardShow :fields="Spécificationfields"/>
        </AppTab>
    </AppTabs>
</template>
