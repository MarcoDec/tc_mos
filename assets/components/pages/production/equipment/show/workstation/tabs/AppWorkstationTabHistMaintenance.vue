<script setup>
import {AppCardableTable} from '../../../../../../bootstrap-5/app-cardable-collection-table'
import {useRoute} from 'vue-router'
const route = useRoute()
const workstationId = route.params.id_engine
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

<style scoped>

</style>