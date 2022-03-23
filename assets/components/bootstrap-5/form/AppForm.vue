<script lang="ts" setup>
import type {
  FormField,
  FormValue,
  FormValues,
} from "../../../types/bootstrap-5";
import { computed, defineEmits, defineProps, provide, withDefaults } from "vue";
import clone from "clone";

const emit = defineEmits<{
  (e: "update:modelValue", values: Readonly<FormValues>): void;
  (e: "submit"): void;
}>();
const props = withDefaults(
  defineProps<{
    countryField?: string | null;
    fields: FormField[];
    id: string;
    modelValue?: FormValues;
  }>(),
  { countryField: null, modelValue: () => ({}) }
);
const country = computed(() =>
  props.countryField !== null ? props.modelValue[props.countryField] : null
);

const tabs = computed(() => {
  for (const field of props.fields) if (field.tab) return true;
  return false;
});


provide("country", country);

function input(value: Readonly<{ value: FormValue; name: string }>): void {
  const cloned = clone(props.modelValue);
  cloned[value.name] = value.value;
  emit("update:modelValue", cloned);
  console.log("select1bbb", cloned);
}
</script>

<template>
  <form :id="id" autocomplete="off" @submit.prevent="emit('submit')">
    <AppTabs id="gui-start" class="gui-start-content" v-if="tabs">
      <AppFormField
        v-for="field in fields"
        :key="field.name"
        :field="field"
        :form="id"
        :model-value="modelValue[field.name]"
        @input="input"
      />
     <slot/>
    </AppTabs>
    <template v-else>
      <AppFormField
        v-for="field in fields"
        :key="field.name"
        :field="field"
        :form="id"
        :model-value="modelValue[field.name]"
        @input="input"
      />
    </template>
    <div class="float-start">
      <slot name="start" />
    </div>
    <div class="float-end">
      <slot />
    </div>
  </form>
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
