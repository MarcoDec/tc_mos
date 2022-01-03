<script lang="ts" setup>
    import type {TableField, TableItem} from '../../types/app-collection-table'
    import {computed, defineEmits, defineProps, provide} from 'vue'
    import type {PropType} from 'vue'

    const emit = defineEmits<(e: 'update', item: TableItem) => void>()
    const props = defineProps({
        create: {required: false, type: Boolean},
        currentPage: {default: 1, type: Number},
        fields: {required: true, type: Object as PropType<TableField>},
        id: {required: true, type: String},
        items: {required: true, type: Array as PropType<TableItem[]>},
        pagination: {required: false, type: Boolean}
    })
    const count = computed(() => props.items.length)
    provide('create', props.create)
    provide('fields', props.fields)
    provide('table-id', props.id)
    function update(item: TableItem): void {
        emit('update', item)
    }
</script>

<template>
    <table :id="id" class="table table-bordered table-hover table-striped">
        <AppCollectionTableHeaders/>
        <AppCollectionTableItems :items="items" @update="update"/>
    </table>
    <AppPagination v-if="pagination" :count="count" :current="currentPage" class="float-end"/>
</template>
