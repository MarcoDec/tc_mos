<script lang="ts" setup>
    import type {FormValue, FormValues} from '../../../types/bootstrap-5'
    import {defineEmits, defineProps, inject, reactive, watch} from 'vue'
    import type {TableField} from '../../../types/app-collection-table'

    const emit = defineEmits<(e: 'plus', item: FormValues) => void>()

    const props = defineProps<{value: number}>()
    const fields = inject<TableField[]>('fields', [])
    const item = reactive<FormValues>({quantite: props.value})

    function input(payload: {name: string, value: FormValue}): void {
        item[payload.name] = payload.value
    }

    function plus(): void {
        emit('plus', item)
    }
    watch(
        () => props.value,
        newValue => {
            item.quantite = newValue
        }
    )
</script>

<template>
    <tr>
        <td class="text-center">
            <AppBtn icon="plus" variant="success" @click="plus"/>
        </td>
        <AppTableItemInput
            v-for="field in fields"
            :key="field.name"
            :field="field"
            :item="item"
            @input="input"/>
    </tr>
</template>
