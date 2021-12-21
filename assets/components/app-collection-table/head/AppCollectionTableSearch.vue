<script lang="ts" setup>
    import {computed, defineEmits, inject} from 'vue'
    import type {TableField} from '../../../types/app-collection-table'

    const emit = defineEmits<(e: 'toggle') => void>()
    const fields = inject<TableField[]>('fields', [])
    const tableId = inject<string>('table-id', 'table')
    const searchFields = computed<TableField[]>(() => fields.map((field: Readonly<TableField>): TableField => ({
        ...field,
        id: `${tableId}-search-${field.name}`,
        type: field.type === 'boolean' ? 'search-boolean' : field.type
    })))

    function toggle(): void {
        emit('toggle')
    }
</script>

<template>
    <tr class="text-center">
        <td>
            <Fa icon="filter"/>
        </td>
        <td>
            <AppBtn icon="plus-circle" variant="success" @click="toggle"/>
            <AppBtn icon="search" variant="secondary"/>
            <AppBtn icon="times" variant="danger"/>
        </td>
        <td v-for="field in searchFields" :key="field.name">
            <AppInputGuesser v-if="field.filter" :field="field"/>
        </td>
    </tr>
</template>
