<script setup>
    const options = [
        {text: 'aaaaa', value: 'aaaaa'},
        {text: 'bbbb', value: 'bbbb'}
    ]
    const Qualitéfields = [
        {label: 'Qualité', name: 'Qualité', type: 'number'},
        {label: 'Nb PPM', name: 'NbPPM', type: 'number'},
        {label: 'url *', name: 'url', type: 'text'},
        {label: 'ident', name: 'ident', type: 'text'},
        {label: 'password', name: 'password', type: 'password'}
    ]
    const Logistiquefields = [
        {label: 'Nombre de bordereau ', name: 'NombreBordereau', type: 'number'},
        {label: 'DuréeTransport', name: 'DuréeTransport', type: 'number'},
        {label: 'Encours maximum', name: 'EncourMaximum', type: 'number'},
        {label: 'url *', name: 'url', type: 'text'},
        {label: 'ident', name: 'ident', type: 'text'},
        {label: 'password', name: 'password', type: 'password'},
        {label: 'incoterm', name: 'incoterm', options: {label: value => options.find(option => option.type === value)?.text ?? null, options}, type: 'select'},
        {label: 'commande minimum', name: 'commandeMinimum', type: 'number'}
    ]
    const Comptabilitéfields = [
        {label: 'compte de comptabilité', name: 'compteComptabilité', type: 'text'},
        {label: 'forcer la TVA', name: 'forcerTVA', options: {label: value => options.find(option => option.type === value)?.text ?? null, options}, type: 'select'},
        {label: 'montant de la facture minimum', name: 'montantFactureMinimum', type: 'text'},
        {label: 'message TVA', name: 'messageTVA', options: {label: value => options.find(option => option.type === value)?.text ?? null, options}, type: 'select'},
        {label: 'conditions de paiement', name: 'conditionsPaiement', options: {label: value => options.find(option => option.type === value)?.text ?? null, options}, type: 'select'},
        {label: 'nb de factures', name: 'nbFactures', type: 'number'},
        {label: 'url *', name: 'url', type: 'text'},
        {label: 'ident', name: 'ident', type: 'text'},
        {label: 'password', name: 'password', type: 'password'},
        {label: 'Envoi facture par email', name: 'EnvoiFactureEmail', type: 'boolean'}
    ]
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab id="gui-start-main" active title="Généralités" icon="pencil" tabs="gui-start">
            <AppCardShow id="addGeneralites"/>
        </AppTab>
        <AppTab id="gui-start-files" title="Fichiers" icon="laptop" tabs="gui-start">
            <AppCardShow id="addFichiers"/>
        </AppTab>
        <AppTab id="gui-start-quality" title="Qualité" icon="certificate" tabs="gui-start">
            <AppCardShow id="addQualite" :fields="Qualitéfields"/>
        </AppTab>
        <AppTab id="gui-start-purchase-logistics" title="Logistique" icon="pallet" tabs="gui-start">
            <AppCardShow id="addLogistique" :fields="Logistiquefields"/>
        </AppTab>
        <AppTab id="gui-start-accounting" title="Comptabilité" icon="industry" tabs="gui-start">
            <AppCardShow id="addComptabilite" :fields="Comptabilitéfields"/>
        </AppTab>
        <AppTab id="gui-start-addresses" title="Adresses" icon="location-dot" tabs="gui-start">
            <AppCardShow id="addAdresses"/>
        </AppTab>
        <AppTab id="gui-start-contacts" title="Contacts" icon="file-contract" tabs="gui-start">
            <AppCardShow id="addContacts"/>
        </AppTab>
    </AppTabs>
</template>
