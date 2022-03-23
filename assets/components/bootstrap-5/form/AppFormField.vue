<script lang="ts" setup>
import { computed } from "@vue/runtime-core";
import { FormField, FormValue } from "../../../types/bootstrap-5";

const emit = defineEmits<{
  (e: "update:modelValue", value: FormValue): void;
  (e: "input", payload: { value: FormValue; name: string }): void;
}>();
const props =
  defineProps<{ field: FormField; form: string; modelValue?: FormValue }>();

const td = computed(() =>
  Array.isArray(props.field.children) && props.field.children.length > 0
    ? props.field.tab
      ? "AppFormTabs"
      : "AppFormFieldset"
    : "AppFormGroup"
);
function input(value: FormValue): void {
  emit("update:modelValue", value);
  emit("input", { name: props.field.name, value });
  console.log("select2", value);
}
</script>

<template>
  <component
    :is="td"
    :field="field"
    :form="form"
    :model-value="modelValue"
    @update:model-value="input"
  />
</template>
