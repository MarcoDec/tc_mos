<script lang="ts" setup>
import type { FormField, FormValue } from "../../../types/bootstrap-5";
import { computed, defineProps } from "vue";

const emit = defineEmits<(e: "update:modelValue", value: FormValue) => void>();

const props =
  defineProps<{
    field: FormField;
    fields: FormField[];
    form: string;
    modelValue?: FormValue;
  }>();


function input(value: FormValue): void {
  emit("update:modelValue", value);
}
</script>

<template>
  <AppTab
    :icon="field.icon"
    :id="field.name"
    :title="field.label"
    :active="field.active"
  >
    <AppFormField
      v-for="child in field.children"
      :key="child.name"
      :field="child"
      :form="form"
      :model-value="modelValue"
      @update:model-value="input"
    >
    
    </AppFormField>
    
  </AppTab>
</template>
