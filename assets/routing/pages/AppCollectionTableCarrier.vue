<script lang="ts" setup>
    import type {TableField, TableItem} from '../../types/app-collection-table'
    import {defineProps} from 'vue'
    import {useRoute} from 'vue-router'

    defineProps<{fields: TableField[], icon: string, title: string}>()
    const route = useRoute()

    const items: TableItem[] = [
        {
            adresse: null,
            codePostal: null,
            complementAdresse: null,
            delete: true,
            email: null,
            name: 'DHL',
            pays: null,
            telephone: null,
            update: true,
            ville: null
        },
        {
            adresse: null,
            codePostal: null,
            complementAdresse: null,
            delete: true,
            email: null,
            name: 'JEANTET',
            pays: null,
            telephone: null,
            update: true,
            ville: null
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
