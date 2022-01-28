<script lang="ts" setup>
    import type {FormField} from '../../../types/bootstrap-5'

    const Productionfields: FormField[] = [
        {label: 'équipe', name: 'équipe', type: 'select'}
    ]
    const Accèsfields: FormField[] = [
        {label: 'identifiant', name: 'identifiant', type: 'text'},
        {label: 'Mot de passe', name: 'MotDePasse', type: 'text'}
    ]
    const Informationsfields: FormField[] = [
        {label: 'Nom', name: 'Lastname', type: 'text'},
        {label: 'Prenom', name: 'FirsName', type: 'text'},
        {label: 'Email', name: 'Email', type: 'text'},
        {label: 'Complément d\'adresse', name: 'ComplémentAdresse', type: 'text'},
        {label: 'Code postal', name: 'CodePostal', type: 'text'},
        {label: 'Ville', name: 'Ville', type: 'text'},
        {label: 'Pays', name: 'pays', type: 'select'},
        {label: 'Fax', name: 'Fax', type: 'text'},
        {label: 'Naissance', name: 'Naissance', type: 'date'},
        {label: 'Initiales', name: 'Initiales', type: 'text'},
        {label: 'Genre', name: 'Genre', type: 'select'},
        {label: 'Numéro social', name: 'NuméroSocial', type: 'text'},
        {label: 'Situation', name: 'Situation', type: 'select'},
        {label: 'Étude', name: 'Étude', type: 'text'},
        {label: 'Date arrivée', name: 'DateArrivée', type: 'date'}
    ]
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab id="gui-start-main" active title="Généralités">
            <AppCardShow/>
        </AppTab>
        <AppTab id="gui-start-Informations" title="Informations personelles">
            <AppCardShow :fields="Informationsfields"/>
        </AppTab>
        <AppTab id="gui-start-contacts" title="Contacts">
            <AppCardShow/>
        </AppTab>
        <AppTab id="gui-start-droits" title="Droits">
            <AppCardShow/>
        </AppTab>
        <AppTab id="gui-start-accés" title="Accés">
            <AppCardShow :fields="Accèsfields"/>
        </AppTab>
        <AppTab id="gui-start-files" title="Fichiers">
            <AppCardShow/>
        </AppTab>
        <AppTab id="gui-start-production" title="Production">
            <AppCardShow :fields="Productionfields"/>
        </AppTab>
    </AppTabs>
</template>
