<script lang="ts" setup>
    import type {TableField} from '../../../types/app-schedule-table'
    import {computed, defineProps} from 'vue'
    const props= defineProps<{field: TableField, lengthFields: Number, index: Number}>()
    // console.log('index', props.index);
    // console.log('lengthFields', props.lengthFields);
    const leftField = computed(()=> props.index *100 + 'px')
    console.log('iiiiiiiiiiiiii', leftField.value);
</script>

<template>
    <th v-if="props.index < lengthFields" class="sticky-col-field first-col-field" >
        {{ field.label }}
    </th>
    <th v-else >
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
