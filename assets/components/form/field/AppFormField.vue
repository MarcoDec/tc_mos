<script setup>
    import AppFormFieldset from './AppFormFieldset.vue'
    import AppFormGroupJS from './AppFormGroupJS'
    import {computed, defineEmits, defineProps, ref} from 'vue'
    import AppFormFlexWrapper from './AppFormFlexWrapper.vue'

    const emit = defineEmits(['update:modelValue', 'input'])
    const props = defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        modelValue: {default: null, type: [Object, String]},
        newField: {default: null, required: false, type: Object},
        disabled: {default: false, type: Boolean}
    })
    // console.log('AppFormField props', props)
    const localData = ref(props.modelValue)
    const componentType = computed(() => {
        if (props.field.mode === 'wrap') return AppFormFlexWrapper
        switch (props.field.mode) {
            case 'tab':
                return 'AppFormTabs'
            case 'fieldset':
                return AppFormFieldset
            default:
                return AppFormGroupJS
        }
    })
    const isDisabled = computed(() => props.disabled || props.field.readOnly)
    // console.log('AppFormField props', props, props.field.mode, componentType.value)
    function input(value) {
        localData.value = value
        emit('update:modelValue', value)
    }
</script>

<template>
    <component
        :is="componentType"
        :disabled="isDisabled"
        :field="field"
        :form="form"
        :name="field.name"
        :model-value="localData"
        :new-field="newField"
        :values="modelValue"
        @update:model-value="input">
        <slot/>
    </component>
</template>
