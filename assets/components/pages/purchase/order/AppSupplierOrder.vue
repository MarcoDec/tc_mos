<script setup>
    import AppCollectionTableCommande from './AppCollectionTableCommande.vue'
    import AppCollectionTableEchange from './AppCollectionTableEchange.vue'
    import AppCollectionTableGestion from './AppCollectionTableGestion.vue'
    import AppCollectionTableQualite from './AppCollectionTableQualite.vue'
    import AppCollectionTableReception from './AppCollectionTableReception.vue'
    import AppSuspense from '../../../../components/AppSuspense.vue'
</script>

<template>
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
                <h1>en cours de traitement</h1>
            </AppTab>
            <AppTab
                id="gui-start-purchase-quantite"
                icon="chart-line"
                tabs="gui-start"
                title="Qualité">
                <!-- <AppCollectionTableQualite/> -->
            </AppTab>
            <AppTab id="gui-start-notes" icon="clipboard-list" tabs="gui-start" title="Notes">
                <h1>en cours</h1>
            </AppTab>
            <AppTab id="gui-start-echanges" icon="file-pdf" tabs="gui-start" title="Echanges">
                <!-- <AppCollectionTableEchange/> -->
            </AppTab>
        </AppTabs>
    </AppCard>
</template>

<style scoped>
 div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
    .gui-start-content {
        font-size: 14px;
    }
    #gui-start-main {
        padding-bottom: 150px;
    }
.cardOrderSupplier {
  border: 6px solid #1d583d;
}
</style>
