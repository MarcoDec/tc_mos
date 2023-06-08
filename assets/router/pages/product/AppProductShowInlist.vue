<script setup>
    import generateItems from '../../../stores/table/items'
    import {useRoute} from 'vue-router'
    import {useTableMachine} from '../../../machine'
    const products = generateItems('products')
    const route = useRoute()
    const machine = useTableMachine(route.name)

    const fieldsComposition = [
        {
            create: false,
            filter: true,
            label: 'Ref - Designation',
            name: 'refDesignation',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Photo.',
            name: 'photo ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Famille',
            name: 'famille ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantity ',
            name: 'quantity ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Unit ',
            name: 'unit ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Stk site',
            name: 'stkSite',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Cap.Fab.Site',
            name: 'capFabSite ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Stk total',
            name: 'stkTotal',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Cap.Fab. Tconcept',
            name: 'capFabTconcept',
            sort: false,
            type: 'text',
            update: true
        }
    ]
    const fieldsCommandes = [
        {
            create: false,
            filter: true,
            label: 'Client',
            name: 'client',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'reference',
            name: 'reference',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité confirmée',
            name: 'quantitéConfirmée',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité souhaitée',
            name: 'quantitéSouhaitée',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité envoyée',
            name: 'quantitéEnvoyée',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de livraison',
            name: 'datelivraison',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'date de livraison souhaitée',
            name: 'datelivraison',
            sort: false,
            type: 'text',
            update: true
        }
    ]
</script>

<template>
    <div>
        <AppTabs id="gui-start" class="gui-start-content">
            <AppTab id="gui-start-composition" active icon="sitemap" title="Composition">
                <AppTable :id="route.name" :fields="fieldsComposition" :store="products" :machine="machine"/>
            </AppTab>
            <AppTab id="gui-start-commandes" icon="hand-holding-usd" title="Commandes">
                <AppTable :id="route.name" :fields="fieldsCommandes" :store="products" :machine="machine"/>
            </AppTab>
        </AppTabs>
    </div>
</template>
