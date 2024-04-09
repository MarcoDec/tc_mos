<script setup>
import { computed, defineEmits, defineProps } from "vue";

const emit = defineEmits("click", field);
const props = defineProps({
  field: { required: false},
  rowspan: { required: false, type: Number },
});

function childLength(field) {
  if (Array.isArray(field.children) && field.children.length > 0) {
    let somme = 2;
    for (const walkedField of field.children) somme += childLength(walkedField);
    return somme;
  }
  return 1;
}

const colspan = computed(() => childLength(props.field));
const safeRowSpan = computed(() =>
  Array.isArray(props.field.children) && props.field.children.length > 0
    ? 1
    : props.rowspan
);

</script>

<template>
  <th :colspan="colspan" :rowspan="safeRowSpan">
    <span class="d-flex justify-content-between">
      <span>{{ field.label }}</span>
    </span>
  </th>
</template>
