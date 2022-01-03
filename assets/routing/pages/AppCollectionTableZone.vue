<script lang="ts" setup>
    import type {TableField, TableItem} from '../../types/app-collection-table'
    import {defineProps} from 'vue'
    import {useRoute} from 'vue-router'

    defineProps<{fields: TableField[], icon: string, title: string}>()
    const route = useRoute()

    const items: TableItem[] = [
        {
            delete: true,
            name: 'Tconcept',
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
