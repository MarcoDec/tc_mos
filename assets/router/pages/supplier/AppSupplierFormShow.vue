<script setup>
    const options = [
        {text: 'aaaaa', value: 'aaaaa'},
        {text: 'bbbb', value: 'bbbb'}
    ]
    const Géneralitésfields = [
        {label: 'Gestion en production', name: 'GestionProd', type: 'boolean'}
    ]
    const Qualitéfields = [
        {label: 'Gestion de la qualité', name: 'GestionQualité', type: 'boolean'},
        {label: 'Niveau de confiance', name: 'NiveauConfiance', type: 'rating'},
        {label: 'Taux de PPM', name: 'TauxPPM', type: 'number'}
    ]
    const Achatfields = [
        {label: 'Minimum de commande', name: 'MinimumCommande', type: 'number'},
        {label: 'Incoterm', name: 'Incoterm', options: {label: value => options.find(option => option.type === value)?.text ?? null, options}, type: 'select'},
        {label: 'Gestion du cuivre', name: 'GestionCuivre', type: 'boolean'},
        {label: 'Niveau de confiance', name: 'NiveauConfiance', type: 'rating'},
        {label: 'Commande ouverte', name: 'CommandeOuverte', type: 'boolean'},
        {label: 'Accusé de réception', name: 'AccuséRéception', type: 'boolean'}
    ]
    const Comptabilitéfields = [
        {label: 'Montant minimum de facture', name: 'MontantMinimumFacture', type: 'number'},
        {label: 'Devise', name: 'Devise', options: {label: value => options.find(option => option.type === value)?.text ?? null, options}, type: 'select'},
        {label: 'Compte de comptabilité', name: 'CompteComptabilité', type: 'text'},
        {label: 'TVA', name: 'TVA', type: 'text'},
        {label: 'Forcer la TVA', name: 'ForcerTVA', options: {label: value => options.find(option => option.type === value)?.text ?? null, options}, type: 'select'},
        {label: 'Message TVA', name: 'MessageTVA', options: {label: value => options.find(option => option.type === value)?.text ?? null, options}, type: 'select'}
    ]
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab id="gui-start-main" active title="Généralités" icon="pencil">
            <AppCardShow id="addGeneralites" :fields="Géneralitésfields"/>
        </AppTab>
        <AppTab id="gui-start-files" title="Fichiers" icon="laptop">
            <AppCardShow id="addFichiers"/>
        </AppTab>
        <AppTab id="gui-start-quality" title="Qualité" icon="certificate">
            <AppCardShow id="addQualite" :fields="Qualitéfields"/>
        </AppTab>
        <AppTab id="gui-start-purchase-logistics" title="Achat/Logistique" icon="bag-shopping">
            <AppCardShow id="addAchatLogistique" :fields="Achatfields"/>
        </AppTab>
        <AppTab id="gui-start-accounting" title="Comptabilité" icon="industry">
            <AppCardShow id="addComptabilite" :fields="Comptabilitéfields"/>
        </AppTab>
        <AppTab id="gui-start-addresses" title="Adresses" icon="location-dot">
            <AppCardShow id="addAdresses"/>
        </AppTab>
        <AppTab id="gui-start-contacts" title="Contacts" icon="file-contract">
            <AppCardShow id="addContacts"/>
        </AppTab>
    </AppTabs>
</template>
