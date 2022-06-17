<script lang="ts" setup>
    import type {TableField} from '../../../types/app-schedule-table'
    import {defineProps} from 'vue'

    defineProps<{fields: TableField[], lengthFields: number}>()
</script>

<template>
    <tr>
        <AppScheduleTableField
            v-for="(field, index) in fields"
            :key="field.name"
            :field="field"
            :index="index"
            :length-fields="lengthFields"/>
    </tr>
</template>
