<script setup>
    import AppInput from './AppInput.vue'

    defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        focusedField: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: 0, type: Number}
    })
    const emit = defineEmits(['update:modelValue', 'onFocusin'])

    function updateModelValue(v) {
        emit('update:modelValue', parseFloat(v))
    }
    function onFocusIn(data) {
        // console.log('AppInputNumber:onFocusIn', data)
        emit('onFocusin', data)
    }
</script>

<template>
    <AppInput
        :id="id"
        :disabled="disabled"
        :field="field"
        :focused-field="focusedField"
        :form="form"
        :model-value="modelValue"
        @update:model-value="updateModelValue"
        @on-focusin="onFocusIn"/>
</template>
