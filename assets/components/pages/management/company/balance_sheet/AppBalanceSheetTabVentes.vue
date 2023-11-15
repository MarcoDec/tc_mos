<script setup>
    import AppBalanceSheetShowTable from './AppBalanceSheetShowTable.vue'
    import {defineProps} from 'vue'

    const props = defineProps({
        idBalanceSheet: {required: true, type: Number},
        isWriterOrAdmin: {required: true, type: Boolean},
        balanceSheetCurrency: {required: true, type: String}
    })
    //region Définition des champs de formulaires et tableaux
    //region      Définition des champs communs
    const priceMeasure = {
        code: {
            label: 'Devise',
            name: 'code',
            type: 'select',
            options: {base: 'currencies'}
        },
        value: {
            label: 'Montant',
            name: 'value',
            type: 'number',
            step: 0.01
        }
    }
    const formFileField = {
        label: 'Fichier',
        name: 'file',
        multiple: false,
        trie: true,
        type: 'file'
    }
    const showFileField = {
        label: 'Fichier',
        name: 'url',
        trie: true,
        type: 'link'
    }
    //endregion
    //region      Définition des champs de formulaires et tableaux pour les Ventes
    const sellingFormField = {
        title: 'Ventes',
        icon: 'hand-holding-dollar',
        id: 'ventes',
        fields: [
            {label: 'Date', name: 'paymentDate', trie: true, type: 'date', min: true},
            {label: 'N° Facture/Avoir', name: 'paymentRef', trie: true, type: 'text', min: true},
            {label: 'Client', name: 'stakeholder', trie: true, type: 'text', min: true},
            {label: 'Libellé', name: 'label', trie: true, type: 'text'},
            {label: 'Montant', name: 'amount', trie: true, type: 'measure', min: true, measure: priceMeasure},
            {label: 'tva', name: 'vat', trie: true, type: 'measure', measure: priceMeasure},
            {label: 'Mode de paiement', name: 'paymentMethod', trie: true, type: 'text'},
            formFileField
        ]
    }
    const sellingTablesField = {
        title: 'Ventes',
        icon: 'hand-holding-dollar',
        id: 'ventes',
        fields: [
            {label: 'Date', name: 'paymentDate', trie: true, type: 'date', min: true},
            {label: 'N° Facture/Avoir', name: 'paymentRef', trie: true, type: 'text', min: true},
            {label: 'Client', name: 'stakeholder', trie: true, type: 'text', min: true},
            {label: 'Libellé', name: 'label', trie: true, type: 'text'},
            {label: 'Montant', name: 'amount', trie: true, type: 'measure', min: true, measure: priceMeasure},
            {label: 'tva', name: 'vat', trie: true, type: 'measure', measure: priceMeasure},
            {label: 'Mode de paiement', name: 'paymentMethod', trie: true, type: 'text'},
            showFileField
        ],
        paymentCategory: 'Ventes'
    }
    //endregion
    //endregion
    //region Initialisation des données de formulaires
    const defaultFormValues = {
        balanceSheet: `/api/balance-sheets/${props.idBalanceSheet}`,
        paymentCategory: sellingTablesField.paymentCategory,
        paymentDate: new Date().toISOString().substr(0, 10),
        paymentRef: '',
        stakeholder: '',
        label: '',
        amount: {code: props.balanceSheetCurrency, value: 0},
        vat: {code: props.balanceSheetCurrency, value: 0},
        paymentMethod: '',
        file: null
    }
    //endregion
</script>

<template>
    <AppBalanceSheetShowTable
        :add-form="isWriterOrAdmin"
        :id-balance-sheet="idBalanceSheet"
        :balance-sheet-currency="balanceSheetCurrency"
        :default-form-values="defaultFormValues"
        :payment-category="sellingTablesField.paymentCategory"
        :form-fields="sellingFormField.fields"
        :tab-fields="sellingTablesField.fields"
        :tab-id="sellingTablesField.id"
        :title="sellingTablesField.title"/>
</template>

<style scoped>
div {
    background-color: white;
}
div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
</style>
