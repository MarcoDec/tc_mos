<script setup>
    import AppShowComponentTabAttributes from './tabs/AppShowComponentTabAttributes.vue'
    import AppShowComponentTabFichiers from './tabs/AppShowComponentTabFichiers.vue'
    import AppShowComponentTabGeneral from './tabs/AppShowComponentTabGeneral.vue'
    import AppShowComponentTabLogistic from './tabs/AppShowComponentTabLogistic.vue'
    import AppShowComponentTabPurchase from './tabs/AppShowComponentTabPurchase.vue'
    import AppShowComponentTabQuality from './tabs/AppShowComponentTabQuality.vue'
    import AppShowComponentTabSpecification from './tabs/AppShowComponentTabSpecification.vue'
</script>

<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab
            id="gui-start-main"
            active
            title="Généralités"
            icon="pencil"
            tabs="gui-start">
            <AppSuspense><AppShowComponentTabGeneral/></AppSuspense>
        </AppTab>
        <!-- <AppTab
            id="gui-start-attribut"
            title="Attribut"
            icon="sitemap"
            tabs="gui-start">
            <AppSuspense><AppShowComponentTabAttributes/></AppSuspense>
        </AppTab> -->
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
    div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
    .gui-start-content {
        font-size: 14px;
    }
</style>
