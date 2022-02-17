<script lang="ts" setup>
    import {TableField} from "../../../types/app-collection-table";
    import type {TableItem} from '../../../types/app-collection-table'
    import {defineProps, inject} from 'vue'

    defineProps<{items: TableItem[]}>()
    const fields = inject<TableField[]>('fields', [])

</script>

<template>
    <tbody>
    <AppCollectionTableItem v-for="(item, index) in items" :key="item.id" :index="index" :item="item">
      <template v-for="field in fields" v-slot:[field.name]="params">
        <slot :name="field.name" v-bind="params"/>
      </template>
    </AppCollectionTableItem>
    </tbody>
</template>
