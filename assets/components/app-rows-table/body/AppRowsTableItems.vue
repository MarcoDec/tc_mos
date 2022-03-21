<script lang="ts" setup>
    import {defineEmits, defineProps} from 'vue'
    import type {TableItem} from '../../../types/app-collection-table'

    const emit = defineEmits<(e: 'update', fields:TableField, item: TableItem) => void>()
    const props = defineProps<{items: TableItem[], fields:TableField }>()
    function update(item: TableItem): void {
        emit('update', item)
    }
    console.log('items', props.items);
    
</script>

<template>
    <tbody>
        <AppRowsTableItem v-for="(item, index) in items" :key="item.id" :fields="fields" :index="index" :item="item" @update="update"/>
    </tbody>
</template>
