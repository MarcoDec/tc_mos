<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-collection-table'
    import type {FormValue} from '../../../types/bootstrap-5'
    import {computed, defineProps} from 'vue'

    const props = defineProps<{field: TableField, item: TableItem}>()
    const value = computed(() => props.item[props.field.name])
    const emit = defineEmits<(e:'input',payload:{name:String, value: FormValue} ) => void>()

    function input (value: FormValue): void{
      emit('input', {name:props.field.name, value:parseInt(value as string ) || 0})
    }
</script>

<template>
    <td>
        <AppInputGuesser :field="field" :model-value="value" @update:model-value="input" />
    </td>
</template>
