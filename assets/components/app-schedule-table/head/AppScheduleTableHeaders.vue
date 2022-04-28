<script lang="ts" setup>
    import type {TableField} from '../../types/app-schedule-table'
    import {defineProps} from 'vue'
    defineProps<{fields: TableField[], lengthFields: number}>()
</script>

<template>
    <thead>
        <AppScheduleTableFields :fields="fields" :length-fields="lengthFields"/>
    </thead>
</template>
