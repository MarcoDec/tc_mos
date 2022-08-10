<script setup>
import { computed, defineProps } from "vue";
import AppRowsTableAddItems from "./AppRowsTableAddItems.vue";
import AppRowsTableItem from "./AppRowsTableItem.vue";

const props = defineProps({
  last: { required: true, type: Boolean },
  item: { required: true, type: Array },
  fields: { required: true, type: Array },
  fieldsByLevel:{required: true, type: Array},
  alignFields:{required: true, type: Array},
  index:{required: true, type: Number},
});
const add = computed(() => typeof props.item === "number");
</script>

<template>
  <AppRowsTableAddItems v-if="add" :fields="fieldsByLevel[item]" />
  <AppRowsTableItem
    v-else
    :last="last"
    :fields="fields"
    :align-fields="alignFields"
    :item="item"
    :index="index"
  />
</template>
