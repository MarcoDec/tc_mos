<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-schedule-table'
    import {computed, defineProps} from 'vue'

    defineProps<{fields: TableField[], item: TableItem, lengthFields: number}>()

    const td = computed(() => 'AppScheduleTableItemField')
</script>

<template>
    <tr>
        <component :is="td" v-for="(field, index) in fields" :key="field.name" :index="index" :field="field" :item="item" :length-fields="lengthFields"/>
    </tr>
</template>
