<script setup>
    import generateSupplier from '../../../../../stores/supplier/supplier'
    import {ref} from 'vue'
    import useOptions from '../../../../../stores/option/options'
    import {useSocietyStore} from '../../../../../stores/societies/societies'
    import {useSuppliersStore} from '../../../../../stores/supplier/suppliers'

    //Définition des propriétés
    //Définition des évènements
    const emit = defineEmits([
        'update',
        'update:modelValue'
    ])
    const fetchSocietyStore = useSocietyStore()
    const fetchSuppliersStore = useSuppliersStore()
    const fetchCurrencyOptions = useOptions('currencies')
    const optionsVatMessageForce = [
        {
            text: 'TVA par défaut selon le pays du client',
            value: 'TVA par défaut selon le pays du client'
        },
        {text: 'Force AVEC TVA', value: 'Force AVEC TVA'},
        {text: 'Force SANS TVA', value: 'Force SANS TVA'}
    ]
    const optionsCurrency = fetchCurrencyOptions.options.map(op => {
        const text = op.text
        const value = op.value
        return {text, value}
    })
    const optionsVat = fetchSuppliersStore.vatMessage.map(op => {
        const text = op.name
        const value = op['@id']
        return {text, value}
    })
    const Comptabilitéfields = [
        {
            label: 'Montant minimum de facture',
            measure: {code: {label: 'Unité', name: 'unit', options: {options: [{text: 'EUR', value: 'EUR'}]}, sortName: 'unit.code', type: 'select'}, value: 'valeur'},
            name: 'invoiceMin',
            type: 'measure'
        },
        {
            label: 'Devise',
            name: 'currency',
            options: {
                label: value =>
                    optionsCurrency.find(option => option.type === value)?.text
                    ?? null,
                options: optionsCurrency
            },
            type: 'select'
        },
        {label: 'Compte de comptabilité', name: 'accountingAccount', type: 'text'},
        {label: 'TVA intracommunautaire', name: 'vat', type: 'text'},
        {
            label: 'Forcer la TVA',
            name: 'forceVat',
            options: {
                label: value =>
                    optionsVatMessageForce.find(option => option.type === value)?.text
                    ?? null,
                options: optionsVatMessageForce
            },
            type: 'select'
        },
        {
            label: 'Message TVA',
            name: 'vatMessage',
            options: {
                label: value =>
                    optionsVat.find(option => option.type === value)?.text ?? null,
                options: optionsVat
            },
            type: 'select'
        }
    ]
    const localData = ref({})
    function initLocalData(){
        // Récupération des données supplier
        localData.value = fetchSuppliersStore.supplier
        // Récupération des données de la societé associée
        localData.value.accountingAccount = fetchSocietyStore.society.accountingAccount
        localData.value.forceVat = fetchSocietyStore.society.forceVat
        localData.value.invoiceMin = fetchSocietyStore.society.invoiceMin
        localData.value.invoiceMinValue = fetchSocietyStore.society.invoiceMin ? fetchSocietyStore.society.invoiceMin.value : 0
        localData.value.vat = fetchSocietyStore.society.vat
        localData.value.vatMessage = fetchSocietyStore.society.vatMessage
    }
    initLocalData()
    async function updateModelValue(value) {
        localData.value = value
        emit('update:modelValue', value)
        console.log(localData.value)
    }
    async function updateComptabilite() {
        const societyId = fetchSocietyStore.society.id
        const dataSociety = {
            accountingAccount: localData.value.accountingAccount,
            forceVat: localData.value.forceVat,
            invoiceMin: {
                code: 'EUR',
                value: Number(localData.value.invoiceMin.value)
            },
            vat: localData.value.vat,
            vatMessage: localData.value.vatMessage
        }
        const data = {
            currency: localData.value.currency
        }
        const item = generateSupplier(fetchSuppliersStore.supplier)
        await item.updateAccounting(data)
        await fetchSocietyStore.update(dataSociety, societyId)
        await fetchSocietyStore.fetchById(societyId)
    }
</script>

<template>
    <AppCardShow
        id="addComptabilite"
        :fields="Comptabilitéfields"
        :component-attribute="localData"
        @update="updateComptabilite"
        @update:model-value="updateModelValue"/>
</template>
