<script lang="ts" setup>
    import {computed, defineProps} from 'vue'
    import type {TableField} from '../../../types/app-schedule-table'

    const props = defineProps<{field: TableField, lengthFields: number, index: number}>()
    const leftField = computed(() => `${props.index * 100}px`)
</script>

<template>
    <th v-if="props.index < lengthFields" class="first-col-field sticky-col-field">
        {{ field.label }}
    </th>
    <th v-else>
        {{ field.label }}
    </th>
</template>

<style scoped>
.sticky-col-field {
  position: -webkit-sticky;
  position: sticky !important ;
  background-color: #eee;
}
.first-col-field {
  width: 100px ;
  min-width: 100px;
  max-width: 100px;
  left: v-bind("leftField");
  z-index: 100
}
</style>
