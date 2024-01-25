<script setup>
    import {computed, ref} from 'vue-demi'
    import AppFormJS from '../../../../form/AppFormJS.js'
    import AppTab from '../../../../tab/AppTab.vue'
    import AppTabs from '../../../../tab/AppTabs.vue'
    import useOptions from '../../../../../stores/option/options'
    import {useSuppliersStore} from '../../../../../stores/purchase/supplier/suppliers'
    import {onBeforeMount} from 'vue'
    import useUser from '../../../../../stores/security'

    defineProps({
        title: {required: true, type: String},
        target: {required: true, type: String},
        modalId: {required: true, type: String}
    })
    const emits = defineEmits(['created'])

    const user = useUser()
    const currentCompany = user.company
    const storeSuppliersList = useSuppliersStore()
    const violations = ref([])
    let success = []
    const optionsCountry = ref([])
    const isPopupVisible = ref(false)
    const isCreatedPopupVisible = ref(false)

    const fetchCurrencyOptions = useOptions('currencies')
    const fetchCountryOptions = useOptions('countries')
    await fetchCurrencyOptions.fetchOp()
    const optionsCurrency = fetchCurrencyOptions.options.map(op => {
        const text = op.text
        const value = op.value
        return {text, value}
    })
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

    const fields = computed(() => [
        {label: 'Nom', name: 'name', type: 'text'},
        {
            label: 'Société mère / Groupe *',
            name: 'society',
            type: 'multiselect-fetch',
            api: '/api/societies',
            filteredProperty: 'name',
            min: true,
            max: 1
        },
        {label: 'Adresse', name: 'address', type: 'text'},
        {label: 'complément d\'adresse', name: 'address2', type: 'text'},
        {label: 'ville', name: 'city', type: 'text'},
        {label: 'Code postal', name: 'zipCode', type: 'text'},
        // {label: 'Pays*', name: 'country',options: {label: value =>optionsCountry.value.find(option => option.type === value)?.text ?? null, options: optionsCountry.value}, type: 'select'},
        {label: 'Pays*', name: 'country', options: {label: value => optionsCountry.value.find(option => option.type === value)?.text ?? null, options: optionsCountry.value}, type: 'select'},
        {label: 'Téléphone', name: 'phoneNumber', type: 'text'},
        {label: 'Email', name: 'email', type: 'text'}
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
    const baseFieldsCuivre = [{
        label: 'Gest. cuivre',
        name: 'managed',
        type: 'boolean'
    }]
    const typeOptions = [
        {text: 'à la livraison', value: 'à la livraison'},
        {text: 'mensuel', value: 'mensuel'},
        {text: 'semestriel', value: 'semestriel'}
    ]
    const conditionnedFieldsCuivre = [{
        label: 'Indice du cuivre',
        name: 'copperIndex ',
        type: 'number'
    }, {
        label: 'Date de l\'indice',
        name: 'last',
        type: 'date'
    }, {
        label: 'Périodicité',
        name: 'type',
        options: {
            label: value =>
                typeOptions.find(option => option.type === value)?.text ?? null,
            options: typeOptions
        },
        type: 'select'
    }]
    const fieldsCuivre = computed(() => {
        if (cuivreData.value.managed) {
            return baseFieldsCuivre.concat(conditionnedFieldsCuivre)
        }
        return baseFieldsCuivre
    })
    const generalData = ref({})
    const qualityData = ref({})
    const comptabilityData = ref({})
    const cuivreData = ref({})

    function generalForm(value) {
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(generalData.value, key)) {
            if (typeof value[key] === 'object') {
                if (typeof value[key].value !== 'undefined') {
                    const inputValue = parseFloat(value[key].value)
                    generalData.value[key] = {...generalData.value[key], value: inputValue}
                }
                if (typeof value[key].code !== 'undefined') {
                    const inputCode = value[key].code
                    generalData.value[key] = {...generalData.value[key], code: inputCode}
                }
            } else {
                generalData.value[key] = value[key]
            }
        } else {
            generalData.value[key] = value[key]
        }
    }
    function qualityForm(value) {
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(qualityData.value, key)) {
            if (typeof value[key] === 'object') {
                if (typeof value[key].value !== 'undefined') {
                    const inputValue = parseFloat(value[key].value)
                    qualityData.value[key] = {...qualityData.value[key], value: inputValue}
                }
                if (typeof value[key].code !== 'undefined') {
                    const inputCode = value[key].code
                    qualityData.value[key] = {...qualityData.value[key], code: inputCode}
                }
            } else {
                qualityData.value[key] = value[key]
            }
        } else {
            qualityData.value[key] = value[key]
        }
    }
    function comptabilityForm(value) {
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(comptabilityData.value, key)) {
            if (typeof value[key] === 'object') {
                if (typeof value[key].value !== 'undefined') {
                    const inputValue = parseFloat(value[key].value)
                    comptabilityData.value[key] = {...comptabilityData.value[key], value: inputValue}
                }
                if (typeof value[key].code !== 'undefined') {
                    const inputCode = value[key].code
                    comptabilityData.value[key] = {...comptabilityData.value[key], code: inputCode}
                }
            } else {
                comptabilityData.value[key] = value[key]
            }
        } else {
            comptabilityData.value[key] = value[key]
        }
    }
    function cuivreForm(value) {
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(cuivreData.value, key)) {
            // if (cuivreData.value.hasOwnProperty(key)) {
            if (typeof value[key] === 'object') {
                if (typeof value[key].value !== 'undefined') {
                    const inputValue = parseFloat(value[key].value)
                    cuivreData.value[key] = {...cuivreData.value[key], value: inputValue}
                }
                if (typeof value[key].code !== 'undefined') {
                    const inputCode = value[key].code
                    cuivreData.value[key] = {...cuivreData.value[key], code: inputCode}
                }
            } else {
                cuivreData.value[key] = value[key]
            }
        } else {
            cuivreData.value[key] = value[key]
        }
    }
    const supplierData = ref({
        administeredBy: [currentCompany],
        address: null,
        copper: {
            index: {
                code: '',
                value: 0
            },
            managed: false
        },
        currency: null,
        managedProduction: false,
        managedQuality: false,
        confidenceCriteria: 0,
        name: '',
        society: null
    })
    async function supplierFormCreate(){
        if (typeof generalData.value.address !== 'undefined') {
            supplierData.value.address = {
                address: generalData.value.address,
                address2: generalData.value.address2,
                city: generalData.value.city,
                country: generalData.value.country,
                email: generalData.value.email,
                phoneNumber: generalData.value.phoneNumber,
                zipCode: generalData.value.zipCode
            }
        }
        if (typeof generalData.value.society !== 'undefined') {
            supplierData.value.society = generalData.value.society[0]
        }
        if (typeof comptabilityData.value.currency !== 'undefined') {
            supplierData.value.currency = comptabilityData.value.currency
        }
        if (typeof generalData.value.name !== 'undefined') {
            supplierData.value.name = generalData.value.name
        }
        if (typeof comptabilityData.value.openOrdersEnabled !== 'undefined') {
            supplierData.value.openOrdersEnabled = comptabilityData.value.openOrdersEnabled
        }
        if (typeof cuivreData.value !== 'undefined') {
            if (typeof cuivreData.value.managed !== 'undefined') {
                supplierData.value.copper.managed = cuivreData.value.managed
            }
            if (typeof cuivreData.value.copperIndex !== 'undefined') {
                supplierData.value.copper.index.value = cuivreData.value.copperIndex
            }
            if (typeof cuivreData.value.copperType !== 'undefined') {
                supplierData.value.copper.index.code = cuivreData.value.copperType
            }
            if (typeof cuivreData.value.next !== 'undefined') {
                supplierData.value.copper.next = cuivreData.value.next
            }
            if (typeof cuivreData.value.last !== 'undefined') {
                supplierData.value.copper.last = cuivreData.value.last
            }
        }
        if (typeof qualityData.value.managedProduction !== 'undefined') {
            supplierData.value.managedProduction = qualityData.value.managedProduction
        }
        if (typeof qualityData.value.managedQuality !== 'undefined') {
            supplierData.value.managedQuality = qualityData.value.managedQuality
        }
        if (typeof qualityData.value.confidenceCriteria !== 'undefined') {
            supplierData.value.confidenceCriteria = qualityData.value.confidenceCriteria
        }
        try {
            await storeSuppliersList.addSupplier(supplierData.value)
            isPopupVisible.value = false
            isCreatedPopupVisible.value = true
            success = 'Fournisseur crée'
            emits('created')
            // Remise à zéro des données
            /* eslint-disable require-atomic-updates */
            supplierData.value = {
                administeredBy: [currentCompany],
                address: null,
                copper: {
                    index: {
                        code: '',
                        value: 0
                    },
                    managed: false
                },
                currency: null,
                managedProduction: false,
                managedQuality: false,
                confidenceCriteria: 0,
                name: '',
                society: null
            }
            /* eslint-disable require-atomic-updates */
            generalData.value = {}
            /* eslint-disable require-atomic-updates */
            comptabilityData.value = {}
            /* eslint-disable require-atomic-updates */
            cuivreData.value = {
                managed: false
            }
            violations.value = []
        } catch (error) {
            violations.value = error
            isPopupVisible.value = true
            isCreatedPopupVisible.value = false
            console.log('violations', violations.value)
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
