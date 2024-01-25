<script setup>
    import {computed, ref} from 'vue-demi'
    import AppFormJS from '../../../../form/AppFormJS.js'
    import AppTab from '../../../../tab/AppTab.vue'
    import AppTabs from '../../../../tab/AppTabs.vue'
    import useOptions from '../../../../../stores/option/options'
    import {useSuppliersStore} from '../../../../../stores/purchase/supplier/suppliers'
    import {onBeforeMount} from 'vue'

    defineProps({
        title: {required: true, type: String},
        target: {required: true, type: String},
        modalId: {required: true, type: String}
    })
    const emits = defineEmits(['created'])
    const storeSuppliersList = useSuppliersStore()
    let violations = []
    let success = []
    const optionsCountry = ref([])
    const isPopupVisible = ref(false)
    const isCreatedPopupVisible = ref(false)

    const fecthOptionsCompanies = useOptions('companies')
    await fecthOptionsCompanies.fetchOp()
    const optionsCompany = computed(() =>
        fecthOptionsCompanies.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))

    const fetchCurrencyOptions = useOptions('currencies')
    const fetchCountryOptions = useOptions('countries')
    await fetchCurrencyOptions.fetchOp()
    const optionsCurrency = fetchCurrencyOptions.options.map(op => {
        const text = op.text
        const value = op.value
        return {text, value}
    })
    const optionsCopperType = [
        {text: 'à la livraison', value: 'à la livraison'},
        {text: 'mensuel', value: 'mensuel'},
        {text: 'semestriel', value: 'semestriel'}
    ]
    onBeforeMount(async () => {
        const promises = []
        promises.push(fetchCurrencyOptions.fetchOp())
        promises.push(fetchCountryOptions.fetchOp())
        Promise.all(promises).then(() => {
            optionsCurrency.value = fetchCurrencyOptions.options.map(op => {
                const text = op.text
                const value = op.value
                return {text, value}
            })
            optionsCountry.value = fetchCountryOptions.options.map(op => {
                const text = op.text
                const value = op.text
                return {text, value}
            })
        })
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
        {
            label: 'Société mère / Groupe *',
            name: 'society',
            type: 'multiselect-fetch',
            api: '/api/societies',
            filteredProperty: 'name',
            min: true,
            max: 1
        },
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Adresse', name: 'address', type: 'text'},
        {label: 'complément d\'adresse', name: 'address2', type: 'text'},
        {label: 'ville', name: 'city', type: 'text'},
        {label: 'Code postal', name: 'zipCode', type: 'text'},
        // {label: 'Pays*', name: 'country',options: {label: value =>optionsCountry.value.find(option => option.type === value)?.text ?? null, options: optionsCountry.value}, type: 'select'},
        {label: 'Pays*', name: 'country', options: {label: value => optionsCountry.value.find(option => option.type === value)?.text ?? null, options: optionsCountry.value}, type: 'select'},
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
        {label: 'CopperType', name: 'copperType', options: {label: value => optionsCopperType.find(option => option.type === value)?.text ?? null, options: optionsCopperType}, type: 'select'},
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
                if (typeof value[key].value !== 'undefined') {
                    const inputValue = parseFloat(value[key].value)
                    generalData[key] = {...generalData[key], value: inputValue}
                }
                if (typeof value[key].code !== 'undefined') {
                    const inputCode = value[key].code
                    generalData[key] = {...generalData[key], code: inputCode}
                }
            } else {
                generalData[key] = value[key]
            }
        } else {
            generalData[key] = value[key]
        }
    }
    function qualityForm(value) {
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(qualityData, key)) {
            if (typeof value[key] === 'object') {
                if (typeof value[key].value !== 'undefined') {
                    const inputValue = parseFloat(value[key].value)
                    qualityData[key] = {...qualityData[key], value: inputValue}
                }
                if (typeof value[key].code !== 'undefined') {
                    const inputCode = value[key].code
                    qualityData[key] = {...qualityData[key], code: inputCode}
                }
            } else {
                qualityData[key] = value[key]
            }
        } else {
            qualityData[key] = value[key]
        }
    }
    function comptabilityForm(value) {
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(comptabilityData, key)) {
            if (typeof value[key] === 'object') {
                if (typeof value[key].value !== 'undefined') {
                    const inputValue = parseFloat(value[key].value)
                    comptabilityData[key] = {...comptabilityData[key], value: inputValue}
                }
                if (typeof value[key].code !== 'undefined') {
                    const inputCode = value[key].code
                    comptabilityData[key] = {...comptabilityData[key], code: inputCode}
                }
            } else {
                comptabilityData[key] = value[key]
            }
        } else {
            comptabilityData[key] = value[key]
        }
    }
    function cuivreForm(value) {
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(cuivreData, key)) {
            // if (cuivreData.hasOwnProperty(key)) {
            if (typeof value[key] === 'object') {
                if (typeof value[key].value !== 'undefined') {
                    const inputValue = parseFloat(value[key].value)
                    cuivreData[key] = {...cuivreData[key], value: inputValue}
                }
                if (typeof value[key].code !== 'undefined') {
                    const inputCode = value[key].code
                    cuivreData[key] = {...cuivreData[key], code: inputCode}
                }
            } else {
                cuivreData[key] = value[key]
            }
        } else {
            cuivreData[key] = value[key]
        }
    }
    async function supplierFormCreate(){
        try {
            const supplier = {
                address: {
                    address: generalData?.address || '',
                    address2: generalData?.address2 || '',
                    city: generalData?.city || '',
                    country: 'FR',
                    // "country":  generalForm?.value?.value?.country || '',
                    email: generalData?.email || '',
                    phoneNumber: generalData?.phoneNumber || '',
                    zipCode: generalData?.zipCode || ''
                },
                administeredBy: generalData?.administeredBy || [],
                confidenceCriteria: qualityData?.confidenceCriteria || 0,
                copper: {
                    index: {
                        code: cuivreData?.copperType || '',
                        value: cuivreData?.copperIndex || 0
                    },
                    last: cuivreData?.last || null,
                    managed: cuivreData?.managed || false,
                    next: cuivreData?.next || null,
                    type: cuivreData?.copperType || ''
                },
                currency: comptabilityData?.currency || '#',
                managedProduction: qualityData?.managedProduction || false,
                managedQuality: qualityData?.managedQuality || false,
                name: generalData?.name || '#',
                openOrdersEnabled: comptabilityData?.openOrdersEnabled || false,
                society: generalData?.society[0] || '#'
            }
            // console.log('supplier', supplier)
            await storeSuppliersList.addSupplier(supplier).then(() => {
                emits('created')
            })
            isPopupVisible.value = false
            isCreatedPopupVisible.value = true
            success = 'Fournisseur crée'
        } catch (error) {
            violations = error
            isPopupVisible.value = true
            isCreatedPopupVisible.value = false
            console.log('violations', violations)
        }
    }
</script>

<template>
    <AppModal :id="modalId" class="four" :title="title">
        <AppTabs id="gui-start" class="gui-start-content">
            <AppTab id="gui-start-general" active icon="sitemap" tabs="gui-start" title="Général">
                <AppFormJS
                    id="supplier"
                    :fields="fields"
                    @update:model-value="generalForm"/>
            </AppTab>
            <AppTab id="gui-start-qte" icon="folder" tabs="gui-start" title="Qualité">
                <AppFormJS id="qte" :fields="fieldsQuality" @update:model-value="qualityForm"/>
            </AppTab>
            <AppTab id="gui-start-comptabilite" icon="chart-line" tabs="gui-start" title="Comptabilité">
                <AppFormJS id="comptabilite" :fields="fieldsComp" @update:model-value="comptabilityForm"/>
            </AppTab>
            <AppTab id="gui-start-cuivre" icon="clipboard-list" tabs="gui-start" title="Cuivre">
                <AppFormJS id="cuivre" :fields="fieldsCuivre" @update:model-value="cuivreForm"/>
            </AppTab>
        </AppTabs>
        <ul v-if="isPopupVisible" class="alert alert-danger" role="list">
            <li>{{ violations }}</li>
        </ul>
        <ul v-if="isCreatedPopupVisible" class="alert alert-success" role="list">
            <li>{{ success }}</li>
        </ul>
        <template #buttons>
            <AppBtn
                variant="success"
                label="Créer"
                data-bs-toggle="modal"
                :data-bs-target="target"
                @click="supplierFormCreate">
                Créer
            </AppBtn>
        </template>
    </AppModal>
</template>

<style>
.cardOrderSupplier {
  border: 6px solid #1d583d;
}
</style>
