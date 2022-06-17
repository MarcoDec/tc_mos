<script lang="ts" setup>
    import type {FormField} from '../../../types/bootstrap-5'

    const Géneralitésfields: FormField[] = [
        {label: 'Gestion en production', name: 'GestionProd', type: 'boolean'}
    ]
    const Qualitéfields: FormField[] = [
        {label: 'Gestion de la qualité', name: 'GestionQualité', type: 'boolean'},
        {label: 'Niveau de confiance', name: 'NiveauConfiance', type: 'rating'},
        {label: 'Taux de PPM', name: 'TauxPPM', type: 'number'}
    ]
    const Achatfields: FormField[] = [
        {label: 'Minimum de commande', name: 'MinimumCommande', type: 'number'},
        {label: 'Incoterm', name: 'Incoterm', type: 'select'},
        {label: 'Gestion du cuivre', name: 'GestionCuivre', type: 'boolean'},
        {label: 'Niveau de confiance', name: 'NiveauConfiance', type: 'rating'},
        {label: 'Commande ouverte', name: 'CommandeOuverte', type: 'boolean'},
        {label: 'Accusé de réception', name: 'AccuséRéception', type: 'boolean'}
    ]
    const Comptabilitéfields: FormField[] = [
        {label: 'Montant minimum de facture', name: 'MontantMinimumFacture', type: 'number'},
        {label: 'Devise', name: 'Devise', type: 'select'},
        {label: 'Compte de comptabilité', name: 'CompteComptabilité', type: 'text'},
        {label: 'TVA', name: 'TVA', type: 'text'},
        {label: 'Forcer la TVA', name: 'ForcerTVA', type: 'select'},
        {label: 'Message TVA', name: 'MessageTVA', options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}], type: 'select'}
    ]
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab id="gui-start-main" active title="Généralités">
            <AppCardShow :fields="Géneralitésfields"/>
        </AppTab>
        <AppTab id="gui-start-files" title="Fichiers">
            <AppCardShow/>
        </AppTab>
        <AppTab id="gui-start-quality" title="Qualité">
            <AppCardShow :fields="Qualitéfields"/>
        </AppTab>
        <AppTab id="gui-start-purchase-logistics" title="Achat/Logistique">
            <AppCardShow :fields="Achatfields"/>
        </AppTab>
        <AppTab id="gui-start-accounting" title="Comptabilité">
            <AppCardShow :fields="Comptabilitéfields"/>
        </AppTab>
        <AppTab id="gui-start-addresses" title="Adresses">
            <AppCardShow/>
        </AppTab>
        <AppTab id="gui-start-contacts" title="Contacts">
            <AppCardShow/>
        </AppTab>
    </AppTabs>
</template>
