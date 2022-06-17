<script lang="ts" setup>
    import type {FormField} from '../../../types/bootstrap-5'

    const Contactsfields: FormField[] = [
        {label: 'supplierRef', name: 'supplierRef', type: 'text'},
        {label: 'serialNumber', name: 'serialNumber', type: 'text'},
        {label: 'manufacturingDate', name: 'manufacturingDate', type: 'date'},
        {label: 'manufacturer', name: 'manufacturer', type: 'select'},
        {label: 'Code postal', name: 'CodePostal', type: 'text'},
        {label: 'entryDate', name: 'entryDate', type: 'date'}
    ]
    const Qualitéfields: FormField[] = [
        {label: 'Hauteur de sertissage (mm)', name: 'HauteurSertissage', type: 'text'},
        {label: 'Largeur de sertissage (mm)', name: 'LargeurSertissage', type: 'text'},
        {label: 'Paramètre de mesure', name: 'ParamètreMesure', type: 'text'}
    ]
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab id="gui-start-main" active title="Généralités">
            <AppCardShow/>
        </AppTab>
        <AppTab id="gui-start-quality" title="Qualité">
            <AppCardShow :fields="Qualitéfields"/>
        </AppTab>
        <AppTab id="gui-start-purchase-logistics" title="Logistique">
            <AppCardShow/>
        </AppTab>
        <AppTab id="gui-start-addresses" title="Adresses\Contacts">
            <AppCardShow :fields="Contactsfields"/>
        </AppTab>
    </AppTabs>
</template>

