<script setup>
    import generateItems from '../../../stores/table/items'
    import {useRoute} from 'vue-router'
    import {useTableMachine} from '../../../machine'
    const products = generateItems('products')
    const route = useRoute()
    const machine = useTableMachine(route.name)
    const fieldsFournitures = [
        {
            create: false,
            filter: true,
            label: 'Composant',
            name: 'composant',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Ref. Fournisseur ',
            name: 'refFournisseur ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Prix ',
            name: 'prix ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité ',
            name: 'quantité ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Texte ',
            name: 'texte ',
            sort: true,
            type: 'text',
            update: true
        }
    ]

    const fieldsRéceptions = [
        {
            create: false,
            filter: true,
            label: 'Crée le ',
            name: 'créeLe ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Composant',
            name: 'composant',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Retard ',
            name: 'retard',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Evènement ',
            name: 'evènement',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Message',
            name: 'message',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Note',
            name: 'note',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Cmde Fournisseur Ferme',
            name: 'fournisseurFerme',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Cmde de Composant Fournisseur',
            name: 'composantFournisseur',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité',
            name: 'quantité',
            sort: true,
            type: 'text',
            update: true
        }
    ]
    const fieldsCommandes = [
        {
            create: false,
            filter: true,
            label: 'Référence',
            name: 'référence',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de Confirmation',
            name: 'dateConfirmation',
            sort: true,
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
        },
        {
            create: false,
            filter: true,
            label: 'Date de Confirmation',
            name: 'dateConfirmation',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Dernière Réception',
            name: 'dernièreRéception',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Statut de Cmde Fournisseur',
            name: 'statutFournisseur',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Supplément Fret',
            name: 'supplémentFret',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Commentaire',
            name: 'Commentaire',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Info Public',
            name: 'infoPublic',
            sort: true,
            type: 'text',
            update: true
        }
    ]
</script>

<template>
    <div>
        <AppTabs id="gui-start" class="gui-start-content">
            <AppTab id="gui-start-Fournitures" active icon="puzzle-piece" title="Fournitures">
                <AppTable :id="route.name" :fields="fieldsFournitures" :store="products" :machine="machine"/>
            </AppTab>
            <AppTab id="gui-start-reception" icon="receipt" title="Réceptions">
                <AppTable :id="route.name" :fields="fieldsRéceptions" :store="products" :machine="machine"/>
            </AppTab>
            <AppTab id="gui-start-commandes" icon="shopping-cart" title="Commandes">
                <AppTable :id="route.name" :fields="fieldsCommandes" :store="products" :machine="machine"/>
            </AppTab>
        </AppTabs>
    </div>
</template>
