<script setup>
    import {defineEmits, defineProps} from 'vue'
    import AppFormField from './AppFormField.vue';
    const emit = defineEmits(['update:modelValue', 'input'])
    const props = defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        modelValue: {default: null, type: Object}
    })

    function input(value) {
        emit('update:modelValue', value)
    }
</script>

<template>
    <template v-if="field.mode === 'tab'">
        <AppTabs  id="gui-start" class="gui-start-content">
            <AppTab
                :id="field.name"
                :icon="field.icon"
                :title="field.label"
                :active="field.active"
                :tabs="`gui-start-${field.name}`">
                <slot>
                    <AppFormField
                        v-for="child in field.children"
                        :key="child"
                        :field="child"
                        :form="form"
                        :model-value="modelValue"
                        @update:model-value="input"/>
                </slot>
            </AppTab>
        </AppTabs>
    </template>
    <template v-else-if="field.mode === 'fieldset'" >
        <fieldset class="scheduler-border">
            <legend class="scheduler-border">
                {{ field.label }}
            </legend>
            <AppFormField
                v-for="child in field.children"
                :key="child"
                :form="form"
                :field="child"
                :model-value="modelValue"
                @update:model-value="input"/>
            <slot/>
        </fieldset>
    </template>
    <template v-else>
        <AppFormGroupJS
        :field="field"
        :form="form"
        :model-value="modelValue"
        :values="modelValue"
        @update:model-value="input">
        <slot/>
        </AppFormGroupJS>
    </template>
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
