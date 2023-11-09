<script setup>
    import AppTab from '../../../../tab/AppTab.vue'
    import AppTabs from '../../../../tab/AppTabs.vue'
    import AppBalanceSheetShowTable from './AppBalanceSheetShowTable.vue'
    import {defineProps} from 'vue'

    const props = defineProps({
        idBalanceSheet: {required: true, type: Number},
        isWriterOrAdmin: {required: true, type: Boolean}
    })
    const fileField = {
        label: 'Fichier',
        name: 'file',
        multiple: false,
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
    const purchaseTablesFields = [
        {
            title: 'Dépenses normales',
            icon: 'money-bill-1',
            id: 'depenses_normales',
            fields: [
                {label: 'Date Paiement', name: 'paymentDate', trie: true, type: 'date', min: true},
                {label: 'Date Facture', name: 'billDate', trie: true, type: 'date', min: false},
                {label: 'N° Facture', name: 'paymentRef', trie: true, type: 'text', min: true},
                {label: 'Fournisseur', name: 'stakeholder', trie: true, type: 'text', min: true},
                {label: 'Libelle', name: 'label', trie: true, type: 'text', min: true},
                {
                    label: 'Débit / MHT',
                    name: 'amount',
                    trie: true,
                    type: 'measure',
                    min: true,
                    measure: priceMeasure
                },
                {label: 'Méthode de paiement', name: 'paymentMethod', trie: true, type: 'text', min: false},
                fileField
            ],
            paymentCategory: 'Dépenses normales'
        },
        {
            title: 'Salaires',
            icon: 'business-time',
            id: 'salaires',
            fields: [
                {label: 'Date', name: 'paymentDate', trie: true, type: 'date', min: true},
                {label: 'N° Matricule', name: 'paymentRef', trie: true, type: 'text'},
                {label: 'Type Paies', name: 'subCategory', trie: true, type: 'text', min: true},
                {label: 'Nom & Prénom', name: 'label', trie: true, type: 'text', min: true},
                {label: 'Montant', name: 'amount', trie: true, type: 'measure', min: true, measure: priceMeasure},
                {label: 'Mode de paiement', name: 'paymentMethod', trie: true, type: 'text'},
                fileField
            ],
            paymentCategory: 'Salaires'
        },
        {
            title: 'Achats Matières Premières',
            icon: 'right-to-bracket',
            id: 'achats_matières_premières',
            fields: [
                {label: 'Date', name: 'paymentDate', trie: true, type: 'date', min: true},
                {label: 'Date Facture', name: 'billDate', trie: true, type: 'date'},
                {label: 'Numéro de Facture', name: 'paymentRef', trie: true, type: 'text', min: true},
                {label: 'Fournisseur', name: 'stakeholder', trie: true, type: 'text', min: true},
                {label: 'Libellé', name: 'label', trie: true, type: 'text', min: false},
                {label: 'Montant', name: 'amount', trie: true, type: 'measure', min: true, measure: priceMeasure},
                {label: 'tva', name: 'vat', trie: true, type: 'measure', min: true, measure: priceMeasure},
                {label: 'Méthode paiement', name: 'paymentMethod', trie: true, type: 'text'},
                fileField
            ],
            paymentCategory: 'Achats Matières Premières'
        },
        {
            title: 'Frais de transport',
            icon: 'truck',
            id: 'frais_de_transport',
            fields: [
                {label: 'Date', name: 'paymentDate', trie: true, type: 'date', min: true},
                {label: 'Date Facture', name: 'billDate', trie: true, type: 'date'},
                {label: 'Ref Facture', name: 'paymentRef', trie: true, type: 'text', min: true},
                {label: 'Fournisseur', name: 'stakeholder', trie: true, type: 'text', min: true},
                {label: 'Libellé', name: 'label', trie: true, type: 'text'},
                {label: 'Montant', name: 'amount', trie: true, type: 'measure', min: true, measure: priceMeasure},
                {label: 'Méthode paiement', name: 'paymentMethod', trie: true, type: 'text'},
                fileField
            ],
            paymentCategory: 'Frais de transport'
        }
    ]
    const defaultFormValue = {
        balanceSheet: `/api/balance-sheets/${props.idBalanceSheet}`,
        paymentCategory: '',
        paymentDate: new Date().toISOString().substr(0, 10),
        billDate: new Date().toISOString().substr(0, 10),
        paymentRef: '',
        stakeholder: '',
        subCategory: '',
        label: '',
        amount: {code: '/api/currencies/1', value: 0},
        vat: {code: '/api/currencies/1', value: 0},
        paymentMethod: '',
        file: null
    }
    const defaultFormValues = [
        {...defaultFormValue, subCategory: null, vat: null, paymentCategory: 'Dépenses normales'},
        {...defaultFormValue, billDate: null, stakeholder: null, vat: null, paymentCategory: 'Salaires'},
        {...defaultFormValue, paymentCategory: 'Achats Matières Premières', subCategory: null},
        {...defaultFormValue, paymentCategory: 'Frais de transport', subCategory: null, vat: null}
    ]
</script>

<template>
    <AppTabs id="achats_tabs" format-nav="block">
        <AppTab
            v-for="(table, index) in purchaseTablesFields"
            :id="table.id"
            :key="table.id"
            :active="index === 0"
            :icon="table.icon"
            tabs="achats_tabs"
            :title="table.title">
            <AppBalanceSheetShowTable
                :add-form="isWriterOrAdmin"
                :id-balance-sheet="idBalanceSheet"
                :default-form-values="defaultFormValues[purchaseTablesFields.indexOf(table)]"
                :payment-category="table.paymentCategory"
                :tab-fields="table.fields"
                :tab-id="table.id"
                :title="table.title"/>
        </AppTab>
    </AppTabs>
</template>

<style scoped>
    div {
        background-color: #c8c8c8;
    }
    div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
</style>
