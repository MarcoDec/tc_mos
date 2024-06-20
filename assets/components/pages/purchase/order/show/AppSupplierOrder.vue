<script setup>
    import api from '../../../../../api'
    import useOptions from '../../../../../stores/option/options'
    import useUser from '../../../../../stores/security'
    import AppCollectionTableCommande from './AppCollectionTableCommande.vue'
    // import AppCollectionTableEchange from './AppCollectionTableEchange.vue'
    // import AppCollectionTableGestion from './AppCollectionTableGestion.vue'
    // import AppCollectionTableQualite from './AppCollectionTableQualite.vue'
    import AppCollectionTableReception from './AppCollectionTableReception.vue'
    import AppSuspense from '../../../../AppSuspense.vue'
    import AppUnderDevelopment from "../../../../gui/AppUnderDevelopment.vue"
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome"
    import AppWorkflowShow from "../../../../workflow/AppWorkflowShow.vue"
    import AppBtn from "../../../../AppBtn.vue"
    import AppShowGuiGen from "../../../AppShowGuiGen.vue"
    import {useRoute, useRouter} from "vue-router"
    import {computed, onBeforeMount, ref} from 'vue'
    import {usePurchaseOrderStore} from "../../../../../stores/purchase/order/purchaseOrder"

    const route = useRoute()
    const idPurchaseOrder = Number(route.params.id)
    const iriPurchaseOrder = ref('')
    const fetchPurchaseOrderStore = usePurchaseOrderStore()
    const beforeMountDataLoaded = ref(false)
    const modeDetail = ref(true)
    const keyTitle = ref(0)
    const keyTabs = ref(0)
    const isFullScreen = ref(false)
    const generalityKey = ref(0)
    const generalityData = ref({})
    const supplier = ref({})
    const user = useUser()
    const isAdmin = user.isPurchaseAdmin
    const fetchCompaniesOptions = useOptions('companies')
    const companiesOptions = computed(() =>
        fetchCompaniesOptions.options.map(op => {
            const text = op.text
            const value = op.value
            return {text, value}
        }))
    const optionsOrderFamily = computed(() => fetchPurchaseOrderStore.orderFamilyOptions())
    const kindOptions = [
        {text: 'El', value: 'El'},
        {text: 'Prototype', value: 'Prototype'},
        {text: 'Série', value: 'Série'},
        {text: 'Pièce de rechange', value: 'Pièce de rechange'}
    ]
    const fieldsGenerality = computed(() => {
        let contactFilter = null
        // console.log('generalityData', generalityData.value)
        if (typeof generalityData.value.wrap1 === 'undefined') {
            return []
        }
        if (typeof generalityData.value.wrap1.supplier !== 'undefined') {
            contactFilter = {field: 'society', value: generalityData.value.wrap1.supplier}
        }
        return [
            {
                label: 'Test wrap',
                name: 'wrap1',
                mode: 'wrap',
                wrapWidth: '40%',
                wrapMinWidth: '300px',
                fontSize: '0.6rem',
                children: [
                    {
                        label: 'Compagnie Gérante *',
                        name: 'company',
                        readOnly: !isAdmin,
                        info: 'Seuls les administrateurs peuvent modifier la compagnie gérante.',
                        options: {
                            label: value =>
                                companiesOptions.value.find(option => option.type === value)?.text ?? null,
                            options: companiesOptions.value
                        },
                        type: 'select'
                    },
                    {
                        label: 'Type de commande *',
                        name: 'orderFamily',
                        options: {
                            label: value => optionsOrderFamily.value.find(option => option.type === value)?.text ?? null,
                            options: optionsOrderFamily.value
                        },
                        type: 'select'
                    },
                    {
                        label: 'Fournisseur',
                        name: 'supplier',
                        type: 'multiselect-fetch',
                        api: '/api/suppliers',
                        filteredProperty: 'name',
                        readOnly: !isAdmin,
                        info: 'Seuls les administrateurs peuvent modifier le fournisseur.',
                        max: 1
                    },
                    {label: 'Référence commande fournisseur *', name: 'ref', type: 'text'},
                    {
                        label: 'Type de Produit/Composant',
                        name: 'kind',
                        readOnly: !isAdmin,
                        info: 'Seuls les administrateurs peuvent modifier le type de produit.',
                        options: {
                            label: value =>
                                kindOptions.find(option => option.type === value)?.text ?? null,
                            options: kindOptions
                        },
                        type: 'select'
                    },
                    {
                        label: 'Adresse de livraison pour cette commande *',
                        name: 'deliveryCompany',
                        type: 'multiselect-fetch',
                        api: '/api/companies',
                        filteredProperty: 'name',
                        min: true,
                        max: 1
                    }
                ]
            },
            {
                label: 'wrap2',
                name: 'wrap2',
                mode: 'wrap',
                wrapWidth: '40%',
                wrapMinWidth: '300px',
                fontSize: '0.6rem',
                children: [
                    {
                        label: 'Contact Fournisseur',
                        name: 'contact',
                        type: 'multiselect-fetch',
                        api: '/api/supplier-contacts',
                        filteredProperty: 'fullName',
                        permanentFilters: [contactFilter],
                        min: true,
                        max: 1
                    },
                    {label: 'Notes', name: 'notes', type: 'textarea'}
                ]
            }
        ]
    })
    const isLoaded = ref(false)
    const requestDetails = () => {
        modeDetail.value = true
    }
    const requestExploitation = () => {
        modeDetail.value = false
    }
    const onUpdated = () => {
        updateStores()
    }
    const activateFullScreen = () => {
        isFullScreen.value = true
    }
    const deactivateFullScreen = () => {
        isFullScreen.value = false
    }
    const router = useRouter()
    const order = computed(() => fetchPurchaseOrderStore.purchaseOrder)
    const goBack = () => {
        router.push({name: 'purchaseOrderList'})
    }
    async function updateGeneralityDataFromApi(data) {
        //Si customer est défini, on le charge afin de pouvoir identifier le type de commande possible
        if (data.supplier){
            // console.log('client non encore défini => chargement')
            fetchPurchaseOrderStore.selectedSupplier = await api(data.supplier, 'GET')
            // console.log('client chargé', fetchCustomerOrderStore.selectedCustomer)
        }
        // console.log('updateGeneralityDataFromApi', data)
        generalityData.value = {
            wrap1: {
                company: data.company,
                supplier: data.supplier,
                kind: data.kind,
                ref: data.ref,
                orderFamily: data.orderFamily,
                deliveryCompany: data.deliveryCompany
            },
            wrap2: {
                contact: data.contact,
                notes: data.notes
            }
        }
        // console.log('generalityData update', generalityData.value)
    }
    function updateStores() {
        const promises = []
        promises.push(fetchPurchaseOrderStore.fetchById(idPurchaseOrder))
        promises.push(fetchCompaniesOptions.fetchOp())
        Promise.all(promises).then(async () => {
            // console.log('updateStores -> purchaseOrder', fetchPurchaseOrderStore.purchaseOrder)
            iriPurchaseOrder.value = fetchPurchaseOrderStore.purchaseOrder['@id']
            api(fetchPurchaseOrderStore.purchaseOrder.supplier, 'GET').then(response => {
                supplier.value = response
            })
            await updateGeneralityDataFromApi(fetchPurchaseOrderStore.purchaseOrder)
            isLoaded.value = true
            generalityKey.value++
            beforeMountDataLoaded.value = true
        })
    }
    async function updateGeneralityDataFromAppCardShow(data) {
        // console.log('current GeneralityData', generalityData.value)
        console.log('data', data)
        //generalityData.value = data
    }
    async function updateGeneralitySupplierOrder(){
        //On doit vérifier avant de valider les modifications si le type de commande est de type EDI qu'il n'en existe pas déjà une, auquel cas on ne peut pas modifier le type de commande
        const orderFamily = generalityData.value.orderFamily
        if (orderFamily === 'edi_orders' || orderFamily === 'edi_delfor'){
            const idSupplier = order.value.supplier
            const hasEdiPurchaseOrders = await fetchPurchaseOrderStore.hasActiveEdiOrders(idSupplier, order.value.id)
            if (hasEdiPurchaseOrders){
                alert('Il existe déjà une commande de type EDI active pour ce client')
                //On recharge la page
                window.location.reload()
                return
            }
        }
        //On doit vérifier avant de valider les modifications si le type de commande est de type prévisionnelle qu'il n'en existe pas déjà une, auquel cas on ne peut pas modifier le type de commande
        if (orderFamily === 'forecast' || orderFamily === 'edi_delfor'){
            const idSupplier = order.value.supplier
            const hasForecastPurchaseOrders = await fetchPurchaseOrderStore.hasActiveForecastOrders(idSupplier, order.value.id)
            if (hasForecastPurchaseOrders){
                alert('Il existe déjà une commande active de type prévisionnelle pour ce client')
                //On recharge la page
                window.location.reload()
                return
            }
        }
        const payload = {
            id: idPurchaseOrder,
            PurchaseOrder: {
                company: generalityData.value.wrap1.company,
                supplier: generalityData.value.wrap1.supplier,
                kind: generalityData.value.wrap1.kind,
                notes: generalityData.value.wrap2.notes,
                ref: generalityData.value.wrap1.ref,
                orderFamily: generalityData.value.wrap1.orderFamily,
                deliveryCompany: generalityData.value.wrap1.deliveryCompany,
                contact: generalityData.value.wrap2.contact
            }
        }
        await fetchPurchaseOrderStore.updatePurchaseOrder(payload)
        keyTabs.value++
    }
    onBeforeMount(() => {
        updateStores()
    })
</script>

<template>
    <AppSuspense>
        <AppShowGuiGen>
            <template #gui-left>
                <div :key="`title-${keyTitle}`" class="bg-white border-1 p-1">
                    <div class="d-flex flex-row">
                        <div>
                            <button class="text-dark" @click="goBack">
                                <FontAwesomeIcon icon="bullhorn"/>
                            </button>
                            <b>
                                Commande Fournisseur
                                <span v-if="fetchPurchaseOrderStore.purchaseOrder.ref !== null">
                                    ({{ fetchPurchaseOrderStore.purchaseOrder.ref }})
                                </span>
                            </b>
                            : {{ fetchPurchaseOrderStore.purchaseOrder.id }}
                        </div>
                        <AppSuspense>
                            <AppWorkflowShow :workflow-to-show="['purchase_order_state', 'blocker']" :item-iri="iriPurchaseOrder"/>
                        </AppSuspense>
                        <span class="ml-auto">
                            <AppBtn :class="{'selected-detail': modeDetail}" label="Détails" icon="eye" variant="secondary" @click="requestDetails"/>
                            <AppBtn :class="{'selected-detail': !modeDetail}" label="Exploitation" icon="industry" variant="secondary" @click="requestExploitation"/>
                        </span>
                    </div>
                </div>
                <div :key="generalityKey" v-if="isLoaded" class="row">
                    <AppCardShow
                        id="Generality"
                        :fields="fieldsGenerality"
                        :component-attribute="generalityData"
                        :title="`Informations générales de la commande${isAdmin ? ' (admin mode)' : ''}`"
                        @update:model-value="updateGeneralityDataFromAppCardShow"
                        @update="updateGeneralitySupplierOrder"/>
                </div>
            </template>
            <template #gui-bottom>
                <AppCard title="" class="cardOrderSupplier">
                    <AppTabs id="gui-start" class="gui-start-content">
                        <AppTab
                            id="gui-start-detail"
                            active
                            icon="sitemap"
                            tabs="gui-start"
                            title="Détail de la commande">
                            <AppSuspense>
                                <AppCollectionTableCommande/>
                            </AppSuspense>
                        </AppTab>
                        <AppTab
                            id="gui-start-purchase-rec"
                            icon="truck-moving"
                            tabs="gui-start"
                            title="Réception">
                            <AppSuspense>
                                <AppCollectionTableReception/>
                            </AppSuspense>
                        </AppTab>
                        <AppTab
                            id="gui-start-bl"
                            icon="clipboard-check"
                            tabs="gui-start"
                            title="BL">
                            <AppUnderDevelopment/>
                        </AppTab>
                        <AppTab
                            id="gui-start-purchase-quantite"
                            icon="chart-line"
                            tabs="gui-start"
                            title="Qualité">
                            <AppUnderDevelopment/>
                            <!-- <AppCollectionTableQualite/> -->
                        </AppTab>
                        <AppTab id="gui-start-notes" icon="clipboard-list" tabs="gui-start" title="Notes">
                            <AppUnderDevelopment/>
                        </AppTab>
                        <AppTab id="gui-start-echanges" icon="file-pdf" tabs="gui-start" title="Echanges">
                            <AppUnderDevelopment/>
                            <!-- <AppCollectionTableEchange/> -->
                        </AppTab>
                    </AppTabs>
                </AppCard>
<!--                <div :class="{'full-screen': isFullScreen}" class="bg-warning-subtle font-small">-->
<!--                    <div class="full-visible-width">-->
<!--                        <AppSuspense>-->
<!--                            <AppEmployeeFormShow v-if="modeDetail" :key="`formtab-${keyTabs}`" class="width100"/>-->
<!--                            <AppEmployeeShowInlist v-else :key="`formlist-${keyTabs}`" class="width100"/>-->
<!--                        </AppSuspense>-->
<!--                    </div>-->
<!--                    <span>-->
<!--                        <FontAwesomeIcon v-if="isFullScreen" icon="fa-solid fa-magnifying-glass-minus" @click="deactivateFullScreen"/>-->
<!--                        <FontAwesomeIcon v-else icon="fa-solid fa-magnifying-glass-plus" @click="activateFullScreen"/>-->
<!--                    </span>-->
<!--                </div>-->
            </template>
            <template #gui-right>
                RIGHT
                <!--            {{ route.params.id_employee }}-->
            </template>
        </AppShowGuiGen>
    </AppSuspense>

</template>

<style scoped>
</style>
