<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-schedule-table'
    import {defineProps} from 'vue'

    defineProps<{fields: TableField[], items: TableItem[], lengthFields: number}>()
</script>

<template>
    <tbody>
        <AppScheduleTableItem v-for="item in items" :key="item.id" :item="item" :fields="fields" :length-fields="lengthFields"/>
    </tbody>
</template>
