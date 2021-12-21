<script lang="ts" setup>
    import {computed, defineProps} from 'vue'
    import type {FormValues} from '../../../types/bootstrap-5'
    import type {TableField} from '../../../types/app-collection-table'

    const props = defineProps<{field: TableField, item: FormValues}>()
    const value = computed(() => props.item[props.field.name])
</script>

<template>
    <td>
        {{ value }}
    </td>
</template>
