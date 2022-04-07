<script lang="ts" setup>
    import {computed, defineProps} from 'vue'
    import type {TableField} from '../../../types/app-rows-table'

    const props = defineProps<{fields: TableField[]}>()
    const alignfields = computed <TableField[]>(() => props.fields
        .map(field => {
            if (Array.isArray(field.children) && field.children.length > 0){
                function nulField(index: number): TableField{
                    return {name: `${field.name}-${index}`, type: null}
                }
                return [nulField(1), nulField(2), ...field.children]
            }
            return field
        })
        .flat())
</script>

<template>
    <tr class="text-center">
        <td>
            <Fa icon="plus-circle"/>
        </td>
        <td>
            <AppBtn icon="plus" variant="success"/>
        </td>
        <td v-for="field in alignfields" :key="field.name">
            <AppInputGuesser v-if="field.type !== null" :field="field" no-label/>
        </td>
    </tr>
</template>
