<script lang="ts" setup>
    import type {TableField, TableItem} from '../../types/app-collection-table'
    import {defineProps} from 'vue'
    import {useRoute} from 'vue-router'

    defineProps<{fields: TableField[], icon: string, title: string}>()
    const route = useRoute()

    const items: TableItem[] = [
        {
            cadence: 100,
            code: 'AS 01',
            delete: true,
            limite: 'Mise En Bornier',
            name: 'Mise en bornnier',
            prix: null,
            temps: null,
            type: null,
            update: true
        },
        {
            cadence: 100,
            code: 'AS 02',
            delete: true,
            limite: 'Mise En Bornier',
            name: 'Mise en bornnier',
            prix: null,
            temps: null,
            type: null,
            update: true
        }
    ]
</script>

<template>
    <h1>
        <Fa :icon="icon"/>
        {{ title }}
    </h1>
    <AppCollectionTable :id="route.name" :fields="fields" :items="items" create pagination/>
</template>
