<script setup>
    import {computed, onBeforeMount, ref} from 'vue'
    import {useRoute, useRouter} from 'vue-router'
    import api from '../../../../../api'
    import {useBalanceSheetStore} from '../../../../../stores/management/balance-sheets/balanceSheets'
    import useUser from '../../../../../stores/security'
    import AppCardShow from '../../../../AppCardShow.vue'
    import AppSuspense from '../../../../AppSuspense.vue'
    import AppTab from '../../../../tab/AppTab.vue'
    import AppTabs from '../../../../tab/AppTabs.vue'
    import AppBalanceSheetTabAchats from './AppBalanceSheetTabAchats.vue'
    import AppBalanceSheetTabVentes from './AppBalanceSheetTabVentes.vue'
    //region Récupération des données de route
    const route = useRoute()
    const idBalanceSheet = Number(route.params.id)
    //endregion

    //region Récupération des données utilisateur
    const fetchUser = useUser()
    const isWriterOrAdmin = fetchUser.isManagementWriter || fetchUser.isManagementAdmin
    //endregion

    //region Définition des données à charger
    const company = ref({})
    const fetchBalanceSheet = useBalanceSheetStore()
    const currentBalanceSheet = computed(() => {
        if (fetchBalanceSheet.item === null) return {
            '@id': '',
            '@type': '',
            id: '',
            month: '',
            year: '',
            totalExpense: {value: 0, code: ''},
            totalIncome: {value: 0, code: ''},
            company: {name: ''},
            currency: null
        }
        return fetchBalanceSheet.item
    })
    const balanceSheetCurrency = computed(() => {
        if (typeof currentBalanceSheet.value.currency === 'undefined') return ''
        if (currentBalanceSheet.value.currency === null) return ''
        if (typeof currentBalanceSheet.value.currency === 'string') return currentBalanceSheet.value.currency
        if (typeof currentBalanceSheet.value.currency === 'object') return currentBalanceSheet.value.currency['@id']
        return ''
    })
    //endregion
    //region Définition des champs de formulaires et tableaux
    const router = useRouter()
    const formFields = [
        {label: 'Mois', name: 'month', type: 'number', step: 1},
        {label: 'Année', name: 'year', type: 'number', step: 1},
        {label: 'Company', name: 'company', options: {base: 'companies'}, type: 'select'},
        {label: 'Devise', name: 'currency', options: {base: 'currencies'}, type: 'select'}
    ]
    //endregion
    //region Définition des variables et fonctions de formulaire
    const formKey = ref(0)
    const formData = ref({
        month: 0,
        year: 0,
        company: '',
        currency: ''
    })
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
        keyAchatsTab.value++
        keyVentesTab.value++
    }
    //endregion
    //region Chargement des données onBeforeMount
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
    function goToList() {
        router.push({name: 'suivi_depenses_ventes'})
    }
    const keyAchatsTab = ref(0)
    const keyVentesTab = ref(0)
</script>

<template>
    <AppSuspense>
        <div class="title">
            <Fa class="clickable" :brand="false" icon="gauge-high" @click="goToList"/>
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
                            <div><Fa :brands="false" icon="cart-shopping"/> Total Achats: {{ new Intl.NumberFormat().format(currentBalanceSheet.totalExpense.value) }} {{ currentBalanceSheet.totalExpense.code }}</div>
                        </div>
                        <div class="col synth text-info">
                            <div><Fa :brands="false" icon="hand-holding-dollar"/>Total Ventes: {{ new Intl.NumberFormat().format(currentBalanceSheet.totalIncome.value) }} {{ currentBalanceSheet.totalIncome.code }}</div>
                        </div>
                        <div class="col synth text-info">
                            <div><Fa :brands="false" icon="scale-balanced"/>Solde: {{ new Intl.NumberFormat().format(currentBalanceSheet.totalIncome.value - currentBalanceSheet.totalExpense.value) }} {{ currentBalanceSheet.totalExpense.code }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <AppTabs id="main_tabs" :icon-mode="true">
            <AppTab
                id="achats_tab"
                active
                icon="cart-shopping"
                tabs="main_tabs"
                title="Achats">
                <AppBalanceSheetTabAchats :key="keyAchatsTab" :balance-sheet-currency="balanceSheetCurrency" :is-writer-or-admin="isWriterOrAdmin" :id-balance-sheet="idBalanceSheet"/>
            </AppTab>
            <AppTab
                id="ventes_tab"
                icon="hand-holding-dollar"
                tabs="main_tabs"
                title="Ventes">
                <AppBalanceSheetTabVentes :key="keyVentesTab" :balance-sheet-currency="balanceSheetCurrency" :is-writer-or-admin="isWriterOrAdmin" :id-balance-sheet="idBalanceSheet"/>
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
    .clickable {
        cursor: pointer;
        color: black;
    }
    .clickable:hover {
        box-shadow: inset darkmagenta 0 0 10px;
    }
</style>
