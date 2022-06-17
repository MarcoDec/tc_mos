<script lang="ts" setup>
    import {defineEmits, defineProps, inject} from 'vue'
    import type {FormValue} from '../../../types/bootstrap-5'
    import type {Items} from '../../../store/supplierItems/supplierItem/getters'
    import type {TableField} from '../../../types/app-collection-table'

    const emit
        = defineEmits<(e: 'update', payload: {item: Items, index: number}) => void>()
    const props = defineProps<{item: Items, index: number}>()
    const fields = inject<TableField[]>('fields', [])

    function update(payload: {name: string, value: FormValue}): void {
        emit('update', {
            index: props.index,
            item: {...props.item, [payload.name]: payload.value}
        })
    }
</script>

<template>
    <tr>
        <td class="text-center">
            {{ index + 1 }}
        </td>
        <AppTableItemInput
            v-for="field in fields"
            :key="field.name"
            :field="field"
            :item="item"
            @input="update"/>
    </tr>
</template>
