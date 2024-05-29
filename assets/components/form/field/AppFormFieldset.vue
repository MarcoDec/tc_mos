<script setup>
    import AppFormField from './AppFormField.vue'

    const emit = defineEmits(['update:modelValue', 'click'])
    const props = defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        modelValue: {default: null}
    })
    // console.log('AppFormFieldset props', props)
    function input(value) {
        emit('update:modelValue', value)
    }
</script>

<template>
    <fieldset class="scheduler-border">
        <legend class="scheduler-border">
            {{ field.label }}
        </legend>
        <AppFormField
            v-for="child in field.children"
            :key="child"
            :form="form"
            :field="child"
            :name="child.name"
            :model-value="modelValue"
            :values="modelValue"
            @update:model-value="input"/>
    </fieldset>
</template>

<style scoped>
fieldset.scheduler-border {
  border: 1px groove #ddd !important;
  padding: 0 1.4em 1.4em 1.4em !important;
  //margin: 0 0 1.5em 0 !important;
  -webkit-box-shadow: 0px 0px 0px 0px #000;
  box-shadow: 0px 0px 0px 0px #000;
    background-color: rgba(169, 169, 169, 0.46);
    border-radius: 10px;
    margin-bottom: 10px;
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
