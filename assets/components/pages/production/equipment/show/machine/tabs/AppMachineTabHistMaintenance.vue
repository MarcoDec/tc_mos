<script setup>
    import {AppCardableTable} from '../../../../../../bootstrap-5/app-cardable-collection-table'
    const fields = [
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
