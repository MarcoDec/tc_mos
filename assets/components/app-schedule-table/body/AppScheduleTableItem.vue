<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-schedule-table'
    import {computed, defineProps} from 'vue'

    defineProps<{fields: TableField[], item: TableItem, lengthFields: Number}>()

    const td = computed(() => 'AppScheduleTableItemField')
</script>

<template>
    <tr>
        <component :is="td" v-for="(field,index) in fields" :index="index" :key="field.name" :field="field" :item="item" :lengthFields="lengthFields"/>
    </tr>
</template>
