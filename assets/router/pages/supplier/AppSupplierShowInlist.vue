<script setup>
    //inlist
    // import generateItems from '../../../stores/table/items'
    // import {useRoute} from 'vue-router'
    // import {useTableMachine} from '../../../machine'
    import {computed, ref} from 'vue'
    import AppSupplierListFourniture from './AppSupplierListFourniture.vue'
    import AppSupplierListRetard from './AppSupplierListRetard.vue'
    import AppSupplierListCommande from './AppSupplierListCommande.vue'

    const guiRatio = ref(0.5)
    const guiRatioPercent = computed(() => `${guiRatio.value * 100}%`)

    // const products = generateItems('products')
    // const route = useRoute()
    // const machine = useTableMachine(route.name)
    // const fieldsFournitures = [
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Composant',
    //         name: 'composant',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Ref. Fournisseur ',
    //         name: 'refFournisseur ',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Prix ',
    //         name: 'prix ',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Quantité ',
    //         name: 'quantité ',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Texte ',
    //         name: 'texte ',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     }
    // ]

    // const fieldsRéceptions = [
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Crée le ',
    //         name: 'créeLe ',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Composant',
    //         name: 'composant',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Retard ',
    //         name: 'retard',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Evènement ',
    //         name: 'evènement',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Message',
    //         name: 'message',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Note',
    //         name: 'note',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Cmde Fournisseur Ferme',
    //         name: 'fournisseurFerme',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Cmde de Composant Fournisseur',
    //         name: 'composantFournisseur',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Quantité',
    //         name: 'quantité',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     }
    // ]
    // const fieldsCommandes = [
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Référence',
    //         name: 'référence',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Date de Confirmation',
    //         name: 'dateConfirmation',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Date Demandée',
    //         name: 'dateDemandée',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Date de Confirmation',
    //         name: 'dateConfirmation',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Dernière Réception',
    //         name: 'dernièreRéception',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Statut de Cmde Fournisseur',
    //         name: 'statutFournisseur',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Supplément Fret',
    //         name: 'supplémentFret',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Commentaire',
    //         name: 'commentaire',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Info Public',
    //         name: 'infoPublic',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     }
    // ]
</script>

<template>
    <AppTabs id="gui-start-bottom" class="gui-start-content-bottom">
        <h1>Fournitures</h1>
        <AppTab id="gui-start-fournitures" active title="Fournitures" icon="puzzle-piece" tabs="gui-start-bottom">
            <AppSuspense>
                <AppSupplierListFourniture/>
            </AppSuspense>
        </AppTab>
        <h1>Retards</h1>
        <AppTab id="gui-start-retard" active title="Retard" icon="receipt" tabs="gui-start-bottom">
            <AppSuspense>
                <AppSupplierListRetard/>
            </AppSuspense>
        </AppTab>
        <h1>Commandes</h1>
        <AppTab id="gui-start-commandes" active title="Commandes" icon="shopping-cart" tabs="gui-start-bottom">
            <AppSuspense>
                <AppSupplierListCommande/>
            </AppSuspense>
        </AppTab>
    </AppTabs>
</template>

<style scoped>
    .gui {
        --gui-ratio: v-bind(guiRatioPercent);
    }
</style>
