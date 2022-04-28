<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-schedule-table'
    import {computed, defineProps} from 'vue'

    const props = defineProps<{field: TableField, item: TableItem, index: number, lengthFields: number}>()
    const value = computed(() => props.item[props.field.name])
    const left = computed(() => `${props.index * 100}px`)
</script>

<template>
    <td v-if="props.index < lengthFields" class="first-col sticky-col">
        {{ value }}
    </td>
    <td v-else>
        {{ value }}
    </td>
</template>

<style scoped>
.sticky-col {
  position: -webkit-sticky;
  position: sticky;
  background-color: white;
}
.first-col {
  width: 100px;
  min-width: 100px;
  max-width: 100px;
  left: v-bind("left");
}
</style>
