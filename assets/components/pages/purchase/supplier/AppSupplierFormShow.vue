<script setup>
    import {computed, ref} from 'vue'
    import AppSupplierShowTabAccounting from './tabs/AppSupplierShowTabAccounting.vue'
    import AppSupplierShowTabAddresses from './tabs/AppSupplierShowTabAddresses.vue'
    import AppSupplierShowTabContacts from './tabs/AppSupplierShowTabContacts.vue'
    import AppSupplierShowTabFichiers from './tabs/AppSupplierShowTabFichiers.vue'
    import AppSupplierShowTabGeneral from './tabs/AppSupplierShowTabGeneral.vue'
    import AppSupplierShowTabPurchase from './tabs/AppSupplierShowTabPurchase.vue'
    import AppSupplierShowTabQuality from './tabs/AppSupplierShowTabQuality.vue'
    import AppSuspense from '../../../AppSuspense.vue'
    import AppTab from '../../../tab/AppTab.vue'
    import api from '../../../../api'
    import {useIncotermStore} from '../../../../stores/logistic/incoterm/incoterm'
    import useOptions from '../../../../stores/option/options'
    import {useRoute} from 'vue-router'
    import {useSocietyStore} from '../../../../stores/management/societies/societies'
    import {useSuppliersStore} from '../../../../stores/purchase/supplier/suppliers'

    const route = useRoute()
    const idSupplier = route.params.id_supplier

    //Création des variables locales
    const isError2 = ref(false)
    const violations2 = ref([])
    //Création des Stores
    const fetchCurrencyOptions = useOptions('currencies')
    const fetchCompanyOptions = useOptions('companies')
    // const fetchOptions = useOptions('countries')
    // await fetchOptions.fetchOp()
    const fetchOptions = await api('/api/countries/options', 'GET')
    const optionsCountries = fetchOptions['hydra:member'].map(country => ({
        text: country.name,
        value: country.code
    }))
    //console.log(fetchOptions)
    const fetchSuppliersStore = useSuppliersStore()
    const fetchIncotermStore = useIncotermStore()
    const fetchSocietyStore = useSocietyStore()
    //Chargement du Fournisseur courant
    await fetchSuppliersStore.fetchOne(idSupplier)

    //Chargement des informations liées
    await fetchSuppliersStore.fetchVatMessage()
    await fetchIncotermStore.fetch()
    await fetchSocietyStore.fetch()
    await fetchCurrencyOptions.fetchOp()
    await fetchCompanyOptions.fetchOp()
    const societyId = Number(fetchSuppliersStore.supplier.society.match(/\d+/))
    await fetchSocietyStore.fetchById(societyId)
    // const dataSuppliers = computed(() =>
    //     Object.assign(fetchSuppliersStore.suppliers, fetchSocietyStore.item))
    const managed = computed(() => {
        const data = {managedCopper: fetchSocietyStore.society.copper.managed}
        return data
    })
    fetchSocietyStore.society.orderMin.code = 'EUR'
    fetchSocietyStore.society.invoiceMin.code = 'EUR'

    const list = computed(() => ({...fetchSocietyStore.society, ...managed.value}))
    const listSuppliers = computed(() => ({
        ...fetchSuppliersStore.supplier,
        ...list.value,
        ...fetchSocietyStore.vatMessageValue,
        ...fetchSocietyStore.incotermsValue
    }))
    function manageErrors(e) {
        console.log(e)
    }
</script>

<template>
    <div>
        <div v-if="isError2" class="alert alert-danger" role="alert">
            <div v-for="violation in violations2" :key="violation">
                <li>{{ violation.propertyPath }} {{ violation.message }}</li>
            </div>
        </div>
        <AppTabs id="gui-start" class="gui-start-content">
            <AppTab
                id="gui-start-main"
                active
                title="Généralités"
                icon="pencil"
                tabs="gui-start">
                <div class="app-tab-content">
                    <AppSuspense>
                        <AppSupplierShowTabGeneral/>
                    </AppSuspense>
                </div>
            </AppTab>
            <AppTab
                id="gui-start-files"
                title="Fichiers"
                icon="laptop"
                tabs="gui-start">
                <AppSuspense><AppSupplierShowTabFichiers/></AppSuspense>
            </AppTab>
            <AppTab
                id="gui-start-quality"
                title="Qualité"
                icon="certificate"
                tabs="gui-start">
                <AppSuspense><AppSupplierShowTabQuality :component-attribute="listSuppliers"/></AppSuspense>
            </AppTab>
            <AppTab
                id="gui-start-purchase-logistics"
                title="Achat/Logistique"
                icon="bag-shopping"
                tabs="gui-start">
                <AppSuspense><AppSupplierShowTabPurchase/></AppSuspense>
            </AppTab>
            <AppTab
                id="gui-start-accounting"
                title="Comptabilité"
                icon="industry"
                tabs="gui-start">
                <AppSuspense><AppSupplierShowTabAccounting :component-attribute="listSuppliers"/></AppSuspense>
            </AppTab>
            <AppTab
                id="gui-start-addresses"
                title="Adresse"
                icon="location-dot"
                tabs="gui-start">
                <AppSuspense><AppSupplierShowTabAddresses :options-countries="optionsCountries"/></AppSuspense>
            </AppTab>
            <AppTab
                id="gui-start-contacts"
                title="Contacts"
                icon="file-contract"
                tabs="gui-start">
                <AppSuspense><AppSupplierShowTabContacts :options-countries="optionsCountries" @error="manageErrors"/></AppSuspense>
            </AppTab>
        </AppTabs>
    </div>
</template>

<style scoped>
    div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
    .gui-start-content {
        font-size: 14px;
    }
</style>
