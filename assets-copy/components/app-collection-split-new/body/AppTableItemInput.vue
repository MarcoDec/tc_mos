<script lang="ts" setup>
    import type {
        TableField,
        TableItem
        } from '../../../types/app-collection-table'
    import {computed, defineEmits, defineProps} from 'vue'

    import type {FormValue} from '../../../types/bootstrap-5'

    const props = defineProps<{field: TableField, item: TableItem}>()
    const val = computed(() => props.item[props.field.name])
    const emit
        = defineEmits<
    (e: 'input', payload: {name: string, value: FormValue}) => void
    >()

    function input(value: FormValue): void {
        emit('input', {
            name: props.field.name,
            value: parseInt(value as string) || 0
        })
    }
</script>

<template>
    <td>
        <AppInputGuesser
            :field="field"
            :model-value="val"
            @update:model-value="input"/>
    </td>
</template>
