<script lang="ts" setup>
    import type {FormField} from '../../../types/bootstrap-5'

    const Qualitéfields: FormField[] = [
        {label: 'Qualité', name: 'Qualité', type: 'number'},
        {label: 'Nb PPM', name: 'NbPPM', type: 'number'},
        {label: 'url *', name: 'url', type: 'text'},
        {label: 'ident', name: 'ident', type: 'text'},
        {label: 'password', name: 'password', type: 'password'}
    ]
    const Logistiquefields: FormField[] = [
        {label: 'Nombre de bordereau ', name: 'NombreBordereau', type: 'number'},
        {label: 'DuréeTransport', name: 'DuréeTransport', type: 'number'},
        {label: 'Encours maximum', name: 'EncourMaximum', type: 'number'},
        {label: 'url *', name: 'url', type: 'text'},
        {label: 'ident', name: 'ident', type: 'text'},
        {label: 'password', name: 'password', type: 'password'},
        {label: 'incoterm', name: 'incoterm', type: 'select'},
        {label: 'commande minimum', name: 'commandeMinimum', type: 'number'}
    ]
    const Comptabilitéfields: FormField[] = [
        {label: 'compte de comptabilité', name: 'compteComptabilité', type: 'text'},
        {label: 'forcer la TVA', name: 'forcerTVA', type: 'select'},
        {label: 'montant de la facture minimum', name: 'montantFactureMinimum', type: 'text'},
        {label: 'message TVA', name: 'messageTVA', type: 'select'},
        {label: 'conditions de paiement', name: 'conditionsPaiement', type: 'select'},
        {label: 'nb de factures', name: 'nbFactures', type: 'number'},
        {label: 'url *', name: 'url', type: 'text'},
        {label: 'ident', name: 'ident', type: 'text'},
        {label: 'password', name: 'password', type: 'password'},
        {label: 'Envoi facture par email', name: 'EnvoiFactureEmail', type: 'boolean'}
    ]
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab id="gui-start-main" active title="Généralités">
            <AppCardShow/>
        </AppTab>
        <AppTab id="gui-start-files" title="Fichiers">
            <AppCardShow/>
        </AppTab>
        <AppTab id="gui-start-quality" title="Qualité">
            <AppCardShow :fields="Qualitéfields"/>
        </AppTab>
        <AppTab id="gui-start-purchase-logistics" title="Logistique">
            <AppCardShow :fields="Logistiquefields"/>
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
