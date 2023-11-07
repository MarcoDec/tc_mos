<script setup>
    import {computed, onBeforeMount, ref} from 'vue'
    import {useRoute} from 'vue-router'
    import api from '../../../../../api'
    import {useBalanceSheetStore} from '../../../../../stores/management/balance-sheets/balanceSheets'
    import useUser from '../../../../../stores/security'
    import AppCardShow from '../../../../AppCardShow.vue'
    import AppSuspense from '../../../../AppSuspense.vue'
    import AppTab from '../../../../tab/AppTab.vue'
    import AppTabs from '../../../../tab/AppTabs.vue'
    import AppBalanceSheetShowTable from './AppBalanceSheetShowTable.vue'

    const route = useRoute()
    const idBalanceSheet = Number(route.params.id)

    const fetchBalanceSheet = useBalanceSheetStore()
    const company = ref({})

    const fetchUser = useUser()
    const isWriterOrAdmin = fetchUser.isManagementWriter || fetchUser.isManagementAdmin
    //const roleuser = ref(isWriterOrAdmin ? 'writer' : 'reader')

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
    const sellingTablesFields = [
        {
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
        <AppTabs id="achats_tabs" format-nav="block">
            <AppTab
                v-for="table in purchaseTablesFields"
                :id="table.id"
                :key="table.id"
                :icon="table.icon"
                tabs="achats_tabs"
                :title="table.title">
                <AppBalanceSheetShowTable
                    :add-form="isWriterOrAdmin"
                    :id-balance-sheet="idBalanceSheet"
                    :payment-category="table.paymentCategory"
                    :tab-fields="table.fields"
                    :tab-id="table.id"
                    :title="table.title"/>
            </AppTab>
        </AppTabs>
        <h2>Ventes</h2>
        <AppTabs id="ventes_tabs">
            <AppTab
                v-for="table in sellingTablesFields"
                :id="table.id"
                :key="table.id"
                :icon="table.icon"
                tabs="ventes_tabs"
                :title="table.title">
                <AppBalanceSheetShowTable
                    :add-form="isWriterOrAdmin"
                    :id-balance-sheet="idBalanceSheet"
                    :payment-category="table.paymentCategory"
                    :tab-fields="table.fields"
                    :tab-id="table.id"
                    :title="table.title"/>
            </AppTab>
        </AppTabs>
    </AppSuspense>
</template>

<style scoped>
    .title {
        font-size: 2em;
        color: #43abd7;
        font-weight: bolder;
    }
    h2 {
        background-color: #43abd7;
        color: white;
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
    div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
</style>
