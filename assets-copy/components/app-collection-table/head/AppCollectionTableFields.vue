<script lang="ts" setup>
    import {inject, ref} from 'vue'
    import type {TableField} from '../../../types/app-collection-table'

    const fields = inject<TableField[]>('fields', [])
    const asc = ref(false)
    const sort = ref('code')

    function handleSort(field: TableField): void {
        if (sort.value === field.name)
            asc.value = !asc.value
        else {
            asc.value = false
            sort.value = field.name
        }
    }
</script>

<template>
    <tr>
        <th/>
        <th>Actions</th>
        <AppCollectionTableField
            v-for="field in fields"
            :key="field.name"
            :asc="asc"
            :field="field"
            :sort="sort"
            @click="handleSort"/>
    </tr>
</template>
