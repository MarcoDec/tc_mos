<script  setup>
    const options = [
        {text: 'aaaaa', value: 'aaaaa'},
        {text: 'bbbb', value: 'bbbb'}
    ]
    const Productionfields = [
        {label: 'équipe', name: 'équipe', options: {label: value => options.find(option => option.type === value)?.text ?? null, options}, type: 'select'}
    ]
    const Accèsfields = [
        {label: 'identifiant', name: 'identifiant', type: 'text'},
        {label: 'Mot de passe', name: 'MotDePasse', type: 'text'}
    ]
    const Informationsfields = [
        {label: 'Nom', name: 'Lastname', type: 'text'},
        {label: 'Prenom', name: 'FirsName', type: 'text'},
        {label: 'Email', name: 'Email', type: 'text'},
        {label: 'Complément d\'adresse', name: 'ComplémentAdresse', type: 'text'},
        {label: 'Code postal', name: 'CodePostal', type: 'text'},
        {label: 'Ville', name: 'Ville', type: 'text'},
        {label: 'Pays', name: 'pays', options: {label: value => options.find(option => option.type === value)?.text ?? null, options}, type: 'select'},
        {label: 'Fax', name: 'Fax', type: 'text'},
        {label: 'Naissance', name: 'Naissance', type: 'date'},
        {label: 'Initiales', name: 'Initiales', type: 'text'},
        {label: 'Genre', name: 'Genre', options: {label: value => options.find(option => option.type === value)?.text ?? null, options}, type: 'select'},
        {label: 'Numéro social', name: 'NuméroSocial', type: 'text'},
        {label: 'Situation', name: 'Situation', options: {label: value => options.find(option => option.type === value)?.text ?? null, options}, type: 'select'},
        {label: 'Étude', name: 'Étude', type: 'text'},
        {label: 'Date arrivée', name: 'DateArrivée', type: 'date'}
    ]
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab id="gui-start-main" active title="Généralités" icon="pencil">
            <AppCardShow id="addGeneralites"/>
        </AppTab>
        <AppTab id="gui-start-Informations" title="Informations personelles" icon="circle-info">
            <AppCardShow id="addInfo" :fields="Informationsfields"/>
        </AppTab>
        <AppTab id="gui-start-contacts" title="Contacts" icon="file-contract">
            <AppCardShow id="addContacts"/>
        </AppTab>
        <AppTab id="gui-start-droits" title="Droits" icon="clipboard-check">
            <AppCardShow id="addDroits"/>
        </AppTab>
        <AppTab id="gui-start-accés" title="Accés" icon="arrow-right-to-bracket">
            <AppCardShow id="addAccés" :fields="Accèsfields"/>
        </AppTab>
        <AppTab id="gui-start-files" title="Fichiers" icon="laptop">
            <AppCardShow id="addFichiers"/>
        </AppTab>
        <AppTab id="gui-start-production" title="Production" icon="industry">
            <AppCardShow id="addProduction" :fields="Productionfields"/>
        </AppTab>
    </AppTabs>
</template>
