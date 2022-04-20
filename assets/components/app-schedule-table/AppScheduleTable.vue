<script lang="ts" setup>
    import type {TableField, TableItem} from '../../types/app-schedule-table'
    import {computed, defineProps, provide} from 'vue'
    import type {PropType} from 'vue'

    const props = defineProps({
        apiFields: {required: true, type: Object as PropType<TableField>},
        currentPage: {default: 1, type: Number},
        fields: {required: true, type: Object as PropType<TableField>},
        id: {required: true, type: String},
        items: {required: true, type: Array as PropType<TableItem[]>},
        pagination: {required: false, type: Boolean},
        lengthFields : {required: true, type: Number}
    })
    const count = computed(() => props.items.length)
    provide('fields', props.fields)
    provide('table-id', props.id)

    const listFields = [...props.fields]
    for (const field of props.apiFields){
        listFields.push(field)
    }

</script>

<template>
    <table :id="id" >
        <AppScheduleTableHeaders :fields="listFields" :lengthFields="lengthFields"/>
        <AppScheduleTableItems :items="items" :fields="listFields" :lengthFields="lengthFields"/>
    </table>
    <AppPagination v-if="pagination" :count="count" :current="currentPage" class="float-end"/>
</template>
