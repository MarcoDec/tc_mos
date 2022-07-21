<script setup>
    import AppCollectionTableCommande from './AppCollectionTableCommande.vue'
    import AppCollectionTableEchange from './AppCollectionTableEchange.vue'
    import AppCollectionTableGestion from './AppCollectionTableGestion.vue'
    import AppCollectionTableQualite from './AppCollectionTableQualite.vue'
    import AppCollectionTableReception from './AppCollectionTableReception.vue'
</script>

<template>
    <AppCard class="cardOrderSupplier">
        <AppTabs id="gui-start" class="gui-start-content">
            <AppTab
                id="gui-start-detail"
                active
                icon="sitemap"
                title="Détail de la commande">
                <AppCollectionTableCommande/>
            </AppTab>
            <AppTab
                id="gui-start-gestion"
                icon="folder"
                title="Gestion des modifications">
                <AppCollectionTableGestion/>
            </AppTab>
            <AppTab id="gui-start-reception" icon="truck-moving" title="Réception">
                <AppTabs id="gui-start" class="gui-start-content">
                    <AppTab
                        id="gui-start-purchase-rec"
                        icon="truck-moving"
                        title="Réception">
                        <AppCollectionTableReception/>
                    </AppTab>
                    <AppTab id="gui-start-bl" icon="clipboard-check" title="BL">
                        <h1>en cours de traitement</h1>
                    </AppTab>
                </AppTabs>
            </AppTab>
            <AppTab
                id="gui-start-purchase-quantite"
                icon="chart-line"
                title="Qualité">
                <AppCollectionTableQualite/>
            </AppTab>
            <AppTab id="gui-start-notes" icon="clipboard-list" title="Notes">
                <h1>en cours</h1>
            </AppTab>
            <AppTab id="gui-start-echanges" icon="file-pdf" title="Echanges">
                <AppCollectionTableEchange/>
            </AppTab>
        </AppTabs>
    </AppCard>
</template>

<style>
.cardOrderSupplier {
  border: 6px solid #1d583d;
}
</style>
