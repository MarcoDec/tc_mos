<script lang="ts" setup>
  import type {TableField, TableItem} from '../../types/app-collection-table'
  import {computed, defineProps, provide} from 'vue'
  import type {PropType} from 'vue'
  import {Items} from "../../store/supplierItems/supplierItem/getters";

  const props = defineProps({
    fields: {required: true, type: Object as PropType<TableField>},
    item: {required: true, type: Object as PropType<Items>},
  })
  provide('fields', props.fields)
</script>

<template>
  <table class="table table-bordered table-hover table-striped">
    <AppTableHeaders/>
    <AppTableItems :item="item"/>
  </table>
</template>
