<script lang="ts" setup>
    import type {TableField} from '../../../types/app-manufacturing-table'
    import {defineProps} from 'vue'
    defineProps<{field: TableField}>()
</script>

<template>
    <th>
        {{ field.label }}
    </th>
</template>
