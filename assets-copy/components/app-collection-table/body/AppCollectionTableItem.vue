<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-collection-table'
    import {computed, defineEmits, defineProps, inject, ref} from 'vue'

    const ONE = 1

    const props = defineProps<{index: number, item: TableItem}>()
    const fields = inject<TableField[]>('fields', [])
    const formattedIndex = computed(() => props.index + ONE)
    const show = ref(true)
    const td = computed(() => (show.value ? 'AppCollectionTableItemField' : 'AppCollectionTableItemInput'))

    const emit = defineEmits<(e: 'update', item: TableItem) => void>()
    function toggle(): void {
        show.value = !show.value
    }
    function update(): void {
        emit('update', props.item)
    }
</script>

<template>
    <tr>
        <td class="text-center">
            {{ formattedIndex }}
        </td>
        <td v-if="show" class="text-center">
            <AppBtn v-if="item.update" icon="pencil-alt" variant="primary" @click="toggle"/>
            <AppBtn v-if="item.update2" icon="eye" variant="secondary" @click="update"/>
            <AppBtn v-if="item['delete']" icon="trash" variant="danger"/>
        </td>
        <td v-else class="text-center">
            <AppBtn icon="check" variant="success"/>
            <AppBtn icon="times" variant="danger" @click="toggle"/>
        </td>
        <component :is="td" v-for="field in fields" :key="field.name" :field="field" :item="item"/>
    </tr>
</template>
