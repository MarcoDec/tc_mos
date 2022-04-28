<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-manufacturing-table'
    import {computed, defineProps} from 'vue'

    defineProps<{fields: TableField[], item: TableItem}>()
</script>

<template>
    <tr>
        <component :is="field.name==='quantite' ? 'AppManufacturingTableItemQuantite' :'AppManufacturingTableItemField' " v-for="field in fields" :key="field.name" :field="field" :item="item" />
    </tr>
</template>
