<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-manufacturing-table'
    import {defineProps} from 'vue'

    const props = defineProps<{fields: TableField[], items: TableItem[], title: string}>()
    const lengthTable = props.fields.length - 1
</script>

<template>
    <tbody>
        <AppManufacturingTableItem v-for="item in items" :key="item.id" :item="item" :fields="fields" :title="title"/>
        <tr v-if="title === 'collapse new Ofs'">
            <td :colspan="lengthTable" class="bg-white text-center text-white"/>
            <td>
                <AppBtn type="submit" variant="success">
                    Générer OFs
                </AppBtn>
            </td>
        </tr>
        <tr v-else-if="title === 'collapse ofs ToConfirm'">
            <td :colspan="lengthTable" class="bg-white text-center text-white"/>
            <td>
                <AppBtn type="submit" variant="success">
                    Confirmer OFs
                </AppBtn>
            </td>
        </tr>
    </tbody>
</template>
