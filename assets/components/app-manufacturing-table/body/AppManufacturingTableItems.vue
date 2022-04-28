<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-manufacturing-table'
    import {defineProps} from 'vue'

    const props= defineProps<{fields: TableField[], items: TableItem[]}>()
    const lengthTable = props.fields.length -1
</script>

<template>
    <tbody>
        <AppManufacturingTableItem v-for="item in items" :key="item.id" :item="item" :fields="fields" />
        <tr>
            <td :colspan="lengthTable" class="bg-white text-white text-center"></td>
            <td>
                <AppBtn type="submit" variant="success">
                    Générer OFs
                </AppBtn>
            </td>
        
        </tr>
    </tbody>
</template>
