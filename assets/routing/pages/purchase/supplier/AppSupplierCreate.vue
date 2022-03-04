<script lang="ts" setup>
    
</script>

<template>
<div>
    <AppCard class="cardOrderSupplier">
        <AppTabs id="gui-start" class="gui-start-content">
            <AppTab id="gui-start-general" active icon="sitemap" title="Général">
            </AppTab>
            <AppTab id="gui-start-qte" icon="folder" title="Qualité">
            </AppTab>
            <AppTab id="gui-start-comptabilite" icon="chart-line" title="Comptabilité">
            </AppTab>
            <AppTab id="gui-start-cuivre" icon="clipboard-list" title="Cuivre">
            </AppTab>
        </AppTabs>
    </AppCard>
</div>
</template>

<style>
.cardOrderSupplier{
  border: 6px solid #1d583d;
}
</style>