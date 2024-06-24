<script setup>
    import AppSuspense from '../../../../AppSuspense.vue'
    import AppUnderDevelopment from '../../../../gui/AppUnderDevelopment.vue'
    import AppCollectionTableCommande from './bottom/AppCollectionTableCommande.vue'
    import AppCollectionTableReception from './bottom/AppCollectionTableReception.vue'
    import {PortalTarget} from "portal-vue"

    /*const props = */defineProps({
        order: {required: true, type: Object},
        supplier: {required: true, type: Object}
    })
</script>

<template>
    <portal-target name="modals"></portal-target>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab
            id="gui-start-detail"
            active
            icon="sitemap"
            tabs="gui-start"
            title="Détail de la commande">
            <AppSuspense>
                <AppCollectionTableCommande :order="order" :supplier="supplier"/>
            </AppSuspense>
        </AppTab>
        <AppTab
            id="gui-start-purchase-rec"
            icon="truck-moving"
            tabs="gui-start"
            title="Réception">
            <AppSuspense>
                <AppCollectionTableReception :order="order"/>
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
</template>
