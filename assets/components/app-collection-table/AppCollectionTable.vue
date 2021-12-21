<script lang="ts" setup>
    import type {TableField, TableItem} from '../../types/app-collection-table'
    import {defineProps, provide} from 'vue'
    import type {PropType} from 'vue'

    const props = defineProps({
        create: {required: false, type: Boolean},
        fields: {required: true, type: Object as PropType<TableField>},
        id: {required: true, type: String},
        items: {required: true, type: Array as PropType<TableItem[]>},
        pagination: {required: false, type: Boolean}
    })
    provide('create', props.create)
    provide('fields', props.fields)
    provide('table-id', props.id)
</script>

<template>
    <table :id="id" class="table table-bordered table-hover table-striped">
        <AppCollectionTableHeaders/>
        <AppCollectionTableItems :items="items"/>
    </table>
    <AppPagination v-if="pagination" :count="100" :current="2" class="float-end"/>
</template>
