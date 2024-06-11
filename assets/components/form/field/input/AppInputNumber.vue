<script setup>
    import AppInput from './AppInput.vue'

    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: 0, type: Number}
    })
    const emit = defineEmits(['update:modelValue', 'focusout'])

    function updateModelValue(v) {
        emit('update:modelValue', parseFloat(v))
    }
    function onFocusOut() {
        console.log('AppInputNumber.vue onFocusOut', props.field.name)
        emit('focusout', props.field.name)
    }
    console.log('AppInputNumber.vue', props.field.name)
</script>

<template>
    <AppInput
        :id="id"
        :disabled="disabled"
        :field="field"
        :form="form"
        :model-value="modelValue"
        @update:model-value="updateModelValue"
        @focusout="onFocusOut"/>
</template>
