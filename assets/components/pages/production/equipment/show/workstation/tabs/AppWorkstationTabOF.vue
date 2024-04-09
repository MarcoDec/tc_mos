<script setup>
    import {AppCardableTable} from '../../../../../../bootstrap-5/app-cardable-collection-table'
    // import {useRoute} from 'vue-router'
    // const route = useRoute()
    // const workstationId = route.params.id_engine
    const fields = [
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
</script>

<template>
    <AppCardableTable
        current-page=""
        first-page=""
        form=""
        items=""
        last-page=""
        next-page=""
        pag
        previous-page=""
        :fields="fields"
        user=""/>
</template>
