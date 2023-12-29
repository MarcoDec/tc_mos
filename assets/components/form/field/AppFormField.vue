<script setup>
    import AppFormFieldset from './AppFormFieldset.vue'
    import AppFormGroupJS from './AppFormGroupJS'
    import {computed, defineEmits, defineProps} from 'vue'
    // import AppFormFieldset from './AppFormFieldset.vue'

    const emit = defineEmits(['update:modelValue', 'input'])
    const props = defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        modelValue: {default: null, type: Object},
        newField: {default: null, required: false, type: Object}
    })

    const componentType = computed(() => {
        switch (props.field.mode) {
            case 'tab':
                return 'AppFormTabs'
            case 'fieldset':
                return AppFormFieldset
            default:
                return AppFormGroupJS
        }
    })
    function input(value) {
        emit('update:modelValue', value)
    }
</script>

<template>
    <component
        :is="componentType"
        :field="field"
        :form="form"
        :name="field.name"
        :model-value="modelValue"
        :new-field="newField"
        :values="modelValue"
        @update:model-value="input">
        <slot/>
    </component>
</template>
