<script lang="ts" setup>
    import {computed, defineEmits, defineProps} from 'vue'
    import type {TableField} from '../../../types/app-collection-table'

    const emit = defineEmits<(e: 'click', field: TableField) => void>()
    const props = defineProps<{asc: boolean, field: TableField, sort: string}>()
    const down = computed(() => ({'text-secondary': props.field.name !== props.sort || props.asc}))
    const up = computed(() => ({'text-secondary': props.field.name !== props.sort || !props.asc}))

    function click(): void {
        emit('click', props.field)
    }
</script>

<template>
    <th :aria-sort="field.sortDir" @click="click">
        <span class="d-flex justify-content-between">
            <span>{{ field.label }}</span>
            <span class="d-flex flex-column">
                <Fa :class="down" icon="caret-up"/>
                <Fa :class="up" icon="caret-down"/>
            </span>
        </span>
    </th>
</template>
