<script setup>
    //inlist
    // import generateItems from '../../../stores/table/items'
    // import {useRoute} from 'vue-router'
    // import {useTableMachine} from '../../../machine'
    // const products = generateItems('products')
    // const route = useRoute()
    // const machine = useTableMachine(route.name)
    import {computed, ref} from 'vue'
    // import AppProductListComposition from './AppProductListComposition.vue'
    import AppProductListCommande from './AppProductListCommande.vue'

    const guiRatio = ref(0.5)
    const guiRatioPercent = computed(() => `${guiRatio.value * 100}%`)

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
    <AppTabs id="gui-start-bottom" class="gui-start-content-bottom">
        <h1>Compositions</h1>
        <AppTab id="gui-start-compositions" active title="Compositions" icon="sitemap" tabs="gui-start-bottom">
            <AppSuspense>
                <!-- <AppProductListComposition/> -->
            </AppSuspense>
        </AppTab>
        <h1>Commandes</h1>
        <AppTab id="gui-start-commandes" active title="Commandes" icon="hand-holding-usd" tabs="gui-start-bottom">
            <AppSuspense>
                <AppProductListCommande/>
            </AppSuspense>
        </AppTab>
    </AppTabs>
</template>

<style scoped>
    .gui {
        --gui-ratio: v-bind(guiRatioPercent);
    }
</style>
