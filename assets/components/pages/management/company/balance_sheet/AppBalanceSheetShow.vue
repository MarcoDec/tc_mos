<script setup>
    import {computed, onBeforeMount, ref} from 'vue'
    import {useRoute} from 'vue-router'
    import api from '../../../../../api'
    import {useBalanceSheetStore} from '../../../../../stores/management/balance-sheets/balanceSheets'
    import AppCardShow from '../../../../AppCardShow.vue'
    import AppSuspense from '../../../../AppSuspense.vue'
    import AppBalanceSheetShowTable from './AppBalanceSheetShowTable.vue'

    const route = useRoute()
    const idBalanceSheet = Number(route.params.id)

    const fetchBalanceSheet = useBalanceSheetStore()
    const company = ref({})

    const formFields = [
        {label: 'Mois', name: 'month', type: 'number', step: 1},
        {label: 'Année', name: 'year', type: 'number', step: 1},
        {label: 'Company', name: 'company', options: {base: 'companies'}, type: 'select'},
        {label: 'Devise', name: 'currency', options: {base: 'currencies'}, type: 'select'}
    ]
    const formData = ref({
        month: 0,
        year: 0,
        company: '',
        currency: ''
    })
    //region récupération de la balance sheet
    const currentBalanceSheet = computed(() => {
        if (fetchBalanceSheet.item === null) return {
            '@id': '',
            '@type': '',
            id: '',
            month: '',
            year: '',
            totalExpense: {value: 0, code: ''},
            totalIncome: {value: 0, code: ''},
            company: {name: ''}
        }
        return fetchBalanceSheet.item
    })
    const formKey = ref(0)
    onBeforeMount(async () => {
        await fetchBalanceSheet.fetchById(idBalanceSheet)
        currentBalanceSheet.value = fetchBalanceSheet.item
        formData.value = currentBalanceSheet.value
        formData.value.company = currentBalanceSheet.value.company['@id']
        formData.value.currency = currentBalanceSheet.value.currency['@id']
        company.value = await api(formData.value.company, 'GET')
        formKey.value++
    })
    //endregion
    function updateFormData(value) {
        formData.value = value
    }
    async function reloadBalanceSheet() {
        await fetchBalanceSheet.fetchById(idBalanceSheet)
        currentBalanceSheet.value = fetchBalanceSheet.item
        formData.value = currentBalanceSheet.value
        formData.value.company = currentBalanceSheet.value.company['@id']
        formData.value.currency = currentBalanceSheet.value.currency['@id']
        formKey.value++
    }
    async function updateForm() {
        await fetchBalanceSheet.update({month: formData.value.month, year: formData.value.year, company: formData.value.company, currency: formData.value.currency}, idBalanceSheet)
        await reloadBalanceSheet()
    }
    const purchaseTablesFields = [
        {
            title: 'Dépenses normales',
            fields: [
                {label: 'Date Paiement', name: 'paymentDate', trie: true, type: 'text'},
                {label: 'Date Facture', name: 'billDate', trie: true, type: 'text'},
                {label: 'N° Facture', name: 'paymentRef', trie: true, type: 'text'},
                {label: 'Fournisseur', name: 'stakeholder', trie: true, type: 'text'},
                {label: 'Libelle', name: 'label', trie: true, type: 'text'},
                {label: 'Débit / MHT', name: 'amount', trie: true, type: 'text'},
                {label: 'Méthode de paiement', name: 'paymentMethod', trie: true, type: 'text'}
            ],
            paymentCategory: 'Dépenses normales'
        },
        {
            title: 'Salaires',
            fields: [
                {label: 'Date', name: 'paymentDate', trie: true, type: 'text'},
                {label: 'N° Matricule', name: 'paymentRef', trie: true, type: 'text'},
                {label: 'Type Paies', name: 'subCategory', trie: true, type: 'text'},
                {label: 'Nom & Prénom', name: 'label', trie: true, type: 'text'},
                {label: 'Montant', name: 'amount', trie: true, type: 'text'},
                {label: 'Mode de paiement', name: 'paymentMethod', trie: true, type: 'text'}
            ],
            paymentCategory: 'Salaires'
        },
        {
            title: 'Achats Matières Premières',
            fields: [
                {label: 'Date', name: 'paymentDate', trie: true, type: 'text'},
                {label: 'Date Facture', name: 'billDate', trie: true, type: 'text'},
                {label: 'Numéro de Facture', name: 'paymentRef', trie: true, type: 'text'},
                {label: 'Fournisseur', name: 'stakeholder', trie: true, type: 'text'},
                {label: 'Libellé', name: 'label', trie: true, type: 'text'},
                {label: 'Montant', name: 'amount', trie: true, type: 'text'},
                {label: 'tva', name: 'vat', trie: true, type: 'text'},
                {label: 'Méthode paiement', name: 'paymentMethod', trie: true, type: 'text'}
            ],
            paymentCategory: 'Achats Matières Premières'
        },
        {
            title: 'Frais de transport',
            fields: [
                {label: 'Date', name: 'paymentDate', trie: true, type: 'text'},
                {label: 'Date Facture', name: 'billDate', trie: true, type: 'text'},
                {label: 'Ref Facture', name: 'paymentRef', trie: true, type: 'text'},
                {label: 'Fournisseur', name: 'stakeholder', trie: true, type: 'text'},
                {label: 'Libellé', name: 'label', trie: true, type: 'text'},
                {label: 'Montant', name: 'amount', trie: true, type: 'text'},
                {label: 'Méthode paiement', name: 'paymentMethod', trie: true, type: 'text'}
            ],
            paymentCategory: 'Frais de transport'
        }
    ]
    const sellingTablesFields = [
        {
            title: 'Ventes',
            fields: [
                {label: 'Date', name: 'paymentDate', trie: true, type: 'text'},
                {label: 'N° Facture/Avoir', name: 'paymentRef', trie: true, type: 'text'},
                {label: 'Client', name: 'stakeholder', trie: true, type: 'text'},
                {label: 'Libellé', name: 'label', trie: true, type: 'text'},
                {label: 'Montant', name: 'amount', trie: true, type: 'text'},
                {label: 'tva', name: 'vat', trie: true, type: 'text'},
                {label: 'Mode de paiement', name: 'paymentMethod', trie: true, type: 'text'}
            ],
            paymentCategory: 'Ventes'
        }
    ]
</script>

<template>
    <AppSuspense>
        <div class="title">
            Suivi des dépenses {{ currentBalanceSheet.month }} - {{ currentBalanceSheet.year }} pour {{ company.name }}
        </div>
        <div class="container-fluid">
            <div class="row">
                <div class="col-6">
                    <AppCardShow
                        id="properties"
                        :key="formKey"
                        :component-attribute="formData"
                        :fields="formFields"
                        title="Caractéristiques du suivi"
                        @update:model-value="updateFormData"
                        @update="updateForm"/>
                </div>
                <div class="col-6">
                    <div class="row">
                        <div class="col p-0">
                            <h2>Synthèse <small>(valeurs calculées)</small></h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col synth text-info">
                            <div><Fa :brands="false" icon="cart-shopping"/> Total Achats: {{ currentBalanceSheet.totalExpense.value }} {{ currentBalanceSheet.totalExpense.code }}</div>
                        </div>
                        <div class="col synth text-info">
                            <div><Fa :brands="false" icon="hand-holding-dollar"/>Total Ventes: {{ currentBalanceSheet.totalIncome.value }} {{ currentBalanceSheet.totalIncome.code }}</div>
                        </div>
                        <div class="col synth text-info">
                            <div><Fa :brands="false" icon="scale-balanced"/>Solde: {{ currentBalanceSheet.totalIncome.value - currentBalanceSheet.totalExpense.value }} {{ currentBalanceSheet.totalExpense.code }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <h2>Achats</h2>
        <AppBalanceSheetShowTable
            v-for="table in purchaseTablesFields"
            :key="table.title"
            :id-balance-sheet="idBalanceSheet"
            :payment-category="table.paymentCategory"
            :tab-fields="table.fields"
            :title="table.title"/>
        <h2>Ventes</h2>
        <AppBalanceSheetShowTable
            v-for="table in sellingTablesFields"
            :key="table.title"
            :id-balance-sheet="idBalanceSheet"
            :payment-category="table.paymentCategory"
            :tab-fields="table.fields"
            :title="table.title"/>
    </AppSuspense>
</template>

<style scoped>
    .title {
        font-size: 2em;
        color: #43abd7;
        font-weight: bolder;
    }
    h2 {
        background-color: #f8eec9;
        color: black;
        text-align: center;
        padding-left: 10px;
        margin-bottom: 0;
        border: 2px solid black;
    }
    h3 {
        background-color: #44546A;
        color: white;
        padding-left: 20px;
    }
    .synth {
        padding: 5px 5px 5px 20px;
    }
    .border {
        border: 1px solid black;
    }
</style>
