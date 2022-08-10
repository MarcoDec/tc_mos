<script setup>
import { defineProps } from "vue";
import AppRowsTableItemComponent from "./AppRowsTableItemComponent.vue";

const props = defineProps({
  item: { required: false, type: String },
  alignFields: { required: false, type: Array },
});
const stateFields = [];
const states = [];
for (const part of props.item) {
  const tabFields = [];
  for (const field of props.alignFields) {
    if (
      part.startsWith(field.prefix) &&
      !(Array.isArray(field.children) && field.children.length > 0)
    ) {
      tabFields.push(field);
    }
  }
  stateFields.push(tabFields);
  states.push({
    ...useNamespacedState(part, [
      ...tabFields.map(({ name }) => name),
      "delete",
      "update",
      "index",
    ]),
    rowspan: useNamespacedGetters(part, ["rowspan"]).rowspan.value,
  });
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
