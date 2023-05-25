<script setup>
    import {useEngineStore} from '../../../stores/engine/engines'

    const fecthEngineStore = useEngineStore()
    await fecthEngineStore.fetchOne()

    const options = [
        {text: 'aaaaa', value: 'aaaaa'},
        {text: 'bbbb', value: 'bbbb'}
    ]
    const Contactsfields = [
        {label: 'supplierRef', name: 'supplierRef', type: 'text'},
        {label: 'serialNumber', name: 'serialNumber', type: 'text'},
        {label: 'manufacturingDate', name: 'manufacturingDate', type: 'date'},
        {label: 'manufacturer', name: 'manufacturer', options: {label: value => options.find(option => option.type === value)?.text ?? null, options}, type: 'select'},
        {label: 'Code postal', name: 'CodePostal', type: 'text'},
        {label: 'entryDate', name: 'entryDate', type: 'date'}
    ]
    const Qualitéfields = [
        {label: 'Hauteur de sertissage (mm)', name: 'HauteurSertissage', type: 'text'},
        {label: 'Largeur de sertissage (mm)', name: 'LargeurSertissage', type: 'text'},
        {label: 'Paramètre de mesure', name: 'ParamètreMesure', type: 'text'}
    ]
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab id="gui-start-main" active title="Généralités" icon="pencil" tabs="gui-start">
            <AppCardShow id="addGeneralites"/>
        </AppTab>
        <AppTab id="gui-start-quality" title="Qualité" icon="certificate" tabs="gui-start">
            <AppCardShow id="addQualite" :fields="Qualitéfields"/>
        </AppTab>
        <AppTab id="gui-start-purchase-logistics" title="Logistique" icon="pallet" tabs="gui-start">
            <AppCardShow id="addLogistique"/>
        </AppTab>
        <AppTab id="gui-start-addresses" title="Adresses\Contacts" icon="file-contract" tabs="gui-start">
            <AppCardShow id="addContacts" :fields="Contactsfields"/>
        </AppTab>
    </AppTabs>
</template>

