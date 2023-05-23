<script setup>
  import { fieldValidator, generateLabelCols } from '../props';
import AppTabs from '../tab/AppTabs.vue';
import AppFormField from './field/AppFormField.vue';
  
  const onSubmit = (e) => {
    e.preventDefault();
    const data = new FormData(e.target);
    for (const [key, value] of Object.entries(Object.fromEntries(data))) {
      if (typeof value === 'undefined' || value === null)
        data.delete(key);
      if (typeof value === 'string') {
        data.set(key, value.trim());
        if (!props.noIgnoreNull && data.get(key).length === 0)
          data.delete(key);
      }
    }
    emit('submit', data);
  };
  
  const props = defineProps({
    disabled: { type: Boolean },
    fields: {
      required: true,
      type: Array,
      validator(value) {
        if (value.length === 0)
          return false;
        for (const field of value) {
          if (!fieldValidator(field))
            return false;
        }
        return true;
      }
    },
    id: { required: true, type: String },
    inline: { type: Boolean },
    labelCols: generateLabelCols(),
    modelValue: { default: () => ({}), type: Object },
    noContent: { type: Boolean },
    noIgnoreNull: { type: Boolean },
    submitLabel: { default: null, type: String },
    violations: { default: () => [], type: Array }
  });
  
  const attrs = {
    autocomplete: 'off',
    enctype: 'multipart/form-data',
    id: props.id,
    method: 'POST',
    novalidate: true
  };
  
  if (props.inline)
    attrs.class = 'd-inline m-0 p-0';
</script>

<template>
    <form v-bind="attrs" @submit="onSubmit">
      <template v-if="props.noContent">
        <slot v-if="typeof $slots.default === 'function'" :disabled="props.disabled" :form="props.id" :submitLabel="props.submitLabel" type="submit"></slot>
      </template>
      <template v-else v-for="field in props.fields" :key="field.name">
        <!-- <AppTabs  id="gui-start" class="gui-start-content"> -->
        <AppFormField  v-bind="{
          disabled: props.disabled,
          field,
          form: props.id,
          labelCols: props.labelCols,
          modelValue: props.modelValue[field.name],
          'onUpdate:modelValue': value => $emit('update:modelValue', { ...props.modelValue, [field.name]: value }),
          violation: props.violations.find(violation => violation.propertyPath === field.name)}">
        </AppFormField>
    <!-- </AppTabs> -->
        <template v-if="props.submitLabel !== null">
          <div class="row">
            <div class="col d-inline-flex justify-content-end">
              <slot v-if="typeof $slots.default === 'function'" :disabled="props.disabled" :form="props.id" type="submit"></slot>
              <template v-else>
                <AppBtnJS :disabled="props.disabled" :form="props.id" type="submit">{{ props.submitLabel }}</AppBtnJS>
              </template>
            </div>
          </div>
        </template>
      </template>
    </form>
  </template>
  