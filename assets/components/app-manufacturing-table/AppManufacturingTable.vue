<script lang="ts" setup>
    import type {TableField, TableItem} from '../../types/app-manufacturing-table'
    import {computed, defineProps, provide} from 'vue'
    import type {PropType} from 'vue'

    const props = defineProps({
        currentPage: {default: 1, type: Number},
        fields: {required: true, type: Object as PropType<TableField>},
        id: {required: true, type: String},
        items: {required: true, type: Array as PropType<TableItem[]>},
        pagination: {required: false, type: Boolean},
        title: {required: false, type: String}
    })
    const count = computed(() => props.items.length)
    provide('fields', props.fields)
    provide('table-id', props.id)
    provide('table-id', props.id)
</script>

<template>
    <table :id="id" class="table table-bordered table-hover table-striped">
        <AppManufacturingTableHeaders :fields="fields" />
        <AppManufacturingTableItems :items="items" :fields="fields" :title="title" />
    </table>
    <AppPagination v-if="pagination" :count="count" :current="currentPage" class="float-end"/>
</template>
