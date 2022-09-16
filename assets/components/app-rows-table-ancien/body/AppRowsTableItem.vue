<script setup>
import { computed, defineProps } from "vue";
import AppRowsTableItemComponent from "./AppRowsTableItemComponent.vue";
import usePrices from "../../../stores/prices/componentSuppliers";

const props = defineProps({
  item: { required: false },
  alignFields: { required: false, type: Array },
});
console.log("alignFields---->", props.alignFields);
console.log("item ***", props.item);
const priceItems = usePrices();
console.log("store rows", priceItems.rows(props.alignFields));

/*const stateFields = [];
const states = [];
for (const part of props.item) {
  console.log("part", part);

  const tabFields = [];
  const state = { rowspan: part.rowpsan };
  for (const field of props.alignFields) {
    if (
      part.type.startsWith(field.prefix) &&
      !(Array.isArray(field.children) && field.children.length > 0)
    ) { 
      tabFields.push(field);
      state[field.name] = part[field.name];
    }
  }
  stateFields.push(tabFields);
  states.push(state);
}*/
const states = [];
const tabFields = [];
states.push(priceItems.rows);
console.log("states--->", states);
const stateFields = [];
for (const field of props.alignFields) {
  tabFields.push(field);
}
stateFields.push(tabFields);
console.log("stateFields--->", stateFields);
console.log("tabFields--->", tabFields);
</script>

<template>
  <tr>
    <AppRowsTableItemComponent
      v-for="(state, i) in states"
      :key="i"
      :i="i"
      :state="state"
      :state-fields="stateFields"
    />
  </tr>
</template>
