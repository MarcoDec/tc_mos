<script setup>
    //import AppProductListCommande from './AppProductListCommande.vue'
    import AppProductNomenclature from './AppProductNomenclature.vue'
    //import AppProductStockList from './AppProductStockList.vue'
</script>

<template>
    <AppTabs id="gui-start-bottom" class="gui-start-content-bottom" format-nav="flex">
        <AppTab id="gui-start-nomenclature" active title="Nomenclature" icon="sitemap" tabs="gui-start-bottom">
            <AppSuspense>
                <AppProductNomenclature/>
            </AppSuspense>
        </AppTab>
        <!--        <AppTab id="gui-start-stocks" active title="Stocks" icon="boxes-stacked" tabs="gui-start-bottom">-->
        <!--            <AppSuspense>-->
        <!--                <AppProductStockList/>-->
        <!--            </AppSuspense>-->
        <!--        </AppTab>-->
        <!--        <AppTab id="gui-start-commandes" title="Commandes" icon="shopping-cart" tabs="gui-start-bottom">-->
        <!--            <AppSuspense>-->
        <!--                <AppProductListCommande/>-->
        <!--            </AppSuspense>-->
        <!--        </AppTab>-->
    </AppTabs>
</template>

<style scoped>
    div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
</style>
