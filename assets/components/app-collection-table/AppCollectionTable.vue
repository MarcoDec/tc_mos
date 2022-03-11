<script lang="ts" setup>
    import type {TableField, TableItem} from '../../types/app-collection-table'
    import {computed, defineProps, provide} from 'vue'
    import type {PropType} from 'vue'

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
</script>

<template>
    <table :id="id" class="table table-bordered table-hover table-striped">
        <AppCollectionTableHeaders/>
        <AppCollectionTableItems :items="items">
            <template v-for="field in fields" #[field.name]="params">
                <slot :name="field.name" v-bind="params"/>
            </template>
        </AppCollectionTableItems>
    </table>
    <AppPagination v-if="pagination" :count="count" :current="currentPage" class="float-end"/>
</template>
