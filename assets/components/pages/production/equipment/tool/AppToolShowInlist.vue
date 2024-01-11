<script setup>
    //inlist
    import generateItems from '../../../../../stores/table/items'
    import {useRoute} from 'vue-router'
    import {useTableMachine} from '../../../../../machine'
    const products = generateItems('products')
    const route = useRoute()
    const machine = useTableMachine(route.name)

    const fieldsTypesMaintenances = [
        {
            create: false,
            filter: true,
            label: 'Opération ',
            name: 'opération ',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Fréquence',
            name: 'fréquence',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Période ',
            name: 'période ',
            sort: false,
            type: 'text',
            update: true
        }
    ]
    const fieldsHistoriqueMaintenances = [
        {
            create: false,
            filter: true,
            label: 'Maintenance de l équipement',
            name: 'maintenanceEquipement',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'date d éxecution',
            name: 'dateExecution',
            sort: false,
            type: 'text',
            update: true
        }
    ]

    const fieldsOF = [
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
            label: 'Ref.Prod',
            name: 'refProd',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Ind.Prod',
            name: 'indProd',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'OF',
            name: 'OF',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Opé.Responsable',
            name: 'opeResponsable',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Opé.Employés',
            name: 'opeEmployés',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Opé.Date de début',
            name: 'opeDateDébut',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Opé.Date de fin',
            name: 'opeDateFin',
            sort: false,
            type: 'text',
            update: true
        }
    ]
    const fieldsPiecesRechange = [
        {
            create: false,
            filter: true,
            label: 'Désignation ',
            name: 'désignation',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Sous Famille de Composant',
            name: 'sousFamilleComposant',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Status',
            name: 'status',
            sort: false,
            type: 'text',
            update: true
        },
        {
            create: false,
            filter: true,
            label: 'Stocks',
            name: 'stocks',
            sort: false,
            type: 'text',
            update: true
        }
    ]
</script>

<template>
    <div>
        <AppTabs id="gui-start" class="gui-start-content">
            <AppTab id="gui-start-maintenances" active icon="paint-roller" title="Types de maintenances">
                <AppTable :id="route.name" :fields="fieldsTypesMaintenances" :store="products" :machine="machine"/>
            </AppTab>
            <AppTab id="gui-start-history" icon="history" title="Historique des maintenances">
                <AppTable :id="route.name" :fields="fieldsHistoriqueMaintenances" :store="products" :machine="machine"/>
            </AppTab>
            <AppTab id="gui-start-Pieces" icon="retweet" title="Pièces de rechange">
                <AppTable :id="route.name" :fields="fieldsPiecesRechange" :store="products" :machine="machine"/>
            </AppTab>
            <AppTab id="gui-start-of" icon="file-invoice" title="OF">
                <AppTable :id="route.name" :fields="fieldsOF" :store="products" :machine="machine"/>
            </AppTab>
        </AppTabs>
    </div>
</template>
