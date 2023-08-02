<script setup>
    import AppComponentListCasEmplois from './bottom/AppComponentListCasEmplois.vue'
    import AppComponentListOF from './bottom/AppComponentListOF.vue'
    import AppComponentListBesoinEnregistre from './bottom/AppComponentListBesoinEnregistre.vue'
    import AppComponentListStock from './bottom/AppComponentListStock.vue'

    // import {useComponentStoreShowInlist} from '../../../../../stores/component/componentStoreShowInlist'

    // const maRoute = useRoute()
    // const componentId = maRoute.params.id_component

    // const storeComponentListBesoinEnregistre = useComponentListBesoinEnregistreStore()
    // storeComponentListBesoinEnregistre.setIdComponent(componentId)

    // const storeComponentProduct = useComponentStoreShowInlist()
    // storeComponentProduct.fetch()

    // const fieldsCasEmplois = [
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Produit',
    //         name: 'produit',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Mandat ',
    //         name: 'mandat ',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Quantité ',
    //         name: 'quantité ',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Unité ',
    //         name: 'unité ',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     }
    // ]

    //composant equivalant tableau der ef ajouter une ligne

    // const fieldsOutilsConsommateurs = [
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Nom ',
    //         name: 'name ',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Ref',
    //         name: 'ref',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Description ',
    //         name: 'description',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Compagnie ',
    //         name: 'compagnie',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'engineGroup.label',
    //         name: 'engineGroup',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     }
    // ]
    // const fieldsCommandes = [
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Commande',
    //         name: 'commande',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Réf. fourn.',
    //         name: 'RéfFourn',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Quantité demandée',
    //         name: 'quantitéDemandée',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Quantité reçue',
    //         name: 'quantitéReçue',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Quantité restante',
    //         name: 'quantitéRestante',
    //         sort: false,
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
    //     }
    // ]
    // const fieldsAttributs = [
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Colors',
    //         name: 'colors',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Composition',
    //         name: 'composition',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Diamètre Extérieur',
    //         name: 'diamètreExtérieur',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Matériel',
    //         name: 'matériel',
    //         sort: false,
    //         type: 'text',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Nombre',
    //         name: 'nombre',
    //         sort: false,
    //         type: 'number',
    //         update: true
    //     },
    //     {
    //         create: false,
    //         filter: true,
    //         label: 'Section',
    //         name: 'section',
    //         sort: true,
    //         type: 'text',
    //         update: true
    //     }
    // ]
</script>

<template>
    <div>
        <AppTabs id="gui-start-bottom" class="gui-start-content-bottom" format-nav="block">
            <AppTab id="gui-start-casEmplois" active title="Cas d'emplois" icon="list-squares" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppComponentListCasEmplois/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-outils" title="OF en cours" icon="screwdriver-wrench" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppComponentListOF/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-besoinEnregistre" title="Besoins enregistrés" icon="shopping-cart" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppComponentListBesoinEnregistre/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-stocks" title="Stocks" icon="shopping-cart" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppComponentListStock/>
                </AppSuspense>
            </AppTab>
        </AppTabs>
    </div>
</template>

<style scoped>
    div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
</style>
