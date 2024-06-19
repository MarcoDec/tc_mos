<script setup>
    import AppCollectionTableCommande from './AppCollectionTableCommande.vue'
    // import AppCollectionTableEchange from './AppCollectionTableEchange.vue'
    // import AppCollectionTableGestion from './AppCollectionTableGestion.vue'
    // import AppCollectionTableQualite from './AppCollectionTableQualite.vue'
    import AppCollectionTableReception from './AppCollectionTableReception.vue'
    import AppSuspense from '../../../../AppSuspense.vue'
    import AppUnderDevelopment from "../../../../gui/AppUnderDevelopment.vue"
    import AppShowGui from "../../../AppShowGui.vue"
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome"
    import AppWorkflowShow from "../../../../workflow/AppWorkflowShow.vue"
    import AppBtn from "../../../../AppBtn.vue"
    import AppShowGuiGen from "../../../AppShowGuiGen.vue"
    import {useRoute, useRouter} from "vue-router"
    import {onBeforeMount, ref} from "vue"
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

    const requestDetails = () => {
        modeDetail.value = true
    }
    const requestExploitation = () => {
        modeDetail.value = false
    }
    function updateStores() {
        const promises = []
        promises.push(fetchPurchaseOrderStore.fetchById(idPurchaseOrder))
        Promise.all(promises).then(() => {
            console.log('purchaseOrder', fetchPurchaseOrderStore.purchaseOrder)
            iriPurchaseOrder.value = fetchPurchaseOrderStore.purchaseOrder['@id']
            beforeMountDataLoaded.value = true
        })
    }
    onBeforeMount(() => {
        updateStores()
    })
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
    const goBack = () => {
        router.push({name: 'purchaseOrderList'})
    }
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
<!--                <div class="d-flex flex-row">-->
<!--                    <AppImg-->
<!--                        class="width30"-->
<!--                        :file-path="fetchEmployeeStore.employee.filePath"-->
<!--                        :image-update-url="imageUpdateUrl"-->
<!--                        @update:file-path="onImageUpdate"/>-->
<!--                    <AppSuspense><AppShowEmployeeTabGeneral :key="`form-${keyTabs}`" class="width70" @updated="onUpdated"/></AppSuspense>-->
<!--                </div>-->
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
