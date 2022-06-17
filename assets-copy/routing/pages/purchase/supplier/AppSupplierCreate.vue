<script lang="ts" setup>
    import type {Actions, Getters} from '../../../../store/countries'
    import type {FormField, FormValue} from '../../../../types/bootstrap-5'
    import {computed, defineEmits, onMounted, reactive} from '@vue/runtime-core'
    import {
        useNamespacedActions,
        useNamespacedGetters
    } from 'vuex-composition-helpers'

    const emit = defineEmits<(e: 'update:modelValue', value: FormValue) => void>()
    const value = reactive({pays: 'fr'})

    const fetchCountry = useNamespacedActions<Actions>('countries', ['fetchCountry']).fetchCountry
    const options = useNamespacedGetters<Getters>('countries', ['options']).options

    const fields = computed<FormField[]>(() => [
        {label: 'Groupe', name: 'groupe'},
        {label: 'Nom ', name: 'nom '},
        {label: 'Adresse  ', name: 'adresse'},
        {label: 'complément d\'adresse', name: 'adresse2'},
        {label: 'Code postal ', name: 'code'},
        {label: 'Pays ', name: 'pays ', options: options.value, type: 'select'},
        {label: 'Téléphone', name: 'tel', type: 'phone'},
        {label: 'Email', name: 'email'},
        {label: 'Site web', name: 'site'}
    ])

    const fieldsQte = computed<FormField[]>(() => [
        {label: 'Gestion en production', name: 'gestion', type: 'boolean'},
        {label: 'Gest. qualité', name: 'qte', type: 'boolean'},
        {label: 'Incoterms   ', name: 'incoterms'},
        {label: 'Qualité ', name: 'quantite'},
        {label: 'Open orders enabled', name: 'open', type: 'boolean'}

    ])

    const fieldsComp = computed<FormField[]>(() => [
        {label: 'AR demandé*', name: 'Ar', type: 'boolean'},
        {label: 'Devise*', name: 'devise'},
        {label: 'TVA*', name: 'tva'},
        {label: 'Open orders enabled*', name: 'open', type: 'boolean'}

    ])
    const fieldsCuivre = computed<FormField[]>(() => [
        {label: 'CopperIndex', name: 'copperIndex ', type: 'number'},
        {label: 'CopperType', name: 'copperType', type: 'select'},
        {label: 'Open orders enabled*', name: 'open', type: 'boolean'}

    ])

    function input(e: Readonly<Event>): void {
        emit('update:modelValue', (e.target as HTMLInputElement).value)
        value.pays = (e.target as HTMLInputElement).value
    }
    onMounted(async () => {
        await fetchCountry()
    })
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab id="gui-start-general" active icon="sitemap" title="Général">
            <AppForm id="general" v-model="value" :fields="fields" country-field="pays" @input="input"/>
        </AppTab>
        <AppTab id="gui-start-qte" icon="folder" title="Qualité">
            <AppForm id="qte" :fields="fieldsQte"/>
        </AppTab>
        <AppTab id="gui-start-comptabilite" icon="chart-line" title="Comptabilité">
            <AppForm id="comp" :fields="fieldsComp"/>
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
