<script setup>
import { computed, defineEmits, defineProps, provide } from "vue";
import AppRowsTableHeaders from "./head/AppRowsTableHeaders.vue";
import AppRowsTableItems from "./body/AppRowsTableItems.vue";

const emit = defineEmits("update", item);
const props = defineProps({
  create: { required: false, type: Boolean },
  currentPage: { default: 1, type: Number },
  fields: { required: true, type: Object },
  id: { required: true, type: String },
  items: { required: true, type: Object },
  pagination: { required: false, type: Boolean },
});
const count = computed(() => props.items.length);
provide("create", props.create);
provide("fields", props.fields);
provide("table-id", props.id);
function update(item) {
  emit("update", item);
}
function align(field) {
  if (Array.isArray(field.children) && field.children.length > 0) {
    return [field, ...field.children.map(align).flat()];
  }
  return [field];
}

const alignFields = computed(() => props.fields.map(align).flat());
console.log("fields", props.fields);
console.log("alignFields", alignFields);
console.log("items", props.items);

</script>

<template>
  <table :id="id" class="table table-bordered table-hover table-striped">
    <AppRowsTableHeaders />
    <AppRowsTableItems
      :items="items"
      :fields="fields"
      :align-fields="alignFields"
      @update="update"
    />
  </table>
  <slot name="btn" />
  <!-- <AppPagination v-if="pagination" :count="count" :current="currentPage" class="float-end"/>-->
</template>
