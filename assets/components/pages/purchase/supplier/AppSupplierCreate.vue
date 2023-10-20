<script setup>
    import {computed} from 'vue-demi'
    import AppFormJS from '../../../../components/form/AppFormJS.js'
    import AppTab from '../../../../components/tabs/AppTab.vue'
    import AppTabs from '../../../../components/tabs/AppTabs.vue'
    import useOptions from '../../../../stores/option/options'

    defineProps({
        success: {required: true, type: Array},
        isCreatedPopupVisible: {required: true, type: Boolean},
        isPopupVisible: {required: true, type: Boolean},
        violations: {required: true, type: Array}
    })

    const emit = defineEmits(['update:modelValue', 'generalData', 'qualityData', 'comptabilityData', 'cuivreData'])

    const fecthOptionsCompanies = useOptions('companies')
    await fecthOptionsCompanies.fetchOp()
    const optionsCompany = computed(() =>
        fecthOptionsCompanies.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))

    const fecthOptionsSociety = useOptions('societies')
    await fecthOptionsSociety.fetchOp()
    const optionsSociety = computed(() =>
        fecthOptionsSociety.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))

    const fetchCurrencyOptions = useOptions('currencies')
    await fetchCurrencyOptions.fetchOp()
    const optionsCurrency = fetchCurrencyOptions.options.map(op => {
        const text = op.text
        const value = op.value
        return {text, value}
    })

    // const fetchCountryOptions = useOptions('countries')
    // await fetchCountryOptions.fetchOp()
    // const optionsCountry = fetchCountryOptions.options.map(op => {
    //     const text = op.text
    //     const value = op.value
    //     return {text, value}
    // })
    // console.log('optionsCountry', optionsCountry);

    const fields = computed(() => [
        {label: 'Société', name: 'society', options: {label: value => optionsSociety.value.find(option => option.type === value)?.text ?? null, options: optionsSociety.value}, type: 'select'},
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Adresse', name: 'address', type: 'text'},
        {label: 'complément d\'adresse', name: 'address2', type: 'text'},
        {label: 'ville', name: 'city', type: 'text'},
        {label: 'Code postal', name: 'zipCode', type: 'text'},
        // {label: 'Pays*', name: 'country',options: {label: value =>optionsCountry.value.find(option => option.type === value)?.text ?? null, options: optionsCountry.value}, type: 'select'},
        {label: 'Pays*', name: 'country', type: 'text'},
        {label: 'Téléphone', name: 'phoneNumber', type: 'text'},
        {label: 'Email', name: 'email', type: 'text'},
        {label: 'sociétés gérant', name: 'administeredBy', options: {label: value => optionsCompany.value.find(option => option.type === value)?.text ?? null, options: optionsCompany.value}, type: 'multiselect'}
    ])

    const fieldsQuality = computed(() => [
        {label: 'Gestion en production', name: 'managedProduction', type: 'boolean'},
        {label: 'Gest. qualité', name: 'managedQuality', type: 'boolean'},
        {label: 'confidence Criteria ', name: 'confidenceCriteria', type: 'number'}
    ])
    const fieldsComp = computed(() => [
        {label: 'Devise', name: 'currency', options: {label: value => optionsCurrency.find(option => option.type === value)?.text ?? null, options: optionsCurrency}, type: 'select'},
        {label: 'Open orders enabled*', name: 'openOrdersEnabled', type: 'boolean'}
    ])
    const fieldsCuivre = computed(() => [
        {label: 'CopperIndex', name: 'copperIndex ', type: 'number'},
        {label: 'CopperType', name: 'copperType', type: 'text'},
        {label: 'Gest. cuivre', name: 'managed', type: 'boolean'},
        {label: 'Date du prochain indice', name: 'next', type: 'date'},
        {label: 'Date du dernier indice', name: 'last', type: 'date'},
        {label: 'type', name: 'type', type: 'text'}
    ])
    const generalData = {}
    const qualityData = {}
    const comptabilityData = {}
    const cuivreData = {}

    function generalForm(value) {
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(generalData, key)) {
            if (typeof value[key] === 'object') {
                if (value[key].value !== undefined) {
                    const inputValue = parseFloat(value[key].value)
                    generalData[key] = {...generalData[key], value: inputValue}
                }
                if (value[key].code !== undefined) {
                    const inputCode = value[key].code
                    generalData[key] = {...generalData[key], code: inputCode}
                }
            } else {
                generalData[key] = value[key]
            }
        } else {
            generalData[key] = value[key]
        }
        emit('generalData', generalData)
    }
    function qualityForm(value) {
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(qualityData, key)) {
            if (typeof value[key] === 'object') {
                if (value[key].value !== undefined) {
                    const inputValue = parseFloat(value[key].value)
                    qualityData[key] = {...qualityData[key], value: inputValue}
                }
                if (value[key].code !== undefined) {
                    const inputCode = value[key].code
                    qualityData[key] = {...qualityData[key], code: inputCode}
                }
            } else {
                qualityData[key] = value[key]
            }
        } else {
            qualityData[key] = value[key]
        }
        emit('qualityData', qualityData)
    }
    function comptabilityForm(value) {
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(comptabilityData, key)) {
            if (typeof value[key] === 'object') {
                if (value[key].value !== undefined) {
                    const inputValue = parseFloat(value[key].value)
                    comptabilityData[key] = {...comptabilityData[key], value: inputValue}
                }
                if (value[key].code !== undefined) {
                    const inputCode = value[key].code
                    comptabilityData[key] = {...comptabilityData[key], code: inputCode}
                }
            } else {
                comptabilityData[key] = value[key]
            }
        } else {
            comptabilityData[key] = value[key]
        }
        emit('comptabilityData', comptabilityData)
    }
    function cuivreForm(value) {
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(cuivreData, key)) {
            // if (cuivreData.hasOwnProperty(key)) {
            if (typeof value[key] === 'object') {
                if (value[key].value !== undefined) {
                    const inputValue = parseFloat(value[key].value)
                    cuivreData[key] = {...cuivreData[key], value: inputValue}
                }
                if (value[key].code !== undefined) {
                    const inputCode = value[key].code
                    cuivreData[key] = {...cuivreData[key], code: inputCode}
                }
            } else {
                cuivreData[key] = value[key]
            }
        } else {
            cuivreData[key] = value[key]
        }
        emit('cuivreData', cuivreData)
    }
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab id="gui-start-general" active icon="sitemap" title="Général">
            <AppFormJS
                id="supplier"
                :fields="fields"
                @update:model-value="generalForm"/>
        </AppTab>
        <AppTab id="gui-start-qte" icon="folder" title="Qualité">
            <AppFormJS id="qte" :fields="fieldsQuality" @update:model-value="qualityForm"/>
        </AppTab>
        <AppTab id="gui-start-comptabilite" icon="chart-line" title="Comptabilité">
            <AppFormJS id="comptabilite" :fields="fieldsComp" @update:model-value="comptabilityForm"/>
        </AppTab>
        <AppTab id="gui-start-cuivre" icon="clipboard-list" title="Cuivre">
            <AppFormJS id="cuivre" :fields="fieldsCuivre" @update:model-value="cuivreForm"/>
        </AppTab>
    </AppTabs>
    <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
        <li>{{ violations }}</li>
    </div>
    <div v-if="isCreatedPopupVisible" class="alert alert-success" role="alert">
        <li>{{ success }}</li>
    </div>
</template>

<style>
.cardOrderSupplier {
  border: 6px solid #1d583d;
}
</style>
