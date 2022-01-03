<script lang="ts" setup>
    import type {TableField, TableItem} from '../../types/app-collection-table'
    import {defineProps} from 'vue'
    import {useRoute} from 'vue-router'

    defineProps<{fields: TableField[], icon: string, title: string}>()
    const route = useRoute()

    const items: TableItem[] = [
        {
            delete: true,
            id: '2',
            message: 'Ventes intra-communautaire : Exonération de TVA article 262 TERI du CGI.',
            update: true
        },
        {
            delete: true,
            id: '3',
            message: 'Ventes en suspension de taxes en application de l article 275 du CGI autorisation n°1320501251R/35/1/2018 du 16/01/2018',
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
