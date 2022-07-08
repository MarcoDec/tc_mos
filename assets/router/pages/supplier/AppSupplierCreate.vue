<script setup>
    import {computed, provide, ref} from 'vue-demi'
    import useCountries from '../../../stores/countries/countries'

    const emit = defineEmits(['update:modelValue'])

    const pays = ref('fr')
    const val = computed(() => pays.value)
    const countries = useCountries()
    provide('country', val)

    const fields = computed(() => [
        {label: 'Groupe', name: 'groupe'},
        {label: 'Nom', name: 'nom'},
        {label: 'Adresse', name: 'adresse'},
        {label: 'complément d\'adresse', name: 'adresse2'},
        {label: 'Code postal', name: 'code'},
        {label: 'Pays', name: 'pays', options: countries, type: 'select'},
        {label: 'Téléphone', name: 'tel', type: 'phone'},
        {label: 'Email', name: 'email'},
        {label: 'Site web', name: 'site'}
    ])

    const fieldsQte = computed(() => [
        {label: 'Gestion en production', name: 'gestion', type: 'boolean'},
        {label: 'Gest. qualité', name: 'qte', type: 'boolean'},
        {label: 'Incoterms   ', name: 'incoterms'},
        {label: 'Qualité ', name: 'quantite'},
        {label: 'Open orders enabled', name: 'open', type: 'boolean'}
    ])
    const fieldsComp = computed(() => [
        {label: 'AR demandé*', name: 'ar', type: 'boolean'},
        {label: 'Devise*', name: 'devise'},
        {label: 'TVA*', name: 'tva'},
        {label: 'Open orders enabled*', name: 'open', type: 'boolean'}
    ])
    const fieldsCuivre = computed(() => [
        {label: 'CopperIndex', name: 'copperIndex ', type: 'number'},
        {label: 'CopperType', name: 'copperType'},
        {label: 'Open orders enabled*', name: 'open', type: 'boolean'}
    ])

    function input(e) {
        emit('update:modelValue', e.target.value)
        pays.value = e.target.value
    }
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab id="gui-start-general" active icon="sitemap" title="Général">
            <AppForm
                id="supplier"
                :fields="fields"
                country-field="pays"
                @input="input"/>
        </AppTab>
        <AppTab id="gui-start-qte" icon="folder" title="Qualité">
            <AppForm id="qte" :fields="fieldsQte"/>
        </AppTab>
        <AppTab id="gui-start-comptabilite" icon="chart-line" title="Comptabilité">
            <AppForm id="comptabilite" :fields="fieldsComp"/>
        </AppTab>
        <AppTab id="gui-start-cuivre" icon="clipboard-list" title="Cuivre">
            <AppForm id="cuivre" :fields="fieldsCuivre"/>
        </AppTab>
    </AppTabs>
</template>

<style>
.cardOrderSupplier {
  border: 6px solid #1d583d;
}
</style>
