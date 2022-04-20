<script lang="ts" setup>
    import type {TableField} from '../../../types/app-schedule-table'
    import {defineProps} from 'vue'

    defineProps<{fields: TableField[], lengthFields: Number}>()
</script>

<template>
    <tr>
        <AppScheduleTableField
            v-for="(field,index) in fields"
            :key="field.name"
            :field="field"
            :index="index"
            :lengthFields="lengthFields"/>
    </tr>
</template>
