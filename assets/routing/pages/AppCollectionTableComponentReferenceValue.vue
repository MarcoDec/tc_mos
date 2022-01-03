<script lang="ts" setup>
    import type {TableField, TableItem} from '../../types/app-collection-table'
    import {defineProps} from 'vue'
    import {useRoute} from 'vue-router'

    defineProps<{fields: TableField[], icon: string, title: string}>()
    const route = useRoute()

    const items: TableItem[] = [
        {
            composant: 'TER-1855',
            delete: true,
            hauteur: 0.84,
            largeur: 1.3,
            obligationHauteur: true,
            obligationLargeur: false,
            obligationTraction: false,
            section: 0.35,
            toleranceHauteur: 0.05,
            toleranceTraction: null,
            tolerenceLargeur: 0.05,
            traction: 60,
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
