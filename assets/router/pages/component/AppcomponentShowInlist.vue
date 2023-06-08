<script setup>
    //inlist
    import generateItems from '../../../stores/table/items'
    import {useRoute} from 'vue-router'
    import {useTableMachine} from '../../../machine'
    const products = generateItems('products')
    const route = useRoute()
    const machine = useTableMachine(route.name)

    const fieldsCasEmplois = [
        {
            create: false,
            filter: true,
            label: 'Produit',
            name: 'produit',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Mandat ',
            name: 'mandat ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité ',
            name: 'quantité ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Unité ',
            name: 'unité ',
            sort: false,
            type: 'text',
            update: true
        }
    ]

    const fieldsOutilsConsommateurs = [
        {
            create: false,
            filter: true,
            label: 'Nom ',
            name: 'name ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Ref',
            name: 'ref',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Description ',
            name: 'description',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Compagnie ',
            name: 'compagnie',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'engineGroup.label',
            name: 'engineGroup',
            sort: true,
            type: 'text',
            update: true
        }
    ]
    const fieldsCommandes = [
        {
            create: false,
            filter: true,
            label: 'Commande',
            name: 'commande',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Réf. fourn.',
            name: 'RéfFourn',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité demandée',
            name: 'quantitéDemandée',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité reçue',
            name: 'quantitéReçue',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité restante',
            name: 'quantitéRestante',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date Demandée',
            name: 'dateDemandée',
            sort: true,
            type: 'text',
            update: true
        }
    ]
    const fieldsAttributs = [
        {
            create: false,
            filter: true,
            label: 'Colors',
            name: 'colors',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Composition',
            name: 'composition',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Diamètre Extérieur',
            name: 'diamètreExtérieur',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Matériel',
            name: 'matériel',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Nombre',
            name: 'nombre',
            sort: false,
            type: 'number',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Section',
            name: 'section',
            sort: true,
            type: 'text',
            update: true
        }
    ]
</script>

<template>
    <div>
        <AppTabs id="gui-start" class="gui-start-content">
            <AppTab id="gui-start-cacEmplois" active icon="product-hunt" brands title="Cas d'emplois">
                <AppTable :id="route.name" :fields="fieldsCasEmplois" :store="products" :machine="machine"/>
            </AppTab>
            <AppTab id="gui-start-outils" icon="tools" title="Outils consommateurs">
                <AppTable :id="route.name" :fields="fieldsOutilsConsommateurs" :store="products" :machine="machine"/>
            </AppTab>
            <AppTab id="gui-start-commandes" icon="shopping-cart" title="Commandes">
                <AppTable :id="route.name" :fields="fieldsCommandes" :store="products" :machine="machine"/>
            </AppTab>
            <AppTab id="gui-start-attributs" icon="shopping-cart" title="Attributs">
                <AppTable :id="route.name" :fields="fieldsAttributs" :store="products" :machine="machine"/>
            </AppTab>
        </AppTabs>
    </div>
</template>
