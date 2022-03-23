<script lang="ts" setup>
import { computed } from "@vue/runtime-core";
import { FormField, FormValue } from "../../../types/bootstrap-5";

const emit = defineEmits<{
(e: "update:modelValue", value: FormValue) : void
(e: 'click') : void
}>();

const props =
  defineProps<{ field: FormField; form: string; modelValue?: FormValue }>();

const td = computed(() =>
  Array.isArray(props.field.children) && props.field.children.length > 0
    ? "AppFormFieldset"
    : "AppFormGroup"
);

function input(value: FormValue): void {
  emit("update:modelValue", value);
}

function click(){
  emit('click')
}
</script>

<template>
  <fieldset class="scheduler-border">
    <legend class="scheduler-border">{{ field.label }}</legend>
    <AppFormField
      v-for="field in field.children"
      :form="form"
      :key="field.name"
      :field="field"
      :model-value="modelValue"
      @update:model-value="input"
    >
    
    </AppFormField>
    
     <AppBtn class="float-end" type="submit" v-if="field.btn" @click="click">
      <Fa icon="plus" />
    </AppBtn>
  </fieldset>
</template>

<style scoped>
fieldset.scheduler-border {
  border: 1px groove #ddd !important;
  padding: 0 1.4em 1.4em 1.4em !important;
  margin: 0 0 1.5em 0 !important;
  -webkit-box-shadow: 0px 0px 0px 0px #000;
  box-shadow: 0px 0px 0px 0px #000;
}

legend.scheduler-border {
  font-size: 1.2em !important;
  font-weight: bold !important;
  text-align: left !important;
  width: auto;
  padding: 0 10px;
  border-bottom: none;
}
</style>
