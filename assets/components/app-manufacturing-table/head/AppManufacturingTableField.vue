<script lang="ts" setup>
    import {computed, defineProps} from 'vue'
    import type {TableField} from '../../../types/app-manufacturing-table'

    const props = defineProps<{field: TableField}>()
</script>

<template>
    <th>
        {{ field.label }}
    </th>
</template>
