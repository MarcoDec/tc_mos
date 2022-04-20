<script lang="ts" setup>
    import type {TableField} from '../../types/app-schedule-table'
    import {defineProps} from 'vue'
    defineProps<{fields: TableField[], lengthFields: Number }>()
</script>

<template>
    <thead>
        <AppScheduleTableFields :fields="fields" :lengthFields="lengthFields"/>
    </thead>
</template>
