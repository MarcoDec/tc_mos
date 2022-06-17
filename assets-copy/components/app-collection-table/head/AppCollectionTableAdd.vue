<script lang="ts" setup>
    import {defineEmits, inject} from 'vue'
    import type {TableField} from '../../../types/app-collection-table'

    const emit = defineEmits<(e: 'toggle') => void>()
    const fields = inject<TableField[]>('fields', [])

    function toggle(): void {
        emit('toggle')
    }
</script>

<template>
    <tr class="text-center">
        <td>
            <Fa icon="plus-circle"/>
        </td>
        <td>
            <AppBtn icon="filter" variant="secondary" @click="toggle"/>
            <AppBtn icon="plus" variant="success"/>
        </td>
        <td v-for="field in fields" :key="field.name">
            <AppInputGuesser v-if="field.create" :field="field" no-label/>
        </td>
    </tr>
</template>
