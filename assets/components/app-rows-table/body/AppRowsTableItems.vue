<script lang="ts" setup>
    import {defineEmits, defineProps} from 'vue'
    import type {TableItem} from '../../../types/app-collection-table'

    const emit = defineEmits<(e: 'update', item: TableItem) => void>()
    defineProps<{items: TableItem[]}>()
    function update(item: TableItem): void {
        emit('update', item)
    }
</script>

<template>
    <tbody>
        <AppRowsTableItem v-for="(item, index) in items" :key="item.id" :index="index" :item="item" @update="update"/>
    </tbody>
</template>
