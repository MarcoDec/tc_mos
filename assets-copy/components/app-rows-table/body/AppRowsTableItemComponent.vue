<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-rows-table'
    import {computed, defineProps, ref} from 'vue'

    defineProps<{state: TableItem, i: number, stateFields: TableField[][]}>()

    const show = ref(true)
    const td = computed(() => (show.value ? 'AppRowsTableItemField' : 'AppRowsTableItemInput'))

    function toggle(): void {
        show.value = !show.value
    }
</script>

<template>
    <td :rowspan="state.rowspan">
        {{ state.index }}
    </td>
    <td v-if="show" :rowspan="state.rowspan" class="text-center">
        <AppBtn v-if="state.update" icon="pencil-alt" variant="primary" @click="toggle"/>
        <AppBtn v-if="state['delete']" icon="trash" variant="danger"/>
    </td>
    <td v-else :rowspan="state.rowspan" class="text-center">
        <AppBtn icon="check" variant="success"/>
        <AppBtn icon="times" variant="danger" @click="toggle"/>
    </td>
    <component :is="td" v-for="field in stateFields[i]" :key="field.name" :rowspan="state.rowspan" :field="field" :item="state"/>
</template>
