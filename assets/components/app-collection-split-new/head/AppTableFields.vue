<script lang="ts" setup>
    import {inject} from 'vue'
    import type {TableField} from '../../../types/app-collection-table'

    const fields = inject<TableField[]>('fields', [])

</script>

<template>
    <tr>
        <th>Actions</th>
        <AppTableField
            v-for="field in fields"
            :key="field.name"
            :field="field"
            />
    </tr>
</template>
