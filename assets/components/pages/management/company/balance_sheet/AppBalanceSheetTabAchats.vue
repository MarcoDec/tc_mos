<script setup>
    import AppTab from '../../../../tab/AppTab.vue'
    import AppTabs from '../../../../tab/AppTabs.vue'
    import AppBalanceSheetShowTable from './AppBalanceSheetShowTable.vue'
    import {defineProps} from 'vue'

    const props = defineProps({
        idBalanceSheet: {required: true, type: Number},
        isWriterOrAdmin: {required: true, type: Boolean},
        balanceSheetCurrency: {required: true, type: String}
    })
    //region Définition des champs de formulaires et tableaux
    //region    Définition des champs partagés
    const formFileField = {
        label: 'Fichier',
        name: 'file',
        multiple: false,
        type: 'file'
    }
    const showFileField = {
        label: 'Fichier',
        name: 'url',
        trie: false,
        type: 'link',
        filter: false
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
    //endregion
    //region    Définition des champs des formulaires d'ajout
    const purchaseFormFields = [
        {
            title: 'Dépenses normales',
            icon: 'money-bill-1',
            id: 'depenses_normales',
            fields: [
                {label: 'Date Paiement', name: 'paymentDate', type: 'date'},
                {label: 'Date Facture', name: 'billDate', type: 'date'},
                {label: 'N° Facture', name: 'paymentRef', type: 'text'},
                {label: 'Fournisseur', name: 'stakeholder', type: 'text'},
                {label: 'Libelle', name: 'label', type: 'text'},
                {
                    label: 'Débit / MHT',
                    name: 'amount',
                    type: 'measure',
                    measure: priceMeasure
                },
                {label: 'Méthode de paiement', name: 'paymentMethod', type: 'text'},
                formFileField
            ],
            paymentCategory: 'Dépenses normales'
        },
        {
            title: 'Salaires',
            icon: 'business-time',
            id: 'salaires',
            fields: [
                {label: 'Date', name: 'paymentDate', type: 'date'},
                {label: 'N° Matricule', name: 'paymentRef', type: 'text'},
                {label: 'Type Paies', name: 'subCategory', type: 'text'},
                {label: 'Nom & Prénom', name: 'label', type: 'text'},
                {label: 'Montant', name: 'amount', type: 'measure', measure: priceMeasure},
                {label: 'Mode de paiement', name: 'paymentMethod', type: 'text'},
                formFileField
            ],
            paymentCategory: 'Salaires'
        },
        {
            title: 'Achats Matières Premières',
            icon: 'right-to-bracket',
            id: 'achats_matières_premières',
            fields: [
                {label: 'Date', name: 'paymentDate', type: 'date'},
                {label: 'Date Facture', name: 'billDate', type: 'date'},
                {label: 'Numéro de Facture', name: 'paymentRef', ttype: 'text'},
                {label: 'Fournisseur', name: 'stakeholder', type: 'text'},
                {label: 'Libellé', name: 'label', type: 'text'},
                {label: 'Montant', name: 'amount', type: 'measure', measure: priceMeasure},
                {label: 'tva', name: 'vat', ttype: 'measure', measure: priceMeasure},
                {label: 'Méthode paiement', name: 'paymentMethod', type: 'text'},
                formFileField
            ],
            paymentCategory: 'Achats Matières Premières'
        },
        {
            title: 'Frais de transport',
            icon: 'truck',
            id: 'frais_de_transport',
            fields: [
                {label: 'Date', name: 'paymentDate', type: 'date'},
                {label: 'Date Facture', name: 'billDate', type: 'date'},
                {label: 'Ref Facture', name: 'paymentRef', type: 'text'},
                {label: 'Fournisseur', name: 'stakeholder', type: 'text'},
                {label: 'Libellé', name: 'label', type: 'text'},
                {label: 'Montant', name: 'amount', type: 'measure', measure: priceMeasure},
                {label: 'Méthode paiement', name: 'paymentMethod', type: 'text'},
                formFileField
            ],
            paymentCategory: 'Frais de transport'
        }
    ]
    //endregion
    //region    Définition des champs des tableaux
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
                    trie: false,
                    type: 'measure',
                    min: true,
                    measure: priceMeasure,
                    filter: false
                },
                {label: 'Méthode de paiement', name: 'paymentMethod', trie: true, type: 'text', min: false},
                showFileField
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
                {label: 'Montant', name: 'amount', trie: false, type: 'measure', min: true, measure: priceMeasure, filter: false},
                {label: 'Mode de paiement', name: 'paymentMethod', trie: true, type: 'text'},
                showFileField
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
                {label: 'Montant', name: 'amount', trie: false, type: 'measure', min: true, measure: priceMeasure, filter: false},
                {label: 'tva', name: 'vat', trie: false, type: 'measure', min: true, measure: priceMeasure, filter: false},
                {label: 'Méthode paiement', name: 'paymentMethod', trie: true, type: 'text'},
                showFileField
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
                {label: 'Montant', name: 'amount', trie: false, type: 'measure', min: true, measure: priceMeasure, filter: false},
                {label: 'Méthode paiement', name: 'paymentMethod', trie: true, type: 'text'},
                showFileField
            ],
            paymentCategory: 'Frais de transport'
        }
    ]
    //endregion
    //endregion
    //region      Initialisation des valeurs par défault des formulaires
    const defaultFormValue = {
        balanceSheet: `/api/balance-sheets/${props.idBalanceSheet}`,
        paymentCategory: '',
        paymentDate: new Date().toISOString().substr(0, 10),
        billDate: new Date().toISOString().substr(0, 10),
        paymentRef: '',
        stakeholder: '',
        subCategory: '',
        label: '',
        amount: {code: props.balanceSheetCurrency, value: 0},
        vat: {code: props.balanceSheetCurrency, value: 0},
        paymentMethod: '',
        file: null
    }
    const defaultFormValues = [
        // Champs subCategory et vat inutiles pour les dépenses normales
        {...defaultFormValue, subCategory: null, vat: null, paymentCategory: 'Dépenses normales'},
        // Champ billDate, stakeholder et vat inutiles pour les salaires
        {...defaultFormValue, billDate: null, stakeholder: null, vat: null, paymentCategory: 'Salaires'},
        // Champ subCategory inutile pour les achats matières premières
        {...defaultFormValue, paymentCategory: 'Achats Matières Premières', subCategory: null},
        // Champs subCategory et vat inutiles pour les frais de transport
        {...defaultFormValue, paymentCategory: 'Frais de transport', subCategory: null, vat: null}
    ]
    //endregion
</script>

<template>
    <AppTabs id="achats_tabs" format-nav="block" :icon-mode="true">
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
                :balance-sheet-currency="balanceSheetCurrency"
                :default-form-values="defaultFormValues[purchaseTablesFields.indexOf(table)]"
                :payment-category="table.paymentCategory"
                :tab-fields="table.fields"
                :form-fields="purchaseFormFields[purchaseTablesFields.indexOf(table)].fields"
                :tab-id="table.id"
                :title="table.title"/>
        </AppTab>
    </AppTabs>
</template>

<style scoped>
    div {
        background-color: white;
    }
    div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
</style>
