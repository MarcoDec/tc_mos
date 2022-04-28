<script lang="ts" setup>
    import type {TableField} from '../../../types/app-manufacturing-table'
    import {defineProps} from 'vue'

    defineProps<{fields: TableField[]}>()
</script>

<template>
    <tr>
        <AppManufacturingTableField
            v-for="field in fields"
            :key="field.name"
            :field="field"
        />
    </tr>
</template>
