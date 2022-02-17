<script lang="ts" setup>
    import {defineProps} from 'vue'
    defineProps<{icon: string, title: string}>()
</script>

<template>
    <h1>
        <Fa :icon="icon"/>
        {{ title }}
    </h1>
    <AppCard class="cardOrderSupplier">
        <AppTabs id="gui-start" class="gui-start-content">
            <AppTab id="gui-start-detail" active icon="sitemap" title="Détail de la commande">
                <AppCollectionTableCommande/>
            </AppTab>
            <AppTab id="gui-start-gestion" icon="folder" title="Gestion des modifications">
                <AppCollectionTableGestion/>
            </AppTab>
            <AppTab id="gui-start-reception" icon="truck-moving" title="Réception">
                <AppTabs id="gui-start" class="gui-start-content">
                    <AppTab id="gui-start-purchase-rec" icon="truck-moving" title="Réception">
                        <AppCollectionTableReception/>
                    </AppTab>
                    <AppTab id="gui-start-bl" icon="clipboard-check" title="BL">
                        <h1>en cours de traitement</h1>
                    </AppTab>
                </AppTabs>
            </AppTab>
            <AppTab id="gui-start-purchase-quantite" icon="chart-line" title="Qualité">
                <AppCollectionTableQte/>
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
.cardOrderSupplier{
  border: 6px solid #1d583d;
}
</style>
