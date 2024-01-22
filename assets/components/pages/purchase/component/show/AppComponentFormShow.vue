<script setup>
    import AppShowComponentTabAttributes from './bottom/AppShowComponentTabAttributes.vue'
    import AppShowComponentTabFichiers from './bottom/AppShowComponentTabFichiers.vue'
    import AppShowComponentTabLogistic from './bottom/AppShowComponentTabLogistic.vue'
    import AppShowComponentTabPurchase from './bottom/AppShowComponentTabPurchase.vue'
    import AppShowComponentTabQuality from './bottom/AppShowComponentTabQuality.vue'
    import AppShowComponentTabSpecification from './bottom/AppShowComponentTabSpecification.vue'
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab
            id="gui-start-attribut"
            active
            title="Attribut"
            icon="sitemap"
            tabs="gui-start">
            <AppSuspense><AppShowComponentTabAttributes/></AppSuspense>
        </AppTab>
        <AppTab
            id="gui-start-files"
            title="Fichiers"
            icon="laptop"
            tabs="gui-start">
            <AppSuspense><AppShowComponentTabFichiers/></AppSuspense>
        </AppTab>
        <AppTab
            id="gui-start-quality"
            title="Qualité"
            icon="certificate"
            tabs="gui-start">
            <AppSuspense><AppShowComponentTabQuality/></AppSuspense>
        </AppTab>
        <AppTab
            id="gui-start-achat"
            title="Achat"
            icon="bag-shopping"
            tabs="gui-start">
            <AppSuspense><AppShowComponentTabPurchase/></AppSuspense>
        </AppTab>
        <AppTab
            id="gui-start-logistics"
            title="Logistique"
            icon="pallet"
            tabs="gui-start">
            <AppSuspense><AppShowComponentTabLogistic/></AppSuspense>
        </AppTab>
        <AppTab
            id="gui-start-spécifications"
            title="Spécification"
            icon="file-contract"
            tabs="gui-start">
            <AppSuspense><AppShowComponentTabSpecification/></AppSuspense>
        </AppTab>
    </AppTabs>
</template>

<style scoped>
    div.active {
        position: relative;
        z-index: 0;
        overflow: scroll;
        max-height: 100%
    }
    .gui-start-content {
        font-size: 14px;
    }
</style>
