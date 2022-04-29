<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-manufacturing-table'
    import {computed, defineProps} from 'vue'

    defineProps<{fields: TableField[], item: TableItem, title: string}>()
</script>

<template>
    <tr>
        <component :is="field.name==='quantite'&& title === 'collapse new Ofs' ? 'AppManufacturingTableItemQuantite' :'AppManufacturingTableItemField' " v-for="field in fields" :key="field.name" :field="field" :item="item" :title="title"/>
    </tr>
</template>
