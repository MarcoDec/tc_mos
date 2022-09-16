<script setup>
import { computed, defineEmits, defineProps } from "vue";

const emit = defineEmits("click", field);
const props = defineProps({
  asc: { required: false },
  field: { required: false},
  sort: { required: false, type: String },
  rowspan: { required: false, type: Number },
});

const down = computed(() => ({
  "text-secondary": props.field.name !== props.sort || props.asc,
}));
const up = computed(() => ({
  "text-secondary": props.field.name !== props.sort || !props.asc,
}));

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

function click() {
  emit("click", props.field);
}
</script>

<template>
  <th :colspan="colspan" :rowspan="safeRowSpan" @click="click">
    <span class="d-flex justify-content-between">
      <span>{{ field.label }}</span>
      <span v-if="field.sort" class="d-flex flex-column">
        <Fa :class="down" icon="caret-up" />
        <Fa :class="up" icon="caret-down" />
      </span>
    </span>
  </th>
</template>
