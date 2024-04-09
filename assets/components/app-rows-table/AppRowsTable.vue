<script setup>
import { computed, defineEmits, defineProps, provide } from "vue";
import AppRowsTableHeaders from "./head/AppRowsTableHeaders.vue";
import AppRowsTableBody from "./body/AppRowsTableBody.vue";

const emit = defineEmits("update", item);
const props = defineProps({
  create: { required: false, type: Boolean },
  currentPage: { default: 1, type: Number },
  fields: { required: true, type: Object },
  id: { required: true, type: String },
  // items: { required: true, type: Object },
  pagination: { required: false, type: Boolean },
});
provide("create", props.create);
provide("fields", props.fields);
provide("table-id", props.id);
function update(item) {
  emit("update", item);
}

// console.log("fields", props.fields);
</script>

<template>
  <table :id="id" class="table table-bordered table-hover table-striped">
    <AppRowsTableHeaders />
    <AppRowsTableBody :fields="fields" />
  </table>
  <slot name="btn" />
</template>
