<script setup>
    import AppSupplierListFourniture from './AppSupplierListFourniture.vue'
    import AppSupplierListRetard from './AppSupplierListRetard.vue'
    import AppSupplierListCommande from './AppSupplierListCommande.vue'
</script>

<template>
    <AppTabs id="gui-start-bottom" class="gui-start-content-bottom" format-nav="block">
        <AppTab id="gui-start-fournitures" active title="Fournitures" icon="puzzle-piece" tabs="gui-start-bottom">
            <AppSuspense>
                <AppSupplierListFourniture/>
            </AppSuspense>
        </AppTab>
        <AppTab id="gui-start-retard" title="Retards" icon="receipt" tabs="gui-start-bottom">
            <AppSuspense>
                <AppSupplierListRetard/>
            </AppSuspense>
        </AppTab>
        <AppTab id="gui-start-commandes" title="Commandes" icon="shopping-cart" tabs="gui-start-bottom">
            <AppSuspense>
                <AppSupplierListCommande/>
            </AppSuspense>
        </AppTab>
    </AppTabs>
</template>

<style scoped>
    div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
</style>
