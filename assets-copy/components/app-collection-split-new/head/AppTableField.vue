<script lang="ts" setup>
    import {defineEmits, defineProps} from 'vue'
    import type {TableField} from '../../../types/app-collection-table'

    const emit = defineEmits<(e: 'click', field: TableField) => void>()
    const props = defineProps<{field: TableField}>()

    function click(): void {
        emit('click', props.field)
    }
</script>

<template>
    <th @click="click">
        <span class="d-flex justify-content-between">
            <span>{{ field.label }}</span>
        </span>
    </th>
</template>
