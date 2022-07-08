<script setup>
    import {computed, defineEmits, defineProps} from 'vue'
    const emit = defineEmits(['update:modelValue', 'input'])
    const props = defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        modelValue: {default: null, type: Object}
    })
    const td = computed(() => {
        switch (props.field.mode) {
            case 'tab':
                return 'AppFormTabs'
            case 'fieldset':
                return 'AppFormFieldset'
            default:
                return 'AppFormGroup'
        }
    })
    function input(value) {
        emit('update:modelValue', value)
    }
</script>

<template>
    <component
        :is="td"
        :field="field"
        :form="form"
        :model-value="modelValue"
        :values="modelValue"
        @update:model-value="input">
        <slot/>
    </component>
</template>
