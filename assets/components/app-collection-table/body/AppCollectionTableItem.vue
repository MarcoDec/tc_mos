<script lang="ts" setup>
    import {computed, defineProps, inject, ref} from 'vue'
    import type {FormValues} from '../../../types/bootstrap-5'
    import type {TableField} from '../../../types/app-collection-table'

    const ONE = 1

    const props = defineProps<{index: number, item: FormValues}>()
    const fields = inject<TableField[]>('fields', [])
    const formattedIndex = computed(() => props.index + ONE)
    const show = ref(true)
    const td = computed(() => (show.value ? 'AppCollectionTableItemField' : 'AppCollectionTableItemInput'))

    function toggle(): void {
        show.value = !show.value
    }
</script>

<template>
    <tr>
        <td class="text-center">
            {{ formattedIndex }}
        </td>
        <td v-if="show" class="text-center">
            <AppBtn icon="pencil-alt" variant="primary" @click="toggle"/>
            <AppBtn icon="trash" variant="danger"/>
        </td>
        <td v-else class="text-center">
            <AppBtn icon="check" variant="success"/>
            <AppBtn icon="times" variant="danger" @click="toggle"/>
        </td>
        <component :is="td" v-for="field in fields" :key="field.name" :field="field" :item="item"/>
    </tr>
</template>
