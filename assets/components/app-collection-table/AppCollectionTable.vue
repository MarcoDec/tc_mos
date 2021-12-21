<script lang="ts" setup>
    import type {TableField, TableItem} from '../../types/app-collection-table'
    import {defineProps, provide} from 'vue'

    const props = defineProps<{fields: TableField, id: string, items: TableItem[]}>()
    provide('fields', props.fields)
    provide('table-id', props.id)
</script>

<template>
    <table :id="id" class="table table-bordered table-hover table-striped">
        <AppCollectionTableHeaders/>
        <AppCollectionTableItems :items="items"/>
    </table>
    <AppPagination :count="100" :current="2" class="float-end"/>
</template>
