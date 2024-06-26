<script setup>
    import {AppCardableTable} from '../../../../../../bootstrap-5/app-cardable-collection-table'
    const fields = [
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
</script>

<template>
    TODO
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
