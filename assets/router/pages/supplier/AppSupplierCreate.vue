<script setup>
    import {computed, provide, ref} from 'vue-demi'
    import useCountries from '../../../stores/countries/countries'
    import AppFormJS from '../../../components/form/AppFormJS.js'
    import AppTab from '../../../components/tabs/AppTab.vue'
    import AppTabs from '../../../components/tabs/AppTabs.vue'

    const emit = defineEmits(['update:modelValue'])

    // const pays = ref('fr')
    // const val = computed(() => pays.value)
    const storeCountries = useCountries()
    storeCountries.countryOption()
    const listCountry = computed(() => storeCountries.countriesOption)
    console.log('listCountry', listCountry);
    

    const fields = computed(() => [
        {label: 'Groupe', name: 'groupe', type: 'text'},
        {label: 'Nom', name: 'nom', type: 'text'},
        {label: 'Adresse', name: 'adresse', type: 'text'},
        {label: 'complément d\'adresse', name: 'adresse2'},
        {label: 'Code postal', name: 'code', type: 'text'},
        // {label: 'Pays', name: 'pays', options: countries, type: 'select'},
        {label: 'Pays*', name: 'pays',options: {label: value =>listCountry.value.find(option => option.type === value)?.text ?? null, options: listCountry.value}, type: 'select'},
        {label: 'Téléphone', name: 'tel', type: 'text'},
        {label: 'Email', name: 'email', type: 'text'},
        {label: 'Site web', name: 'site', type: 'text'}
    ])

    const fieldsQte = computed(() => [
        {label: 'Gestion en production', name: 'gestion', type: 'boolean'},
        {label: 'Gest. qualité', name: 'qte', type: 'boolean'},
        {label: 'Incoterms   ', name: 'incoterms', type: 'text'},
        {label: 'Qualité ', name: 'quantite', type: 'text'},
        {label: 'Open orders enabled', name: 'open', type: 'boolean'}
    ])
    const fieldsComp = computed(() => [
        {label: 'AR demandé*', name: 'ar', type: 'boolean'},
        {label: 'Devise*', name: 'devise', type: 'text'},
        {label: 'TVA*', name: 'tva', type: 'text'},
        {label: 'Open orders enabled*', name: 'open', type: 'boolean'}
    ])
    const fieldsCuivre = computed(() => [
        {label: 'CopperIndex', name: 'copperIndex ', type: 'number'},
        {label: 'CopperType', name: 'copperType', type: 'text'},
        {label: 'Open orders enabled*', name: 'open', type: 'boolean'}
    ])

    function input(e) {
        emit('update:modelValue', e.target.value)
        // pays.value = e.target.value
    }
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab id="gui-start-general" active icon="sitemap" title="Général">
            <AppFormJS
                id="supplier"
                :fields="fields"
                @input="input"/>
        </AppTab>
        <AppTab id="gui-start-qte" icon="folder" title="Qualité"> 
            <AppFormJS id="qte" :fields="fieldsQte"/>
        </AppTab>
        <AppTab id="gui-start-comptabilite" icon="chart-line" title="Comptabilité">
            <AppFormJS id="comptabilite" :fields="fieldsComp"/>
        </AppTab>
        <AppTab id="gui-start-cuivre" icon="clipboard-list" title="Cuivre">
            <AppFormJS id="cuivre" :fields="fieldsCuivre"/>
        </AppTab> 
    </AppTabs>
</template>

<style>
.cardOrderSupplier {
  border: 6px solid #1d583d;
}
</style>
