<script lang="ts" setup>
    import type {
        TableField,
        TableItem
    } from '../../../types/app-collection-table'
    import {computed, defineProps} from 'vue'

    const props = defineProps<{field: TableField, item: TableItem}>()
    const value = computed(() => props.item[props.field.name])
</script>

<template>
    <td v-if="field.update">
        <AppInputGuesser :field="field" :model-value="value"/>
    </td>

    <AppCollectionTableItemField v-else :field="field" :item="item"/>
</template>
