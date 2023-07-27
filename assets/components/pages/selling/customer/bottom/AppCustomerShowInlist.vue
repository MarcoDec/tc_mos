<script setup>
    import AppCustomerListProduct from './AppCustomerListProduct.vue'
    import AppCustomerListOF from './AppCustomerListOF.vue'
    import AppCustomerListCommande from './AppCustomerListCommande.vue'
    import AppCustomerListBL from './AppCustomerListBL.vue'
    import AppCustomerListFacture from './AppCustomerListFacture.vue'
</script>

<template>
    <div>
        <AppTabs id="gui-start-bottom" class="gui-start-content-bottom" format-nav="block">
            <AppTab id="gui-start-products" active title="Produits" icon="toolbox" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppCustomerListProduct/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-of" title="OF" icon="file-invoice" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppCustomerListOF/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-commandes" title="Commandes" icon="hand-holding-usd" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppCustomerListCommande/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-bl" title="BL" icon="clipboard-list" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppCustomerListBL/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-factures" title="Factures" icon="file-invoice" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppCustomerListFacture/>
                </AppSuspense>
            </AppTab>
        </AppTabs>
    </div>
</template>

<style scoped>
    div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
</style>
