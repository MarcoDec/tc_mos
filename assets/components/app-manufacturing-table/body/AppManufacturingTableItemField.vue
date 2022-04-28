<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-manufacturing-table'
    import {computed, defineProps} from 'vue'

    const props = defineProps<{field: TableField, item: TableItem}>()
    const value = computed(() => props.item[props.field.name])
</script>

<template>
    <td v-if="field.type === 'date' || field.type === 'select' || field.type === 'boolean' ">
        <AppInputGuesser :field="field" :model-value="value" no-label/>
    </td>
    <td v-else>
        {{ value }}
    </td>
</template>
