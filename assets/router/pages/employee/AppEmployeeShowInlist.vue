<script setup>
    //inlist
    import generateItems from '../../../stores/table/items'
    import {useRoute} from 'vue-router'
    import {useTableMachine} from '../../../machine'
    const products = generateItems('products')
    const route = useRoute()
    const machine = useTableMachine(route.name)

    const fieldsFormations = [
        {
            create: false,
            filter: true,
            label: 'Sujet ',
            name: 'sujet ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de début',
            name: 'dateDébut ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Date de fin ',
            name: 'dateFin ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'date de rappel',
            name: 'dateRappel  ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Niveau',
            name: 'niveau ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'formateur',
            name: 'Formateur',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Dernière facture',
            name: 'dernièreFacture ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Prix',
            name: 'prix ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité',
            name: 'quantité',
            sort: false,
            type: 'text',
            update: true
        }
    ]
    const fieldsEvenements = [
        {
            create: false,
            filter: true,
            label: 'Date de l événement',
            name: 'dateEvénement',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Description ',
            name: 'description ',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Motif',
            name: 'motif',
            sort: true,
            type: 'text',
            update: true
        }
    ]

    const fieldsOF = [
        {
            create: false,
            filter: true,
            label: 'OF',
            name: 'OF',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Produit',
            name: 'produit',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Quantité fabriquée ',
            name: 'quantitéFabriquée',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Rebus',
            name: 'rebus',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Cadence',
            name: 'cadence',
            sort: true,
            type: 'text',
            update: true
        }
    ]

    const fieldsFactures = [
        {
            create: false,
            filter: true,
            label: 'date de début ',
            name: 'dateDébut',
            sort: true,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'date de fin',
            name: 'dateFin',
            sort: true,
            type: 'text',
            update: true
        }
    ]
</script>

<template>
    <div>
        <AppTabs id="gui-start" class="gui-start-content">
            <AppTab id="gui-start-formations" active icon="book" title="Formations">
                <AppTable :id="route.name" :fields="fieldsFormations" :store="products" :machine="machine"/>
            </AppTab>
            <AppTab id="gui-start-evenments" icon="calendar" title="Événements">
                <AppTable :id="route.name" :fields="fieldsEvenements" :store="products" :machine="machine"/>
            </AppTab>
            <AppTab id="gui-start-of" icon="file-invoice" title="OF">
                <AppTable :id="route.name" :fields="fieldsOF" :store="products" :machine="machine"/>
            </AppTab>
            <AppTab id="gui-start-equipments" icon="tools" title="Équipements">
                <AppTable :id="route.name" :fields="fieldsFactures" :store="products" :machine="machine"/>
            </AppTab>
            <AppTab id="gui-start-pointage" icon="bullseye" title="Pointage">
                <AppTable :id="route.name" :fields="fieldsFactures" :store="products" :machine="machine"/>
            </AppTab>
        </AppTabs>
    </div>
</template>
