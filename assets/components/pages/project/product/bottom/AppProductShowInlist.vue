<script setup>
    // import AppProductListComposition from './AppProductListComposition.vue'
    import AppProductListCommande from './AppProductListCommande.vue'
    // const fieldsComposition = [
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Ref - Designation',
    //         name: 'refDesignation',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Photo.',
    //         name: 'photo ',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Famille',
    //         name: 'famille ',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Quantity ',
    //         name: 'quantity ',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Unit ',
    //         name: 'unit ',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Stk site',
    //         name: 'stkSite',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Cap.Fab.Site',
    //         name: 'capFabSite ',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Stk total',
    //         name: 'stkTotal',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Cap.Fab. Tconcept',
    //         name: 'capFabTconcept',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     }
    // ]
    // const fieldsCommandes = [
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Client',
    //         name: 'client',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'reference',
    //         name: 'reference',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Quantité confirmée',
    //         name: 'quantiteConfirmee',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Quantité souhaitée',
    //         name: 'quantiteSouhaitee',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Quantité envoyée',
    //         name: 'quantiteEnvoyee',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Date de livraison',
    //         name: 'dateLivraison',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'date de livraison souhaitée',
    //         name: 'dateLivraisonSouhaitee',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     }
    // ]
</script>

<template>
    <AppTabs id="gui-start-bottom" class="gui-start-content-bottom" format-nav="block">
        <AppTab id="gui-start-compositions" active title="Compositions" icon="sitemap" tabs="gui-start-bottom">
            <AppSuspense>
                <!-- <AppProductListComposition/> -->
            </AppSuspense>
        </AppTab>
        <AppTab id="gui-start-commandes" title="Commandes" icon="shopping-cart" tabs="gui-start-bottom">
            <AppSuspense>
                <AppProductListCommande/>
            </AppSuspense>
        </AppTab>
    </AppTabs>
</template>

<style scoped>
    div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
</style>
