<script setup>
import { defineProps } from "vue";
import AppRowsTableItemComponent from "./AppRowsTableItemComponent.vue";

const props = defineProps({
  item: { required: false, type: Array },
  alignFields: { required: false, type: Array },
});
console.log("alignFields444", props.alignFields);

const stateFields = [];
const states = [];
for (const part of props.item) {
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
}
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
