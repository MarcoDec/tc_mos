<script setup>
import { computed, defineProps } from "vue";
import AppRowsTableAddItems from "./AppRowsTableAddItems.vue";
import AppRowsTableItem from "./AppRowsTableItem.vue";

const props = defineProps({
  last: { required: true, type: Boolean },
  item: { required: true },
  fields: { required: true, type: Array },
  fieldsByLevel: { required: true, type: Array },
  alignFields: { required: true, type: Array },
  index: { required: true, type: Number },
});
const add = computed(() => typeof props.item === "number");
console.log("alignFields", props.alignFields);
console.log("fieldsByLevel", props.fieldsByLevel);
console.log("item", props.item);

</script> 

<template>
  <AppRowsTableAddItems v-if="add" :fields="fieldsByLevel[item]" />
  <AppRowsTableItem
    v-else
    :align-fields="alignFields"
    :fields="fields"
    :last="last"
    :index="index"
    :item="item"
  />
</template>
