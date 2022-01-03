<script lang="ts" setup>
    import type {TableField, TableItem} from '../../types/app-collection-table'
    import {defineProps} from 'vue'
    import {useRoute} from 'vue-router'

    defineProps<{fields: TableField[], icon: string, title: string}>()
    const route = useRoute()

    const items: TableItem[] = [
        {
            adresse: 'RUE IBN KHALDOUN',
            codePostal: 'RUE IBN KHALDOUN',
            complementAdresse: null,
            delete: true,
            email: null,
            name: 'Certificat',
            pays: 'Tunisie',
            prenom: 'RAWAA',
            telephone: 'Tunisie (+216) 55134104',
            update: true,
            ville: 'KALAA KEBIRA'
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
