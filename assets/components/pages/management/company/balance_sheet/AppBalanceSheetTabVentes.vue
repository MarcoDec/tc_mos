<script setup>
    import AppBalanceSheetShowTable from './AppBalanceSheetShowTable.vue'
    import {defineProps} from 'vue'

    const props = defineProps({
        idBalanceSheet: {required: true, type: Number},
        isWriterOrAdmin: {required: true, type: Boolean}
    })
    const fileField = {
        label: 'Fichier',
        name: 'file',
        trie: true,
        type: 'file'
    }
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
            fileField
        ],
        paymentCategory: 'Ventes'
    }
    const defaultFormValues = {
        balanceSheet: `/api/balance-sheets/${props.idBalanceSheet}`,
        paymentCategory: sellingTablesField.paymentCategory,
        paymentDate: new Date().toISOString().substr(0, 10),
        paymentRef: '',
        stakeholder: '',
        label: '',
        amount: {code: '/api/currencies/1', value: 0},
        vat: {code: '/api/currencies/1', value: 0},
        paymentMethod: '',
        file: null
    }
</script>

<template>
    <AppBalanceSheetShowTable
        :add-form="isWriterOrAdmin"
        :id-balance-sheet="idBalanceSheet"
        :default-form-values="defaultFormValues"
        :payment-category="sellingTablesField.paymentCategory"
        :tab-fields="sellingTablesField.fields"
        :tab-id="sellingTablesField.id"
        :title="sellingTablesField.title"/>
</template>

<style scoped>
div {
    background-color: #c8c8c8;
}
div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
</style>
