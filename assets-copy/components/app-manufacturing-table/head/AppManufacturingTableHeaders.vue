<script lang="ts" setup>
    import type {TableField} from '../../types/app-manufacturing-table'
    import {defineProps} from 'vue'
    defineProps<{fields: TableField[]}>()
</script>

<template>
    <thead class="table-dark">
        >
        <AppManufacturingTableFields :fields="fields"/>
    </thead>
</template>
