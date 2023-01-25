<script lang="ts" setup>
    import type {TableItem} from '../../../types/app-collection-table'
    import {defineProps} from 'vue'

    defineProps<{items: TableItem[]}>()
</script>

<template>
    <tbody>
        <AppCollectionTableItem v-for="(item, index) in items" :key="item.id" :index="index" :item="item"/>
    </tbody>
</template>
