<script lang="ts" setup>
    import type {TableField} from '../../../types/app-collection-table'
    import {inject} from 'vue'

    const fields = inject<TableField[]>('fields', [])
</script>

<template>
    <tr>
        <th/>
        <th>Actions</th>
        <th v-for="field in fields" :key="field.name" :aria-sort="field.sortDir">
            {{ field.label }}
        </th>
    </tr>
</template>
